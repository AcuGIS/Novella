<?php

declare(strict_types=1);

namespace GeoLibre\Model;

use Doctrine\DBAL\Connection;

class Document extends AbstractModel
{
    public function __construct(Connection $db)
    {
        parent::__construct($db);
        $this->table = 'documents';
    }

    public function findByUserId(int $userId): array
    {
        $qb = $this->createQueryBuilder();
        return $qb->select('*')
            ->from($this->table)
            ->where('user_id = :user_id')
            ->setParameter('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function findById(int $id): ?array
    {
        $qb = $this->createQueryBuilder();
        $result = $qb->select('*')
            ->from($this->table)
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();

        return $result ?: null;
    }

    public function create(array $data): int
    {
        $qb = $this->createQueryBuilder();
        $qb->insert($this->table)
            ->values([
                'title' => ':title',
                'description' => ':description',
                'file_path' => ':file_path',
                'file_type' => ':file_type',
                'file_size' => ':file_size',
                'is_public' => ':is_public',
                'user_id' => ':user_id'
            ])
            ->setParameters([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'file_path' => $data['file_path'],
                'file_type' => $data['file_type'],
                'file_size' => $data['file_size'],
                'is_public' => $data['is_public'] ?? false,
                'user_id' => $data['user_id']
            ]);

        $qb->executeQuery();
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $qb = $this->createQueryBuilder();
        $qb->update($this->table)
            ->set('title', ':title')
            ->set('description', ':description')
            ->set('is_public', ':is_public')
            ->where('id = :id')
            ->setParameters([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'is_public' => $data['is_public'] ? 1 : 0,
                'id' => $id
            ]);

        return $qb->executeQuery()->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $qb = $this->createQueryBuilder();
        $qb->delete($this->table)
            ->where('id = :id')
            ->setParameter('id', $id);

        return $qb->executeQuery()->rowCount() > 0;
    }

    public function togglePublic(int $id): bool
    {
        $qb = $this->createQueryBuilder();
        $qb->update($this->table)
            ->set('is_public', 'NOT is_public')
            ->where('id = :id')
            ->setParameter('id', $id);

        return $qb->executeQuery()->rowCount() > 0;
    }

    public function getPublicDocuments(): array
    {
        $qb = $this->createQueryBuilder();
        return $qb->select('d.*', 'u.username')
            ->from($this->table, 'd')
            ->leftJoin('d', 'users', 'u', 'd.user_id = u.id')
            ->where('d.is_public = true')
            ->orderBy('d.created_at', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();
    }
} 