<?php

declare(strict_types=1);

namespace GeoLibre\Model;

use Doctrine\DBAL\Connection;

class GisData extends AbstractModel
{
    public function __construct(Connection $db)
    {
        parent::__construct($db);
        $this->table = 'datasets';
    }

    public function getAll(array $filters = []): array
    {
        $qb = $this->createQueryBuilder();
        $qb->select('d.*', 'h.name as harvest_source_name', 'm.quality_score', 'ST_AsGeoJSON(d.spatial_extent) as geometry', 'd.wms_url', 'd.wms_layer')
            ->from('datasets', 'd')
            ->leftJoin('d', 'oai_records', 'r', 'd.id = r.dataset_id')
            ->leftJoin('r', 'harvest_sources', 'h', 'r.harvest_source_id = h.id')
            ->leftJoin('d', 'metadata', 'm', 'd.id = m.dataset_id')
            ->where('(r.id IS NULL) OR (r.deleted = false AND h.id IS NOT NULL AND h.schedule IS NOT NULL)');

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

        $result = $qb->orderBy('d.created_at', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        // Convert geometry JSON strings to arrays
        foreach ($result as &$row) {
            if (isset($row['geometry'])) {
                $row['geometry'] = json_decode($row['geometry'], true);
            }
        }

        return $result;
    }

    public function findById(int $id): ?array
    {
        $qb = $this->createQueryBuilder();
        $result = $qb->select('d.*', 'm.metadata_xml', 'm.metadata_standard', 'm.metadata_version', 'm.quality_score', 'hs.name as harvest_source_name', 'ST_AsGeoJSON(d.spatial_extent) as spatial_extent')
            ->from($this->table, 'd')
            ->leftJoin('d', 'metadata', 'm', 'd.id = m.dataset_id')
            ->leftJoin('d', 'oai_records', 'r', 'd.id = r.dataset_id')
            ->leftJoin('r', 'harvest_sources', 'hs', 'r.harvest_source_id = hs.id')
            ->where('d.id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();

        if ($result) {
            // Handle geometry for both manual and harvested datasets
            if (isset($result['spatial_extent'])) {
                $result['spatial_extent'] = json_decode($result['spatial_extent'], true);
            
                // For harvested datasets, try to extract geometry from metadata
                try {
                    $xml = new \SimpleXMLElement($result['metadata_xml']);
                    $xml->registerXPathNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
                    $xml->registerXPathNamespace('gco', 'http://www.isotc211.org/2005/gco');
                    
                    $westArr = $xml->xpath('//gmd:westBoundLongitude//gco:Decimal');
                    $eastArr = $xml->xpath('//gmd:eastBoundLongitude//gco:Decimal');
                    $southArr = $xml->xpath('//gmd:southBoundLatitude//gco:Decimal');
                    $northArr = $xml->xpath('//gmd:northBoundLatitude//gco:Decimal');

                    $west = isset($westArr[0]) ? (float)$westArr[0] : null;
                    $east = isset($eastArr[0]) ? (float)$eastArr[0] : null;
                    $south = isset($southArr[0]) ? (float)$southArr[0] : null;
                    $north = isset($northArr[0]) ? (float)$northArr[0] : null;
                    
                    if ($west && $east && $south && $north) {
                        $result['spatial_extent'] = [
                            'type' => 'Polygon',
                            'coordinates' => [[
                                [$west, $south],
                                [$west, $north],
                                [$east, $north],
                                [$east, $south],
                                [$west, $south]
                            ]]
                        ];
                    }
                } catch (\Exception $e) {
                    error_log("Error extracting geometry from metadata: " . $e->getMessage());
                }
            }

            // Ensure spatial_extent has the correct structure for the edit form
            if (!isset($result['spatial_extent']) || !isset($result['spatial_extent']['coordinates'])) {
                $result['spatial_extent'] = [
                    'type' => 'Polygon',
                    'coordinates' => [[
                        [0, 0],
                        [0, 0],
                        [0, 0],
                        [0, 0],
                        [0, 0]
                    ]]
                ];
            }

            // Ensure coordinates array has the correct structure
            if (!isset($result['spatial_extent']['coordinates'][0]) || 
                !isset($result['spatial_extent']['coordinates'][0][0]) || 
                !isset($result['spatial_extent']['coordinates'][0][0][0])) {
                $result['spatial_extent']['coordinates'] = [[
                    [0, 0],
                    [0, 0],
                    [0, 0],
                    [0, 0],
                    [0, 0]
                ]];
            }

            // Parse metadata XML if present
            if (isset($result['metadata_xml'])) {
                try {
                    $xml = new \SimpleXMLElement($result['metadata_xml']);
                    $xml->registerXPathNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
                    $xml->registerXPathNamespace('gco', 'http://www.isotc211.org/2005/gco');
                    
                    // Helper function to safely get XPath value
                    $getXPathValue = function($xpath) use ($xml) {
                        $result = $xml->xpath($xpath);
                        return $result && isset($result[0]) ? (string)$result[0] : '';
                    };
                    
                    $result['metadata'] = [
                        'abstract' => $getXPathValue('//gmd:abstract//gco:CharacterString'),
                        'purpose' => $getXPathValue('//gmd:purpose//gco:CharacterString'),
                        'crs' => $getXPathValue('//gmd:referenceSystemInfo//gmd:code//gco:CharacterString') ?: 'EPSG:4326',
                        'crsType' => $getXPathValue('//gmd:referenceSystemInfo//gmd:type//gco:CharacterString') ?: 'geographic',
                        'temporalStart' => $getXPathValue('//gmd:temporalElement//gmd:extent//gmd:begin//gco:DateTime'),
                        'temporalEnd' => $getXPathValue('//gmd:temporalElement//gmd:extent//gmd:end//gco:DateTime'),
                        'pointOfContactName' => $getXPathValue('//gmd:pointOfContact//gmd:individualName//gco:CharacterString'),
                        'pointOfContactOrg' => $getXPathValue('//gmd:pointOfContact//gmd:organisationName//gco:CharacterString'),
                        'pointOfContactEmail' => $getXPathValue('//gmd:pointOfContact//gmd:contactInfo//gmd:address//gmd:electronicMailAddress//gco:CharacterString'),
                        'publisherName' => $getXPathValue('//gmd:publisher//gmd:individualName//gco:CharacterString'),
                        'publisherOrg' => $getXPathValue('//gmd:publisher//gmd:organisationName//gco:CharacterString')
                    ];
                } catch (\Exception $e) {
                    error_log("Error parsing metadata XML: " . $e->getMessage());
                    $result['metadata'] = [];
                }
            } else {
                $result['metadata'] = [];
            }
        }

        return $result ?: null;
    }

    public function findByTitle(string $title): ?array
    {
        $qb = $this->createQueryBuilder();
        $result = $qb->select('d.*', 'm.metadata_xml', 'm.metadata_standard', 'm.metadata_version', 'm.quality_score', 'hs.name as harvest_source_name', 'ST_AsGeoJSON(d.spatial_extent) as geometry')
            ->from($this->table, 'd')
            ->leftJoin('d', 'metadata', 'm', 'd.id = m.dataset_id')
            ->leftJoin('d', 'oai_records', 'r', 'd.id = r.dataset_id')
            ->leftJoin('r', 'harvest_sources', 'hs', 'r.harvest_source_id = hs.id')
            ->where('d.title = :title')
            ->andWhere('(d.is_public = true AND d.status = \'published\' AND r.id IS NULL) OR (r.deleted = false AND hs.id IS NOT NULL AND hs.schedule IS NOT NULL)')
            ->setParameter('title', $title)
            ->executeQuery()
            ->fetchAssociative();

        if ($result && isset($result['geometry'])) {
            $result['geometry'] = json_decode($result['geometry'], true);
        }

        return $result ?: null;
    }

    public function findByBoundingBox(float $minX, float $minY, float $maxX, float $maxY): array
    {
        $qb = $this->createQueryBuilder();
        $result = $qb->select('d.*', 'm.metadata_xml', 'm.metadata_standard', 'm.metadata_version', 'm.quality_score', 'hs.name as harvest_source_name', 'ST_AsGeoJSON(d.spatial_extent) as geometry')
            ->from('datasets', 'd')
            ->leftJoin('d', 'metadata', 'm', 'd.id = m.dataset_id')
            ->leftJoin('d', 'oai_records', 'r', 'd.id = r.dataset_id')
            ->leftJoin('r', 'harvest_sources', 'hs', 'r.harvest_source_id = hs.id')
            ->where('ST_Intersects(d.spatial_extent, ST_MakeEnvelope(:minX, :minY, :maxX, :maxY, 4326))')
            ->andWhere('(d.is_public = true AND d.status = \'published\' AND r.id IS NULL) OR (r.deleted = false AND hs.id IS NOT NULL AND hs.schedule IS NOT NULL)')
            ->setParameter('minX', $minX)
            ->setParameter('minY', $minY)
            ->setParameter('maxX', $maxX)
            ->setParameter('maxY', $maxY)
            ->executeQuery()
            ->fetchAllAssociative();

        // Convert geometry JSON strings to arrays
        foreach ($result as &$row) {
            if (isset($row['geometry'])) {
                $row['geometry'] = json_decode($row['geometry'], true);
            }
        }

        return $result;
    }

    public function createGisData(array $data): int
    {
        // Set timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Handle boolean fields
        if (isset($data['is_public'])) {
            $data['is_public'] = filter_var($data['is_public'], FILTER_VALIDATE_BOOLEAN);
        } else {
            $data['is_public'] = false;
        }

        // Set default values for new fields if not provided
        $data['status'] = $data['status'] ?? 'draft';

        // Extract spatial_extent and remove it from data array
        $spatial_extent = $data['spatial_extent'] ?? null;
        unset($data['spatial_extent']);

        // Build the SQL query
        $columns = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = sprintf(
            "INSERT INTO %s (%s%s) VALUES (%s%s) RETURNING id",
            $this->table,
            implode(', ', $columns),
            $spatial_extent ? ', spatial_extent' : '',
            implode(', ', $placeholders),
            $spatial_extent ? ', ST_GeomFromGeoJSON(?)' : ''
        );

        // Add spatial_extent as the last parameter if provided
        if ($spatial_extent) {
            $values[] = $spatial_extent;  // Don't encode again, it's already encoded
        }

        // Execute the query
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery($values);
        return (int) $result->fetchOne();
    }

    public function updateGisData(int $id, array $data): bool
    {
        try {
            $qb = $this->db->createQueryBuilder();
            $qb->update('datasets')
               ->where('id = :id')
               ->setParameter('id', $id);

            foreach ($data as $key => $value) {
                $qb->set($key, ':' . $key)
                   ->setParameter($key, $value);
            }

            $result = $qb->executeQuery();
            return $result->rowCount() > 0;
        } catch (\Exception $e) {
            error_log("Error updating dataset: " . $e->getMessage());
            return false;
        }
    }

    public function search(string $query, array $filters = []): array
    {
        $qb = $this->createQueryBuilder();
        $qb->select('d.*', 'h.name as harvest_source_name', 'm.quality_score', 'ST_AsGeoJSON(d.spatial_extent) as geometry')
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

        if (isset($filters['is_public'])) {
            $qb->andWhere('d.is_public = :is_public')
                ->setParameter('is_public', $filters['is_public']);
        }

        if (!empty($filters['status'])) {
            $qb->andWhere('d.status = :status')
                ->setParameter('status', $filters['status']);
        }

        $result = $qb->orderBy('d.created_at', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        // Convert geometry JSON strings to arrays
        foreach ($result as &$row) {
            if (isset($row['geometry'])) {
                $row['geometry'] = json_decode($row['geometry'], true);
            }
        }

        return $result;
    }

    public function deleteDataset(int $id): bool
    {
        return $this->delete($id);
    }

    public function findAllPublic(int $page = 1, int $limit = 10, ?string $search = null, ?string $metadataStandard = null, ?string $harvestSource = null, ?string $dateFrom = null, ?string $dateTo = null, ?array $topics = null, ?string $updatedAt = null, ?string $mapExtent = null, ?string $spatialMode = 'any'): array
    {
        $offset = ($page - 1) * $limit;
        
        $qb = $this->createQueryBuilder();
        $qb->select('d.*', 'h.name as harvest_source_name', 'm.quality_score', 'ST_AsGeoJSON(d.spatial_extent) as geometry')
            ->from('datasets', 'd')
            ->leftJoin('d', 'oai_records', 'r', 'd.id = r.dataset_id')
            ->leftJoin('r', 'harvest_sources', 'h', 'r.harvest_source_id = h.id')
            ->leftJoin('d', 'metadata', 'm', 'd.id = m.dataset_id')
            ->where('(d.is_public = true AND d.status = \'published\') OR (r.deleted = false AND h.id IS NOT NULL AND h.schedule IS NOT NULL AND d.is_public = true)');

        // Add search condition if provided
        if ($search) {
            $qb->andWhere('d.title ILIKE :search OR d.description ILIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        // Add metadata standard filter if provided
        if ($metadataStandard) {
            $qb->andWhere('m.metadata_standard = :metadata_standard')
               ->setParameter('metadata_standard', $metadataStandard);
        }

        // Add harvest source filter if provided
        if ($harvestSource) {
            $qb->andWhere('h.name = :harvest_source')
               ->setParameter('harvest_source', $harvestSource);
        }

        // Add date range filters if provided
        if ($dateFrom) {
            $qb->andWhere('d.updated_at >= :date_from')
               ->setParameter('date_from', $dateFrom);
        }
        if ($dateTo) {
            $qb->andWhere('d.updated_at <= :date_to')
               ->setParameter('date_to', $dateTo);
        }

        // Add updated_at search if provided
        if ($updatedAt) {
            $qb->andWhere('d.updated_at >= :updatedAt')
                ->setParameter('updatedAt', $updatedAt);
        }

        // Add topics filter if provided
        if ($topics && !empty($topics)) {
            $qb->select('DISTINCT d.*', 'h.name as harvest_source_name', 'm.quality_score', 'ST_AsGeoJSON(d.spatial_extent) as geometry')
               ->innerJoin('d', 'dataset_topics', 'dt', 'd.id = dt.dataset_id')
               ->andWhere('dt.topic_id IN (:topics)')
               ->setParameter('topics', $topics, Connection::PARAM_INT_ARRAY);
        }

        // Add spatial filter if needed
        if ($mapExtent && $spatialMode && $spatialMode !== 'any') {
            $coords = explode(',', $mapExtent);
            if (count($coords) === 4) {
                $minLon = (float)$coords[0];
                $minLat = (float)$coords[1];
                $maxLon = (float)$coords[2];
                $maxLat = (float)$coords[3];
                $envelope = "ST_MakeEnvelope(:minLon, :minLat, :maxLon, :maxLat, 4326)";
                error_log('DEBUG: Applying spatial filter: ' . $spatialMode . ' with envelope ' . json_encode([$minLon, $minLat, $maxLon, $maxLat]));
                if ($spatialMode === 'intersects') {
                    error_log('DEBUG: SQL: ST_Intersects(d.spatial_extent, ' . $envelope . ')');
                    $qb->andWhere("ST_Intersects(d.spatial_extent, $envelope)");
                } elseif ($spatialMode === 'within') {
                    error_log('DEBUG: SQL: ST_Within(d.spatial_extent, ' . $envelope . ')');
                    $qb->andWhere("ST_Within(d.spatial_extent, $envelope)");
                }
                $qb->setParameter('minLon', $minLon)
                   ->setParameter('minLat', $minLat)
                   ->setParameter('maxLon', $maxLon)
                   ->setParameter('maxLat', $maxLat);
            } else {
                error_log('DEBUG: Invalid map_extent format: ' . var_export($mapExtent, true));
            }
        }

        // Get total count before pagination
        $countQb = clone $qb;
        $countQb->select('COUNT(DISTINCT d.id) as total');
        $total = (int)$countQb->executeQuery()->fetchAssociative()['total'];

        // Add pagination
        $qb->setFirstResult($offset)
           ->setMaxResults($limit)
           ->orderBy('d.updated_at', 'DESC');

        $result = $qb->executeQuery()->fetchAllAssociative();

        // Convert geometry JSON strings to arrays
        foreach ($result as &$row) {
            if (isset($row['geometry'])) {
                $row['geometry'] = json_decode($row['geometry'], true);
            }
        }

        return [
            'items' => $result,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ];
    }

    public function findPublicById(int $id): ?array
    {
        $qb = $this->createQueryBuilder();
        $result = $qb->select('d.*', 'm.metadata_xml', 'm.metadata_standard', 'm.metadata_version', 'm.quality_score', 'hs.name as harvest_source_name', 'ST_AsGeoJSON(d.spatial_extent) as geometry')
            ->from($this->table, 'd')
            ->leftJoin('d', 'metadata', 'm', 'd.id = m.dataset_id')
            ->leftJoin('d', 'oai_records', 'r', 'd.id = r.dataset_id')
            ->leftJoin('r', 'harvest_sources', 'hs', 'r.harvest_source_id = hs.id')
            ->where('d.id = :id')
            ->andWhere('(d.is_public = true) OR (r.deleted = false AND hs.id IS NOT NULL AND hs.schedule IS NOT NULL AND d.is_public = true)')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();

        if ($result) {
            if (isset($result['geometry'])) {
                $result['geometry'] = json_decode($result['geometry'], true);
            }
        }

        return $result ?: null;
    }

    public function isHarvestedDataset(int $id): bool
    {
        $qb = $this->createQueryBuilder();
        $result = $qb->select('COUNT(*)')
            ->from('oai_records', 'r')
            ->where('r.dataset_id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchOne();
        
        return (int)$result > 0;
    }

    public function updatePublicStatus(int $id, bool $isPublic): bool
    {
        $qb = $this->createQueryBuilder();
        $qb->update($this->table)
           ->set('is_public', ':is_public')
           ->set('updated_at', ':updated_at')
           ->where('id = :id')
           ->setParameter('is_public', $isPublic, \Doctrine\DBAL\Types\Types::BOOLEAN)
           ->setParameter('updated_at', date('Y-m-d H:i:s'))
           ->setParameter('id', $id);
        
        return $qb->executeQuery()->rowCount() > 0;
    }

    /**
     * Fetch all features for a dataset as GeoJSON features.
     * Assumes a table 'features' with columns: id, dataset_id, geom (geometry), name, description.
     * Returns an array of GeoJSON features for the given dataset_id.
     */
    public function findFeaturesByDatasetId(int $datasetId): array
    {
        $sql = "SELECT id, ST_AsGeoJSON(geom) AS geometry, name, description FROM features WHERE dataset_id = :dataset_id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['dataset_id' => $datasetId]);
            $rows = $stmt->fetchAll();
        } catch (\Exception $e) {
            error_log('Error fetching features for dataset: ' . $e->getMessage());
            return [];
        }
        $features = [];
        foreach ($rows as $row) {
            $features[] = [
                'type' => 'Feature',
                'geometry' => json_decode($row['geometry'], true),
                'properties' => [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                ]
            ];
        }
        return $features;
    }
} 