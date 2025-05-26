<?php

declare(strict_types=1);

namespace GeoLibre\Model;

use Doctrine\DBAL\Connection;
use DateTime;

class OaiPmh extends AbstractModel
{
    public function __construct(Connection $db)
    {
        parent::__construct($db);
        $this->table = 'oai_records';
    }

    public function getRecordByIdentifier(string $identifier): ?array
    {
        $qb = $this->createQueryBuilder();
        $result = $qb->select('r.*', 'd.title', 'd.description')
            ->from($this->table, 'r')
            ->join('r', 'datasets', 'd', 'r.dataset_id = d.id')
            ->leftJoin('r', 'harvest_sources', 'h', 'r.harvest_source_id = h.id')
            ->where('r.oai_identifier = :identifier')
            ->andWhere('h.id IS NULL OR (r.deleted = false AND h.id IS NOT NULL)')
            ->setParameter('identifier', $identifier)
            ->executeQuery()
            ->fetchAssociative();

        if ($result) {
            // Get metadata separately to handle cases where it might not exist
            $metadata = $this->db->executeQuery(
                'SELECT metadata_xml FROM metadata WHERE dataset_id = ?',
                [$result['dataset_id']]
            )->fetchAssociative();
            
            $result['metadata_xml'] = $metadata ? $metadata['metadata_xml'] : null;
        }

        return $result ?: null;
    }

    public function getRecordsByDateRange(DateTime $from, DateTime $until, ?string $setSpec = null): array
    {
        $qb = $this->createQueryBuilder();
        $qb->select('r.*', 'd.title', 'd.description')
            ->from($this->table, 'r')
            ->join('r', 'datasets', 'd', 'r.dataset_id = d.id')
            ->leftJoin('r', 'harvest_sources', 'h', 'r.harvest_source_id = h.id')
            ->where('r.datestamp BETWEEN :from AND :until')
            ->andWhere('h.id IS NULL OR (r.deleted = false AND h.id IS NOT NULL)')
            ->setParameter('from', $from->format('Y-m-d H:i:s'))
            ->setParameter('until', $until->format('Y-m-d H:i:s'))
            ->orderBy('r.datestamp', 'ASC');

        if ($setSpec !== null) {
            $qb->andWhere('EXISTS (
                SELECT 1 FROM oai_record_sets rs
                JOIN oai_sets s ON rs.set_id = s.id
                WHERE rs.record_id = r.id AND s.set_spec = :setSpec
            )')
            ->setParameter('setSpec', $setSpec);
        }

        $records = $qb->executeQuery()->fetchAllAssociative();

        // Get metadata for all records in a single query
        if (!empty($records)) {
            $datasetIds = array_column($records, 'dataset_id');
            $metadataMap = $this->db->executeQuery(
                'SELECT dataset_id, metadata_xml FROM metadata WHERE dataset_id IN (?)',
                [$datasetIds],
                [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY]
            )->fetchAllKeyValue();

            // Add metadata to records
            foreach ($records as &$record) {
                $record['metadata_xml'] = $metadataMap[$record['dataset_id']] ?? null;
            }
        }

        return $records;
    }

    public function getSets(): array
    {
        $qb = $this->createQueryBuilder();
        return $qb->select('s.*', 'COUNT(rs.record_id) as record_count')
            ->from('oai_sets', 's')
            ->leftJoin('s', 'oai_record_sets', 'rs', 's.id = rs.set_id')
            ->groupBy('s.id')
            ->orderBy('s.set_spec', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function addRecordToSet(int $recordId, string $setSpec): bool
    {
        try {
            $this->db->beginTransaction();

            // Get set ID
            $setId = $this->db->executeQuery(
                'SELECT id FROM oai_sets WHERE set_spec = ?',
                [$setSpec]
            )->fetchOne();

            if (!$setId) {
                throw new \Exception("Set not found: $setSpec");
            }

            // Add record to set
            $this->db->executeStatement(
                'INSERT INTO oai_record_sets (record_id, set_id)
                 VALUES (?, ?)
                 ON CONFLICT (record_id, set_id) DO NOTHING',
                [$recordId, $setId]
            );

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function removeRecordFromSet(int $recordId, string $setSpec): bool
    {
        try {
            $this->db->beginTransaction();

            // Get set ID
            $setId = $this->db->executeQuery(
                'SELECT id FROM oai_sets WHERE set_spec = ?',
                [$setSpec]
            )->fetchOne();

            if (!$setId) {
                throw new \Exception("Set not found: $setSpec");
            }

            // Remove record from set
            $this->db->executeStatement(
                'DELETE FROM oai_record_sets
                 WHERE record_id = ? AND set_id = ?',
                [$recordId, $setId]
            );

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function markAsDeleted(string $identifier, ?int $harvestSourceId = null): bool
    {
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE {$this->table}
                    SET deleted = true,
                        status = 'deleted',
                        updated_at = CURRENT_TIMESTAMP
                    WHERE oai_identifier = ?";
            $params = [$identifier];

            if ($harvestSourceId !== null) {
                $sql .= " AND harvest_source_id = ?";
                $params[] = $harvestSourceId;
            }

            $result = $this->db->executeStatement($sql, $params) > 0;
            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error marking record as deleted: " . $e->getMessage());
            throw $e;
        }
    }

    public function getDeletedRecords(DateTime $from, DateTime $until): array
    {
        $qb = $this->createQueryBuilder();
        return $qb->select('r.*', 'd.title')
            ->from($this->table, 'r')
            ->join('r', 'datasets', 'd', 'r.dataset_id = d.id')
            ->where('r.deleted = true')
            ->andWhere('r.updated_at BETWEEN :from AND :until')
            ->setParameter('from', $from->format('Y-m-d H:i:s'))
            ->setParameter('until', $until->format('Y-m-d H:i:s'))
            ->orderBy('r.updated_at', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function storeRecord(string $identifier, string $metadataXml, ?int $harvestSourceId = null, string $status = 'active'): int
    {
        try {
            error_log("Starting to store record: " . $identifier);
            $this->db->beginTransaction();

            // Check if record already exists
            $existingRecord = $this->getRecordByIdentifier($identifier);
            $now = new DateTime();
            $nowStr = $now->format('Y-m-d H:i:s');

            // Calculate quality score
            $metadata = new Metadata($this->db);
            $qualityScore = $metadata->calculateQualityScore($metadataXml);
            error_log("Calculated quality score: " . $qualityScore);

            if ($existingRecord) {
                error_log("Record already exists, updating: " . $identifier);
                $datasetId = $existingRecord['dataset_id'];

                // If the record is being updated with a new harvest source, update it
                if ($harvestSourceId !== null) {
                    // Update dataset
                    $this->db->update('datasets', [
                        'title' => $this->extractTitle($metadataXml),
                        'description' => $this->extractDescription($metadataXml),
                        'updated_at' => $nowStr
                    ], ['id' => $datasetId]);

                    // Update metadata
                    $this->db->executeStatement(
                        'UPDATE metadata 
                         SET metadata_xml = ?::xml,
                             quality_score = ?,
                             updated_at = ?
                         WHERE dataset_id = ?',
                        [$metadataXml, $qualityScore, $nowStr, $datasetId]
                    );

                    // Update OAI record
                    $this->db->executeStatement(
                        'UPDATE oai_records 
                         SET datestamp = ?,
                             deleted = ?::boolean,
                             updated_at = ?,
                             harvest_source_id = ?,
                             status = ?
                         WHERE oai_identifier = ?',
                        [$nowStr, 'false', $nowStr, $harvestSourceId, $status, $identifier]
                    );

                    $recordId = $existingRecord['id'];
                } else {
                    // If no harvest source is provided, mark the record as deleted
                    $this->db->executeStatement(
                        'UPDATE oai_records 
                         SET deleted = ?::boolean,
                             updated_at = ?,
                             status = ?
                         WHERE oai_identifier = ?',
                        ['true', $nowStr, $status, $identifier]
                    );
                    $recordId = $existingRecord['id'];
                }
            } else {
                error_log("Creating new record: " . $identifier);
                // Only create new records if they have a harvest source
                if ($harvestSourceId !== null) {
                    // Create dataset
                    $this->db->insert('datasets', [
                        'title' => $this->extractTitle($metadataXml),
                        'description' => $this->extractDescription($metadataXml),
                        'created_at' => $nowStr,
                        'updated_at' => $nowStr
                    ]);
                    $datasetId = (int) $this->db->lastInsertId();

                    // Create metadata
                    $this->db->executeStatement(
                        'INSERT INTO metadata (dataset_id, metadata_standard, metadata_version, metadata_xml, quality_score, created_at, updated_at)
                         VALUES (?, ?, ?, ?::xml, ?, ?, ?)',
                        [
                            $datasetId,
                            'ISO 19115',
                            '2018',
                            $metadataXml,
                            $qualityScore,
                            $nowStr,
                            $nowStr
                        ]
                    );

                    // Create OAI record
                    $this->db->executeStatement(
                        'INSERT INTO oai_records (dataset_id, oai_identifier, datestamp, deleted, harvest_source_id, status, created_at, updated_at)
                         VALUES (?, ?, ?, ?::boolean, ?, ?, ?, ?)',
                        [
                            $datasetId,
                            $identifier,
                            $nowStr,
                            'false',
                            $harvestSourceId,
                            $status,
                            $nowStr,
                            $nowStr
                        ]
                    );

                    $recordId = (int) $this->db->lastInsertId();
                } else {
                    throw new \Exception("Cannot create new record without harvest source ID");
                }
            }

            $this->db->commit();
            error_log("Successfully stored record: " . $identifier);
            return $recordId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error storing record: " . $e->getMessage());
            throw $e;
        }
    }

    private function extractTitle(string $metadataXml): string
    {
        try {
            $xml = new \SimpleXMLElement($metadataXml);
            $xml->registerXPathNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
            $xml->registerXPathNamespace('gco', 'http://www.isotc211.org/2005/gco');
            
            // Try to get title from citation
            $titles = $xml->xpath('//gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString');
            if ($titles) {
                return (string)$titles[0];
            }
            
            // Fallback to any title
            $titles = $xml->xpath('//gmd:title/gco:CharacterString');
            if ($titles) {
                return (string)$titles[0];
            }
            
            error_log("No title found in metadata XML");
            return 'Untitled Dataset';
        } catch (\Exception $e) {
            error_log("Error extracting title: " . $e->getMessage());
            return 'Untitled Dataset';
        }
    }

    private function extractDescription(string $metadataXml): string
    {
        try {
            $xml = new \SimpleXMLElement($metadataXml);
            $xml->registerXPathNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
            $xml->registerXPathNamespace('gco', 'http://www.isotc211.org/2005/gco');
            
            // Try to get abstract
            $abstracts = $xml->xpath('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gco:CharacterString');
            if ($abstracts) {
                return (string)$abstracts[0];
            }
            
            error_log("No description found in metadata XML");
            return '';
        } catch (\Exception $e) {
            error_log("Error extracting description: " . $e->getMessage());
            return '';
        }
    }

    public function getProcessedCount(int $harvestSourceId): int
    {
        return (int) $this->db->executeQuery(
            'SELECT COUNT(*) FROM oai_records WHERE harvest_source_id = ? AND status = ?',
            [$harvestSourceId, 'active']
        )->fetchOne();
    }

    public function getSkippedCount(int $harvestSourceId): int
    {
        return (int) $this->db->executeQuery(
            'SELECT COUNT(*) FROM oai_records WHERE harvest_source_id = ? AND status = ?',
            [$harvestSourceId, 'skipped']
        )->fetchOne();
    }

    public function getErrorCount(int $harvestSourceId): int
    {
        return (int) $this->db->executeQuery(
            'SELECT COUNT(*) FROM oai_records WHERE harvest_source_id = ? AND status = ?',
            [$harvestSourceId, 'error']
        )->fetchOne();
    }

    public function getLatestLogs(int $harvestSourceId, int $limit = 50): array
    {
        return $this->db->executeQuery(
            'SELECT message, created_at FROM oai_logs 
             WHERE harvest_source_id = ? 
             ORDER BY created_at DESC 
             LIMIT ?',
            [$harvestSourceId, $limit]
        )->fetchAllAssociative();
    }

    public function addLog(int $harvestSourceId, string $message): void
    {
        $this->db->executeStatement(
            'INSERT INTO oai_logs (harvest_source_id, message, created_at) VALUES (?, ?, NOW())',
            [$harvestSourceId, $message]
        );
    }

    public function getRecord(int $harvestSourceId, string $identifier): ?array
    {
        $result = $this->db->executeQuery(
            'SELECT * FROM oai_records WHERE harvest_source_id = ? AND identifier = ?',
            [$harvestSourceId, $identifier]
        )->fetchAssociative();
        
        return $result ?: null;
    }
} 