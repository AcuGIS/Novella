<?php

declare(strict_types=1);

namespace GeoLibre\Model;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

abstract class AbstractModel
{
    protected Connection $db;
    protected string $table;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->db->createQueryBuilder();
    }

    public function find(int $id): ?array
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

    public function findAll(array $criteria = [], array $orderBy = [], ?int $limit = null, ?int $offset = null): array
    {
        $qb = $this->createQueryBuilder();
        $qb->select('*')->from($this->table);

        foreach ($criteria as $field => $value) {
            $qb->andWhere("$field = :$field")
               ->setParameter($field, $value);
        }

        foreach ($orderBy as $field => $direction) {
            $qb->addOrderBy($field, $direction);
        }

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }

        return $qb->executeQuery()->fetchAllAssociative();
    }

    public function create(array $data): int
    {
        $this->db->insert($this->table, $data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        return $this->db->update($this->table, $data, ['id' => $id]) > 0;
    }

    public function delete(int $id): bool
    {
        return $this->db->delete($this->table, ['id' => $id]) > 0;
    }
} 