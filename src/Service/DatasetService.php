<?php

namespace App\Service;

use App\Model\Dataset;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DatasetService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAll(int $page = 1, int $limit = 10): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('d')
            ->from(Dataset::class, 'd')
            ->leftJoin('d.oaiRecords', 'r')
            ->leftJoin('r.harvestSource', 'h')
            ->where('h.id IS NULL OR (r.deleted = false AND h.id IS NOT NULL)')
            ->orderBy('d.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($query);
        
        return [
            'items' => iterator_to_array($paginator),
            'total' => count($paginator),
            'page' => $page,
            'limit' => $limit
        ];
    }

    public function findById(int $id): ?Dataset
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('d')
            ->from(Dataset::class, 'd')
            ->leftJoin('d.oaiRecords', 'r')
            ->leftJoin('r.harvestSource', 'h')
            ->where('d.id = :id')
            ->andWhere('h.id IS NULL OR (r.deleted = false AND h.id IS NOT NULL)')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function create(array $data): Dataset
    {
        $dataset = new Dataset();
        $this->updateDatasetFromData($dataset, $data);
        
        $this->entityManager->persist($dataset);
        $this->entityManager->flush();
        
        return $dataset;
    }

    public function update(int $id, array $data): Dataset
    {
        $dataset = $this->findById($id);
        
        if (!$dataset) {
            throw new \Exception('Dataset not found');
        }
        
        $this->updateDatasetFromData($dataset, $data);
        
        $this->entityManager->flush();
        
        return $dataset;
    }

    public function delete(int $id): void
    {
        $dataset = $this->findById($id);
        
        if (!$dataset) {
            throw new \Exception('Dataset not found');
        }
        
        $this->entityManager->remove($dataset);
        $this->entityManager->flush();
    }

    private function updateDatasetFromData(Dataset $dataset, array $data): void
    {
        if (isset($data['title'])) {
            $dataset->setTitle($data['title']);
        }
        
        if (isset($data['description'])) {
            $dataset->setDescription($data['description']);
        }
        
        if (isset($data['keywords'])) {
            $dataset->setKeywords($data['keywords']);
        }
        
        if (isset($data['contact'])) {
            $dataset->setContact($data['contact']);
        }
        
        if (isset($data['metadata'])) {
            $dataset->setMetadata($data['metadata']);
        }
        
        $dataset->setUpdatedAt(new \DateTime());
    }

    public function search(string $query, array $params, int $page = 1, int $limit = 10): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('d')
            ->from(Dataset::class, 'd')
            ->leftJoin('d.oaiRecords', 'r')
            ->leftJoin('r.harvestSource', 'h')
            ->where('h.id IS NULL OR (r.deleted = false AND h.id IS NOT NULL)');

        // Add text search conditions
        if (!empty($query)) {
            $qb->andWhere('d.title LIKE :query OR d.description LIKE :query')
               ->setParameter('query', '%' . $query . '%');
        }

        // Add keyword filter
        if (!empty($params['keywords'])) {
            $qb->andWhere('JSON_CONTAINS(d.keywords, :keywords) = 1')
               ->setParameter('keywords', json_encode($params['keywords']));
        }

        // Add spatial extent filter
        if (!empty($params['spatial_extent'])) {
            $qb->andWhere('ST_Intersects(d.spatial_extent, ST_GeomFromText(:spatial_extent, 4326)) = 1')
               ->setParameter('spatial_extent', $params['spatial_extent']);
        }

        // Add temporal extent filter
        if (!empty($params['temporal_start']) && !empty($params['temporal_end'])) {
            $qb->andWhere('d.temporal_extent && tstzrange(:temporal_start, :temporal_end, \'[]\') = 1')
               ->setParameter('temporal_start', $params['temporal_start'])
               ->setParameter('temporal_end', $params['temporal_end']);
        }

        // Add metadata standard filter
        if (!empty($params['metadata_standard'])) {
            $qb->andWhere('d.metadata_standard = :metadata_standard')
               ->setParameter('metadata_standard', $params['metadata_standard']);
        }

        // Add sorting
        $sortBy = $params['sort_by'] ?? 'createdAt';
        $sortOrder = $params['sort_order'] ?? 'DESC';
        $qb->orderBy('d.' . $sortBy, $sortOrder);

        // Add pagination
        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        $paginator = new Paginator($qb);
        
        return [
            'items' => iterator_to_array($paginator),
            'total' => count($paginator),
            'page' => $page,
            'limit' => $limit
        ];
    }

    public function suggest(string $query, int $limit = 5): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('DISTINCT d.title')
            ->from(Dataset::class, 'd')
            ->leftJoin('d.oaiRecords', 'r')
            ->leftJoin('r.harvestSource', 'h')
            ->where('h.id IS NULL OR (r.deleted = false AND h.id IS NOT NULL)')
            ->andWhere('d.title LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function getFacets(string $query): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('d.metadata_standard, COUNT(d.id) as count')
            ->from(Dataset::class, 'd')
            ->leftJoin('d.oaiRecords', 'r')
            ->leftJoin('r.harvestSource', 'h')
            ->where('h.id IS NULL OR (r.deleted = false AND h.id IS NOT NULL)')
            ->groupBy('d.metadata_standard');

        if (!empty($query)) {
            $qb->andWhere('d.title LIKE :query OR d.description LIKE :query')
               ->setParameter('query', '%' . $query . '%');
        }

        $metadataStandards = $qb->getQuery()->getResult();

        // Get keyword facets
        $qb = $this->entityManager->createQueryBuilder()
            ->select('d.keywords')
            ->from(Dataset::class, 'd')
            ->leftJoin('d.oaiRecords', 'r')
            ->leftJoin('r.harvestSource', 'h')
            ->where('h.id IS NULL OR (r.deleted = false AND h.id IS NOT NULL)');

        if (!empty($query)) {
            $qb->andWhere('d.title LIKE :query OR d.description LIKE :query')
               ->setParameter('query', '%' . $query . '%');
        }

        $keywords = [];
        foreach ($qb->getQuery()->getResult() as $row) {
            foreach ($row['keywords'] as $keyword) {
                $keywords[$keyword] = ($keywords[$keyword] ?? 0) + 1;
            }
        }

        return [
            'metadata_standards' => $metadataStandards,
            'keywords' => $keywords
        ];
    }
} 