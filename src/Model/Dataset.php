<?php

declare(strict_types=1);

namespace GeoLibre\Model;

use Doctrine\DBAL\Connection;

class Dataset extends AbstractModel
{
    public function __construct(Connection $db)
    {
        parent::__construct($db);
        $this->table = 'datasets';
    }

    public function findByTitle(string $title): ?array
    {
        $qb = $this->createQueryBuilder();
        $result = $qb->select('*')
            ->from($this->table)
            ->where('title = :title')
            ->setParameter('title', $title)
            ->executeQuery()
            ->fetchAssociative();

        return $result ?: null;
    }

    public function findBySpatialExtent(float $minX, float $minY, float $maxX, float $maxY): array
    {
        $qb = $this->createQueryBuilder();
        return $qb->select('*')
            ->from($this->table)
            ->where('ST_Intersects(spatial_extent, ST_MakeEnvelope(:minX, :minY, :maxX, :maxY, 4326))')
            ->setParameter('minX', $minX)
            ->setParameter('minY', $minY)
            ->setParameter('maxX', $maxX)
            ->setParameter('maxY', $maxY)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function findByTemporalExtent(\DateTime $start, \DateTime $end): array
    {
        $qb = $this->createQueryBuilder();
        return $qb->select('*')
            ->from($this->table)
            ->where('temporal_extent && tstzrange(:start, :end, \'[]\')')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function createDataset(array $data): int
    {
        // Set timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Ensure spatial_extent is properly formatted if provided
        if (isset($data['spatial_extent'])) {
            $data['spatial_extent'] = "ST_GeomFromGeoJSON('" . json_encode($data['spatial_extent']) . "')";
        }

        // Ensure temporal_extent is properly formatted if provided
        if (isset($data['temporal_extent'])) {
            $data['temporal_extent'] = "tstzrange('" . 
                $data['temporal_extent']['start'] . "', '" . 
                $data['temporal_extent']['end'] . "', '[]')";
        }

        return $this->create($data);
    }

    public function updateDataset(int $id, array $data): bool
    {
        // Update timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Handle spatial_extent update if provided
        if (isset($data['spatial_extent'])) {
            $data['spatial_extent'] = "ST_GeomFromGeoJSON('" . json_encode($data['spatial_extent']) . "')";
        }

        // Handle temporal_extent update if provided
        if (isset($data['temporal_extent'])) {
            $data['temporal_extent'] = "tstzrange('" . 
                $data['temporal_extent']['start'] . "', '" . 
                $data['temporal_extent']['end'] . "', '[]')";
        }

        return $this->update($id, $data);
    }

    public function search(string $query, array $filters = []): array
    {
        $qb = $this->createQueryBuilder();
        $qb->select('d.*', 'h.name as harvest_source_name', 'm.quality_score')
            ->from('datasets', 'd')
            ->leftJoin('d', 'oai_records', 'r', 'd.id = r.dataset_id')
            ->leftJoin('r', 'harvest_sources', 'h', 'r.harvest_source_id = h.id')
            ->leftJoin('d', 'metadata', 'm', 'd.id = m.dataset_id')
            ->where('(d.is_public = true AND r.id IS NULL) OR (r.deleted = false AND h.id IS NOT NULL AND h.schedule IS NOT NULL)');

        if (!empty($query)) {
            $qb->andWhere('d.title ILIKE :query OR d.description ILIKE :query')
                ->setParameter('query', '%' . $query . '%');
        }

        if (!empty($filters['harvest_source'])) {
            $qb->andWhere('h.id = :harvest_source')
                ->setParameter('harvest_source', $filters['harvest_source']);
        }

        if (!empty($filters['date_from'])) {
            $qb->andWhere('d.created_at >= :date_from')
                ->setParameter('date_from', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $qb->andWhere('d.created_at <= :date_to')
                ->setParameter('date_to', $filters['date_to']);
        }

        if (isset($filters['quality_score'])) {
            $qb->andWhere('m.quality_score >= :quality_score')
                ->setParameter('quality_score', (int)$filters['quality_score']);
        }

        if (!empty($filters['status'])) {
            $qb->andWhere('d.status = :status')
                ->setParameter('status', $filters['status']);
        }

        return $qb->orderBy('d.created_at', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function getWithMetadata(int $id): ?array
    {
        $qb = $this->createQueryBuilder();
        $result = $qb->select('d.*', 'm.metadata_xml', 'm.metadata_standard', 'm.metadata_version', 'm.quality_score', 'hs.name as harvest_source_name')
            ->from($this->table, 'd')
            ->leftJoin('d', 'metadata', 'm', 'd.id = m.dataset_id')
            ->leftJoin('d', 'oai_records', 'r', 'd.id = r.dataset_id')
            ->leftJoin('r', 'harvest_sources', 'hs', 'r.harvest_source_id = hs.id')
            ->where('d.id = :id')
            ->andWhere('(hs.id IS NULL OR (r.deleted = false AND hs.id IS NOT NULL))')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();

        if ($result) {
            // Set is_harvested flag based on harvest source
            $result['is_harvested'] = !empty($result['harvest_source_name']);

            // Get keywords for the dataset
            $keywordsQb = $this->createQueryBuilder();
            $keywords = $keywordsQb->select('keyword')
                ->from('keywords')
                ->where('dataset_id = :id')
                ->setParameter('id', $id)
                ->executeQuery()
                ->fetchFirstColumn();
            
            $result['keywords'] = $keywords;
        }

        return $result ?: null;
    }

    public function getAll(array $filters = []): array
    {
        $qb = $this->createQueryBuilder();
        $qb->select('d.*', 'h.name as harvest_source_name', 'm.quality_score')
            ->from('datasets', 'd')
            ->leftJoin('d', 'oai_records', 'r', 'd.id = r.dataset_id')
            ->leftJoin('r', 'harvest_sources', 'h', 'r.harvest_source_id = h.id')
            ->leftJoin('d', 'metadata', 'm', 'd.id = m.dataset_id')
            ->where('(d.is_public = true AND r.id IS NULL) OR (r.deleted = false AND h.id IS NOT NULL AND h.schedule IS NOT NULL)');

        if (isset($filters['quality_score'])) {
            $qb->andWhere('m.quality_score >= :quality_score')
                ->setParameter('quality_score', (int)$filters['quality_score']);
        }

        if (!empty($filters['metadata_standard'])) {
            $qb->andWhere('m.metadata_standard = :metadata_standard')
                ->setParameter('metadata_standard', $filters['metadata_standard']);
        }

        if (!empty($filters['status'])) {
            $qb->andWhere('d.status = :status')
                ->setParameter('status', $filters['status']);
        }

        return $qb->orderBy('d.created_at', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function all()
    {
        return $this->findAll();
    }
} 