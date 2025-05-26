<?php

declare(strict_types=1);

namespace GeoLibre\Model;

use Doctrine\DBAL\Connection;
use DateTime;

class HarvestSource extends AbstractModel
{
    public function __construct(Connection $db)
    {
        parent::__construct($db);
        $this->table = 'harvest_sources';
    }

    public function createSource(array $data): int
    {
        $data['created_at'] = (new DateTime())->format('Y-m-d H:i:s');
        $data['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');
        return $this->create($data);
    }

    public function updateSource(int $id, array $data): bool
    {
        $data['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');
        return $this->update($id, $data);
    }

    public function getAllSources(): array
    {
        return $this->findAll();
    }

    public function getSourceById(int $id): ?array
    {
        return $this->find($id);
    }

    public function updateLastHarvest(int $id, DateTime $date): bool
    {
        return $this->update($id, [
            'last_harvest' => $date->format('Y-m-d H:i:s'),
            'updated_at' => (new DateTime())->format('Y-m-d H:i:s')
        ]);
    }

    public function getSourcesBySchedule(string $schedule): array
    {
        $qb = $this->createQueryBuilder();
        return $qb->select('*')
            ->from($this->table)
            ->where('schedule = :schedule')
            ->setParameter('schedule', $schedule)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function getSourcesDueForHarvest(): array
    {
        $qb = $this->createQueryBuilder();
        return $qb->select('*')
            ->from($this->table)
            ->where('last_harvest IS NULL')
            ->orWhere('schedule = :daily AND last_harvest < :yesterday')
            ->orWhere('schedule = :weekly AND last_harvest < :last_week')
            ->orWhere('schedule = :monthly AND last_harvest < :last_month')
            ->setParameter('daily', 'daily')
            ->setParameter('weekly', 'weekly')
            ->setParameter('monthly', 'monthly')
            ->setParameter('yesterday', (new DateTime('-1 day'))->format('Y-m-d H:i:s'))
            ->setParameter('last_week', (new DateTime('-1 week'))->format('Y-m-d H:i:s'))
            ->setParameter('last_month', (new DateTime('-1 month'))->format('Y-m-d H:i:s'))
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function getSourceByUrl(string $url): ?array
    {
        $qb = $this->createQueryBuilder();
        $result = $qb->select('*')
            ->from($this->table)
            ->where('url = :url')
            ->setParameter('url', $url)
            ->executeQuery()
            ->fetchAssociative();
        return $result ?: null;
    }

    public function getSelectedLayers(int $sourceId): array
    {
        $qb = $this->createQueryBuilder();
        return $qb->select('layer_name')
            ->from('harvest_source_layers')
            ->where('harvest_source_id = :sourceId')
            ->setParameter('sourceId', $sourceId)
            ->executeQuery()
            ->fetchFirstColumn();
    }

    public function setSelectedLayers(int $sourceId, array $layers): bool
    {
        try {
            $this->db->beginTransaction();

            // Delete existing layers
            $this->db->executeStatement(
                'DELETE FROM harvest_source_layers WHERE harvest_source_id = ?',
                [$sourceId]
            );

            // Insert new layers
            foreach ($layers as $layer) {
                $this->db->insert('harvest_source_layers', [
                    'harvest_source_id' => $sourceId,
                    'layer_name' => $layer,
                    'created_at' => (new DateTime())->format('Y-m-d H:i:s'),
                    'updated_at' => (new DateTime())->format('Y-m-d H:i:s')
                ]);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error setting selected layers: " . $e->getMessage());
            return false;
        }
    }
} 