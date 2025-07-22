<?php

namespace Novella\Models;

use Novella\Database\Database;
use PDO;
use PDOException;
use Exception;
use Psr\Container\ContainerInterface;

class Metadata {
    private $db;
    private $container;

    public function __construct(PDO $db, ?ContainerInterface $container = null) {
        $this->db = $db;
        $this->container = $container;
    }

    public function getDb(): PDO {
        return $this->db;
    }

    private function formatKeywordsForPostgres($keywords): ?string {
        if (empty($keywords)) {
            return null;
        }

        // If keywords is already an array, convert it to a string
        if (is_array($keywords)) {
            $keywords = implode(', ', array_map('trim', $keywords));
        }

        // Split by comma and clean up each keyword
        $keywordArray = array_map(function($keyword) {
            return trim($keyword);
        }, explode(',', $keywords));

        // Remove empty keywords and duplicates
        $keywordArray = array_filter(array_unique($keywordArray));

        if (empty($keywordArray)) {
            return null;
        }

        // Format for PostgreSQL array
        return '{' . implode(',', array_map(function($keyword) {
            return '"' . str_replace('"', '\\"', $keyword) . '"';
        }, $keywordArray)) . '}';
    }

    public function create(array $data): array {
        try {
            $this->db->beginTransaction();

            // Format keywords for PostgreSQL
            $keywords = $this->formatKeywordsForPostgres($data['keywords'] ?? null);

            // Insert main metadata record
            $stmt = $this->db->prepare("
                INSERT INTO metadata_records (
                    title, abstract, purpose, keywords, wms_url, wms_layer,
                    contact_org, conformity, service_url, metadata_date, metadata_language,
                    metadata_point_of_contact, spatial_resolution, resource_type, lineage,
                    data_format, distribution_url, coupled_resource,
                    metadata_poc_organization, metadata_poc_email, metadata_poc_role,
                    resource_identifier, maintenance_frequency, character_set,
                    topic_id, inspire_theme_id
                )
                VALUES (
                    :title, :abstract, :purpose, :keywords::text[], :wms_url, :wms_layer,
                    :contact_org, :conformity, :service_url, :metadata_date, :metadata_language,
                    :metadata_point_of_contact, :spatial_resolution, :resource_type, :lineage,
                    :data_format::text[], :distribution_url, :coupled_resource,
                    :metadata_poc_organization, :metadata_poc_email, :metadata_poc_role,
                    :resource_identifier, :maintenance_frequency, :character_set,
                    :topic_id, :inspire_theme_id
                )
                RETURNING id
            ");

            // Format data_format array for PostgreSQL if it exists
            $dataFormat = isset($data['data_format']) && is_array($data['data_format']) 
                ? '{' . implode(',', array_map(function($format) {
                    return '"' . str_replace('"', '\\"', $format) . '"';
                }, $data['data_format'])) . '}'
                : null;

            $stmt->execute([
                'title' => $data['title'],
                'abstract' => $data['abstract'],
                'purpose' => $data['purpose'] ?? null,
                'keywords' => $keywords,
                'wms_url' => $data['wms_url'] ?? null,
                'wms_layer' => $data['wms_layer'] ?? null,
                'contact_org' => $data['contact_org'] ?? null,
                'conformity' => $data['conformity'] ?? null,
                'service_url' => $data['service_url'] ?? null,
                'metadata_date' => $data['metadata_date'] ?? null,
                'metadata_language' => $data['metadata_language'] ?? null,
                'metadata_point_of_contact' => $data['metadata_point_of_contact'] ?? null,
                'spatial_resolution' => $data['spatial_resolution'] ?? null,
                'resource_type' => $data['resource_type'] ?? null,
                'lineage' => $data['lineage'] ?? null,
                'data_format' => $dataFormat,
                'distribution_url' => $data['distribution_url'] ?? null,
                'coupled_resource' => $data['coupled_resource'] ?? null,
                'metadata_poc_organization' => $data['metadata_poc_organization'] ?? null,
                'metadata_poc_email' => $data['metadata_poc_email'] ?? null,
                'metadata_poc_role' => $data['metadata_poc_role'] ?? null,
                'resource_identifier' => $data['resource_identifier'] ?? null,
                'maintenance_frequency' => $data['maintenance_frequency'] ?? null,
                'character_set' => $data['character_set'] ?? null,
                'topic_id' => $data['topic'] ?? null,
                'inspire_theme_id' => $data['inspire_theme'] ?? null
            ]);

            $metadataId = $stmt->fetchColumn();

            if (!$metadataId) {
                throw new PDOException("Failed to insert metadata record");
            }

            // Insert citation
            $stmt = $this->db->prepare("
                INSERT INTO citations (metadata_id, citation_date, responsible_org, responsible_person, role)
                VALUES (:metadata_id, :citation_date, :responsible_org, :responsible_person, :role)
            ");

            $stmt->execute([
                'metadata_id' => $metadataId,
                'citation_date' => $data['citation_date'],
                'responsible_org' => $data['responsible_org'],
                'responsible_person' => $data['responsible_person'] ?? null,
                'role' => $data['role'] ?? null
            ]);

            // Insert geographic extent
            $stmt = $this->db->prepare("
                INSERT INTO geographic_extents (metadata_id, west_longitude, east_longitude, south_latitude, north_latitude)
                VALUES (:metadata_id, :west_longitude, :east_longitude, :south_latitude, :north_latitude)
            ");

            // Round spatial extent values to 6 decimal places
            $west_longitude = round($data['west_longitude'], 6);
            $east_longitude = round($data['east_longitude'], 6);
            $south_latitude = round($data['south_latitude'], 6);
            $north_latitude = round($data['north_latitude'], 6);

            $stmt->execute([
                'metadata_id' => $metadataId,
                'west_longitude' => $west_longitude,
                'east_longitude' => $east_longitude,
                'south_latitude' => $south_latitude,
                'north_latitude' => $north_latitude
            ]);

            // Insert temporal extent if dates are provided
            if (!empty($data['start_date']) || !empty($data['end_date'])) {
                $stmt = $this->db->prepare("
                    INSERT INTO temporal_extents (metadata_id, start_date, end_date)
                    VALUES (:metadata_id, :start_date, :end_date)
                ");

                $stmt->execute([
                    'metadata_id' => $metadataId,
                    'start_date' => $data['start_date'] ?? null,
                    'end_date' => $data['end_date'] ?? null
                ]);
            }

            // Insert spatial representation if provided
            if (!empty($data['coordinate_system'])) {
                $stmt = $this->db->prepare("
                    INSERT INTO spatial_representations (metadata_id, coordinate_system)
                    VALUES (:metadata_id, :coordinate_system)
                ");

                $stmt->execute([
                    'metadata_id' => $metadataId,
                    'coordinate_system' => $data['coordinate_system']
                ]);
            }

            // Insert constraints if provided
            if (!empty($data['use_constraints']) || !empty($data['access_constraints']) || !empty($data['use_limitation'])) {
                $stmt = $this->db->prepare("
                    INSERT INTO constraints (metadata_id, use_constraints, access_constraints, use_limitation)
                    VALUES (:metadata_id, :use_constraints, :access_constraints, :use_limitation)
                ");

                $stmt->execute([
                    'metadata_id' => $metadataId,
                    'use_constraints' => $data['use_constraints'] ?? null,
                    'access_constraints' => $data['access_constraints'] ?? null,
                    'use_limitation' => $data['use_limitation'] ?? null
                ]);
            }

            // Insert INSPIRE metadata if provided
            if (!empty($data['point_of_contact_org']) || !empty($data['conformity_result']) || !empty($data['spatial_data_service_url'])) {
                $stmt = $this->db->prepare("
                    INSERT INTO inspire_metadata (metadata_id, point_of_contact_org, conformity_result, spatial_data_service_url)
                    VALUES (:metadata_id, :point_of_contact_org, :conformity_result, :spatial_data_service_url)
                ");

                $stmt->execute([
                    'metadata_id' => $metadataId,
                    'point_of_contact_org' => $data['point_of_contact_org'] ?? null,
                    'conformity_result' => $data['conformity_result'] ?? null,
                    'spatial_data_service_url' => $data['spatial_data_service_url'] ?? null
                ]);
            }

            $this->db->commit();

            return [
                'success' => true,
                'id' => $metadataId,
                'message' => 'Metadata saved successfully'
            ];

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Database error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Failed to save metadata: " . $e->getMessage()
            ];
        }
    }

    public function getById(string $id): ?array {
        $isAdmin = false;
        if ($this->container) {
            $auth = $this->container->get('auth');
            $isAdmin = $auth->isLoggedIn() && $auth->getCurrentUser()->hasPermission('edit_dataset');
        }
        
        $sql = "
            SELECT 
                m.*,
                t.topic as topic_name,
                k.keyword as inspire_theme_name,
                c.citation_date,
                c.responsible_org,
                c.responsible_person,
                c.role,
                g.west_longitude,
                g.east_longitude,
                g.south_latitude,
                g.north_latitude,
                te.start_date,
                te.end_date,
                sr.coordinate_system,
                cons.use_constraints,
                cons.access_constraints,
                cons.use_limitation,
                im.point_of_contact_org,
                im.conformity_result,
                im.spatial_data_service_url
            FROM metadata_records m
            LEFT JOIN topics t ON t.id = m.topic_id
            LEFT JOIN keywords k ON k.id = m.inspire_theme_id
            LEFT JOIN citations c ON c.metadata_id = m.id
            LEFT JOIN geographic_extents g ON g.metadata_id = m.id
            LEFT JOIN temporal_extents te ON te.metadata_id = m.id
            LEFT JOIN spatial_representations sr ON sr.metadata_id = m.id
            LEFT JOIN constraints cons ON cons.metadata_id = m.id
            LEFT JOIN inspire_metadata im ON im.metadata_id = m.id
            WHERE m.id = :id
            " . ($isAdmin ? "" : "AND m.is_public = TRUE");
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            // Convert PostgreSQL array to PHP array for keywords
            if (isset($result['keywords']) && $result['keywords'] !== null) {
                $keywords = trim($result['keywords'], '{}');
                $keywords = str_replace('"', '', $keywords);
                $result['keywords'] = explode(',', $keywords);
            } else {
                $result['keywords'] = [];
            }
            
            // Convert PostgreSQL array to PHP array for data_format
            if (isset($result['data_format']) && $result['data_format'] !== null) {
                $formats = trim($result['data_format'], '{}');
                $formats = str_replace('"', '', $formats);
                $result['data_format'] = explode(',', $formats);
            } else {
                $result['data_format'] = [];
            }
            
            // add GIS Files
            $sql = 'SELECT id,file_name from gis_files WHERE metadata_id = :id';
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            $result['gis_files'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $result ?: null;
    }

    /**
     * Fetch all datasets for listing page
     * 
     * @param int $page Current page number (1-based)
     * @param int $perPage Number of items per page
     * @return array Array containing datasets and pagination info
     */
    public function getAll(int $page = 1, int $perPage = 12): array {
        $isAdmin = false;
        if ($this->container) {
            $auth = $this->container->get('auth');
            $isAdmin = $auth->isLoggedIn() && $auth->getCurrentUser()->hasPermission('edit_dataset');
        }
        
        // Calculate offset
        $offset = ($page - 1) * $perPage;
        
        // First get total count
        $countSql = "
            SELECT COUNT(DISTINCT m.id) as total
            FROM metadata_records m
            WHERE " . ($isAdmin ? "TRUE" : "m.is_public = TRUE");
        
        $countStmt = $this->db->query($countSql);
        $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Then get paginated results
        $sql = "
            SELECT 
                m.id, 
                m.title, 
                m.abstract, 
                m.keywords,
                m.metadata_date,
                m.is_public,
                m.created_at,
                m.updated_at,
                MAX(g.thumbnail_path) AS thumbnail_path, 
                MAX(g.created_at) AS last_uploaded,
                t.topic as topic_name,
                t.id as topic_id,
                k.keyword as inspire_theme_name,
                k.id as inspire_theme_id
            FROM metadata_records m
            LEFT JOIN gis_files g ON g.metadata_id = m.id AND g.file_type = 'thumbnail'
            LEFT JOIN topics t ON t.id = m.topic_id
            LEFT JOIN keywords k ON k.id = m.inspire_theme_id
            WHERE " . ($isAdmin ? "TRUE" : "m.is_public = TRUE") . "
            GROUP BY m.id, m.title, m.abstract, m.keywords, m.metadata_date, m.is_public, m.created_at, m.updated_at, t.topic, t.id, k.keyword, k.id
            ORDER BY GREATEST(COALESCE(m.updated_at, m.created_at), COALESCE(MAX(g.created_at), '1970-01-01'::timestamp)) DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate pagination info
        $totalPages = ceil($totalCount / $perPage);
        
        return [
            'datasets' => $results,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $totalCount,
                'total_pages' => $totalPages,
                'has_previous' => $page > 1,
                'has_next' => $page < $totalPages
            ]
        ];
    }
    
    /**
     * Delete a dataset and its associated files
     */
    public function delete(string $id): array {
        try {
            
            // Start a transaction
            $this->db->beginTransaction();

            // Delete the metadata record
            $stmt = $this->db->prepare("DELETE FROM metadata_records WHERE id = :id RETURNING id");
            $stmt->execute(['id' => $id]);
            
            if (!$stmt->fetch()) {
                throw new Exception("Dataset not found");
            }

            // Commit the transaction
            $this->db->commit();

            return [
                'success' => true,
                'message' => "Dataset and associated files deleted successfully"
            ];

        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->rollBack();
            error_log('Error deleting dataset: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function update(string $id, array $data): array {
        try {
            $this->db->beginTransaction();

            // Log the data being processed
            error_log('Processing update data: ' . json_encode($data));
            error_log('WMS URL before update: ' . ($data['wms_url'] ?? 'null'));
            error_log('WMS Layer before update: ' . ($data['wms_layer'] ?? 'null'));

            // Format keywords for PostgreSQL
            $keywords = $this->formatKeywordsForPostgres($data['keywords'] ?? null);

            // Format data_format array for PostgreSQL if it exists
            $dataFormat = isset($data['data_format']) && is_array($data['data_format']) 
                ? '{' . implode(',', array_map(function($format) {
                    return '"' . str_replace('"', '\\"', $format) . '"';
                }, $data['data_format'])) . '}'
                : null;

            // Update main metadata record
            $stmt = $this->db->prepare("
                UPDATE metadata_records SET
                    title = :title,
                    abstract = :abstract,
                    purpose = :purpose,
                    keywords = :keywords::text[],
                    wms_url = :wms_url,
                    wms_layer = :wms_layer,
                    contact_org = :contact_org,
                    conformity = :conformity,
                    service_url = :service_url,
                    metadata_date = :metadata_date,
                    metadata_language = :metadata_language,
                    metadata_point_of_contact = :metadata_point_of_contact,
                    spatial_resolution = :spatial_resolution,
                    resource_type = :resource_type,
                    lineage = :lineage,
                    data_format = :data_format::text[],
                    distribution_url = :distribution_url,
                    coupled_resource = :coupled_resource,
                    metadata_poc_organization = :metadata_poc_organization,
                    metadata_poc_email = :metadata_poc_email,
                    metadata_poc_role = :metadata_poc_role,
                    resource_identifier = :resource_identifier,
                    maintenance_frequency = :maintenance_frequency,
                    character_set = :character_set,
                    topic_id = :topic_id,
                    inspire_theme_id = :inspire_theme_id,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
                RETURNING id, wms_url, wms_layer
            ");

            $params = [
                'id' => $id,
                'title' => $data['title'],
                'abstract' => $data['abstract'],
                'purpose' => $data['purpose'] ?? null,
                'keywords' => $keywords,
                'wms_url' => $data['wms_url'] ?? null,
                'wms_layer' => $data['wms_layer'] ?? null,
                'contact_org' => $data['contact_org'] ?? null,
                'conformity' => $data['conformity'] ?? null,
                'service_url' => $data['service_url'] ?? null,
                'metadata_date' => $data['metadata_date'] ?? null,
                'metadata_language' => $data['metadata_language'] ?? null,
                'metadata_point_of_contact' => $data['metadata_point_of_contact'] ?? null,
                'spatial_resolution' => $data['spatial_resolution'] ?? null,
                'resource_type' => $data['resource_type'] ?? null,
                'lineage' => $data['lineage'] ?? null,
                'data_format' => $dataFormat,
                'distribution_url' => $data['distribution_url'] ?? null,
                'coupled_resource' => $data['coupled_resource'] ?? null,
                'metadata_poc_organization' => $data['metadata_poc_organization'] ?? null,
                'metadata_poc_email' => $data['metadata_poc_email'] ?? null,
                'metadata_poc_role' => $data['metadata_poc_role'] ?? null,
                'resource_identifier' => $data['resource_identifier'] ?? null,
                'maintenance_frequency' => $data['maintenance_frequency'] ?? null,
                'character_set' => $data['character_set'] ?? null,
                'topic_id' => $data['topic'] ?? null,
                'inspire_theme_id' => $data['inspire_theme'] ?? null
            ];

            // Log the parameters being used
            error_log('Update parameters: ' . json_encode($params));

            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Log the result of the update
            error_log('Update result from database: ' . json_encode($result));

            if (!$result) {
                throw new PDOException("Failed to update metadata record");
            }

            // Update citation
            $stmt = $this->db->prepare("
                UPDATE citations SET
                    citation_date = :citation_date,
                    responsible_org = :responsible_org,
                    responsible_person = :responsible_person,
                    role = :role
                WHERE metadata_id = :metadata_id
            ");

            $stmt->execute([
                'metadata_id' => $id,
                'citation_date' => $data['citation_date'],
                'responsible_org' => $data['responsible_org'],
                'responsible_person' => $data['responsible_person'] ?? null,
                'role' => $data['role'] ?? null
            ]);

            // Update geographic extent
            $stmt = $this->db->prepare("
                UPDATE geographic_extents SET
                    west_longitude = :west_longitude,
                    east_longitude = :east_longitude,
                    south_latitude = :south_latitude,
                    north_latitude = :north_latitude
                WHERE metadata_id = :metadata_id
            ");

            // Round spatial extent values to 6 decimal places
            $west_longitude = round($data['west_longitude'], 6);
            $east_longitude = round($data['east_longitude'], 6);
            $south_latitude = round($data['south_latitude'], 6);
            $north_latitude = round($data['north_latitude'], 6);

            $stmt->execute([
                'metadata_id' => $id,
                'west_longitude' => $west_longitude,
                'east_longitude' => $east_longitude,
                'south_latitude' => $south_latitude,
                'north_latitude' => $north_latitude
            ]);

            // Update temporal extent
            $stmt = $this->db->prepare("
                UPDATE temporal_extents SET
                    start_date = :start_date,
                    end_date = :end_date
                WHERE metadata_id = :metadata_id
            ");

            $stmt->execute([
                'metadata_id' => $id,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null
            ]);

            // If no row was updated, insert a new one
            if ($stmt->rowCount() === 0) {
                $stmt = $this->db->prepare("
                    INSERT INTO temporal_extents (
                        metadata_id,
                        start_date,
                        end_date
                    ) VALUES (
                        :metadata_id,
                        :start_date,
                        :end_date
                    )
                ");

                $stmt->execute([
                    'metadata_id' => $id,
                    'start_date' => $data['start_date'] ?? null,
                    'end_date' => $data['end_date'] ?? null
                ]);
            }

            // Update spatial representation
            if (!empty($data['coordinate_system'])) {
                $stmt = $this->db->prepare("
                    UPDATE spatial_representations SET
                        coordinate_system = :coordinate_system
                    WHERE metadata_id = :metadata_id
                ");

                $stmt->execute([
                    'metadata_id' => $id,
                    'coordinate_system' => $data['coordinate_system']
                ]);
            }

            // Update constraints
            $stmt = $this->db->prepare("
                UPDATE constraints SET
                    use_constraints = :use_constraints,
                    access_constraints = :access_constraints,
                    use_limitation = :use_limitation
                WHERE metadata_id = :metadata_id
            ");

            $stmt->execute([
                'metadata_id' => $id,
                'use_constraints' => $data['use_constraints'] ?? null,
                'access_constraints' => $data['access_constraints'] ?? null,
                'use_limitation' => $data['use_limitation'] ?? null
            ]);

            // If no row was updated, insert a new one
            if ($stmt->rowCount() === 0) {
                $stmt = $this->db->prepare("
                    INSERT INTO constraints (
                        metadata_id,
                        use_constraints,
                        access_constraints,
                        use_limitation
                    ) VALUES (
                        :metadata_id,
                        :use_constraints,
                        :access_constraints,
                        :use_limitation
                    )
                ");

                $stmt->execute([
                    'metadata_id' => $id,
                    'use_constraints' => $data['use_constraints'] ?? null,
                    'access_constraints' => $data['access_constraints'] ?? null,
                    'use_limitation' => $data['use_limitation'] ?? null
                ]);
            }

            // Update INSPIRE metadata
            $stmt = $this->db->prepare("
                UPDATE inspire_metadata SET
                    point_of_contact_org = :point_of_contact_org,
                    conformity_result = :conformity_result,
                    spatial_data_service_url = :spatial_data_service_url
                WHERE metadata_id = :metadata_id
            ");

            $stmt->execute([
                'metadata_id' => $id,
                'point_of_contact_org' => $data['point_of_contact_org'] ?? null,
                'conformity_result' => $data['conformity_result'] ?? null,
                'spatial_data_service_url' => $data['spatial_data_service_url'] ?? null
            ]);

            // If no row was updated, insert a new one
            if ($stmt->rowCount() === 0) {
                $stmt = $this->db->prepare("
                    INSERT INTO inspire_metadata (
                        metadata_id,
                        point_of_contact_org,
                        conformity_result,
                        spatial_data_service_url
                    ) VALUES (
                        :metadata_id,
                        :point_of_contact_org,
                        :conformity_result,
                        :spatial_data_service_url
                    )
                ");

                $stmt->execute([
                    'metadata_id' => $id,
                    'point_of_contact_org' => $data['point_of_contact_org'] ?? null,
                    'conformity_result' => $data['conformity_result'] ?? null,
                    'spatial_data_service_url' => $data['spatial_data_service_url'] ?? null
                ]);
            }

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Metadata updated successfully',
                'id' => $result['id'],
                'wms_url' => $result['wms_url'],
                'wms_layer' => $result['wms_layer']
            ];

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Database error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Failed to update metadata: " . $e->getMessage()
            ];
        }
    }

    public function togglePublic(string $id): array {
        try {
            $stmt = $this->db->prepare("
                UPDATE metadata_records 
                SET is_public = NOT is_public 
                WHERE id = :id 
                RETURNING id, is_public
            ");
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                throw new Exception("Dataset not found");
            }
            
            return [
                'success' => true,
                'message' => "Dataset is now " . ($result['is_public'] ? "public" : "private"),
                'is_public' => $result['is_public']
            ];
        } catch (Exception $e) {
            error_log('Error toggling dataset public status: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Search datasets by bounding box
     * 
     * @param array $bbox Bounding box coordinates [west, south, east, north]
     * @param string $spatialRelation Spatial relation type (intersects, contains, within)
     * @return array Array of matching datasets
     */
    public function searchByBbox(array $bbox, string $spatialRelation): array
    {
        try {
            // Check if user is admin
            $isAdmin = false;
            if ($this->container) {
                $auth = $this->container->get('auth');
                $isAdmin = $auth->isLoggedIn() && $auth->getCurrentUser()->hasPermission('edit_dataset');
            }

            // Log the input bbox for debugging
            error_log("Spatial search input - bbox: " . json_encode([
                'west' => $bbox['west'],
                'south' => $bbox['south'],
                'east' => $bbox['east'],
                'north' => $bbox['north']
            ]) . ", spatialRelation: " . $spatialRelation . ", isAdmin: " . ($isAdmin ? 'true' : 'false'));

            // Base query to get all datasets that have any overlap with the search box
            $query = "WITH dataset_extents AS (
                SELECT 
                    m.*,
                    t.topic as topic_name,
                    k.keyword as inspire_theme_name,
                    g.west_longitude, g.east_longitude, g.south_latitude, g.north_latitude,
                    -- More precise coordinate comparisons with proper handling of null values and coordinate wrapping
                    CASE 
                        WHEN g.west_longitude IS NULL OR g.east_longitude IS NULL 
                             OR g.south_latitude IS NULL OR g.north_latitude IS NULL
                        THEN 'no_coordinates'
                        WHEN (
                            -- Handle coordinate wrapping for longitude
                            (
                                -- Normal case: both boxes are within -180 to 180
                                (g.west_longitude <= g.east_longitude AND :west::numeric <= :east::numeric AND
                                 g.west_longitude <= :east::numeric AND g.east_longitude >= :west::numeric)
                                OR
                                -- Case 1: dataset crosses 180/-180 boundary
                                (g.west_longitude > g.east_longitude AND
                                 (g.west_longitude <= :east::numeric OR g.east_longitude >= :west::numeric))
                                OR
                                -- Case 2: search box crosses 180/-180 boundary
                                (:west::numeric > :east::numeric AND
                                 (g.west_longitude <= :east::numeric OR g.east_longitude >= :west::numeric))
                            )
                            AND
                            -- Normal latitude comparison
                            g.south_latitude <= :north::numeric AND g.north_latitude >= :south::numeric
                        )
                        THEN 'intersects'
                        ELSE 'no_intersect'
                    END as intersect_status,
                    CASE 
                        WHEN g.west_longitude IS NULL OR g.east_longitude IS NULL 
                             OR g.south_latitude IS NULL OR g.north_latitude IS NULL
                        THEN 'no_coordinates'
                        WHEN (
                            -- Handle coordinate wrapping for longitude
                            (
                                -- Normal case: both boxes are within -180 to 180
                                (g.west_longitude <= g.east_longitude AND :west::numeric <= :east::numeric AND
                                 g.west_longitude >= :west::numeric AND g.east_longitude <= :east::numeric)
                                OR
                                -- Case 1: dataset crosses 180/-180 boundary
                                (g.west_longitude > g.east_longitude AND
                                 g.west_longitude >= :west::numeric AND g.east_longitude <= :east::numeric)
                                OR
                                -- Case 2: search box crosses 180/-180 boundary
                                (:west::numeric > :east::numeric AND
                                 g.west_longitude >= :west::numeric AND g.east_longitude <= :east::numeric)
                            )
                            AND
                            -- Normal latitude comparison
                            g.south_latitude >= :south::numeric AND g.north_latitude <= :north::numeric
                        )
                        THEN 'within'
                        ELSE 'not_within'
                    END as within_status,
                    -- Debug information
                    json_build_object(
                        'has_coordinates', 
                        g.west_longitude IS NOT NULL AND g.east_longitude IS NOT NULL 
                        AND g.south_latitude IS NOT NULL AND g.north_latitude IS NOT NULL,
                        'dataset_bbox', json_build_object(
                            'west', g.west_longitude,
                            'east', g.east_longitude,
                            'south', g.south_latitude,
                            'north', g.north_latitude
                        ),
                        'search_bbox', json_build_object(
                            'west', :west::numeric,
                            'east', :east::numeric,
                            'south', :south::numeric,
                            'north', :north::numeric
                        ),
                        'wraps_180', g.west_longitude > g.east_longitude,
                        'search_wraps_180', :west::numeric > :east::numeric
                    ) as debug_info
                FROM metadata_records m 
                LEFT JOIN geographic_extents g ON m.id = g.metadata_id 
                LEFT JOIN topics t ON t.id = m.topic_id
                LEFT JOIN keywords k ON k.id = m.inspire_theme_id
                WHERE 1=1 " . ($isAdmin ? "" : "AND m.is_public = TRUE") . "
            )
            SELECT * FROM dataset_extents WHERE 1=1";

            // Add spatial relation condition
            switch ($spatialRelation) {
                case 'any':
                case 'intersects':
                    $query .= " AND intersect_status = 'intersects'";  // Only include actual intersections
                    break;
                case 'within':
                    $query .= " AND within_status = 'within'";
                    break;
                default:
                    throw new Exception("Invalid spatial relation: {$spatialRelation}");
            }

            $query .= " ORDER BY title ASC";

            // Log the query and parameters for debugging
            error_log("Spatial search query: " . $query);
            error_log("Spatial search parameters: " . json_encode([
                'west' => $bbox['west'],
                'south' => $bbox['south'],
                'east' => $bbox['east'],
                'north' => $bbox['north'],
                'spatialRelation' => $spatialRelation
            ]));

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':west' => $bbox['west'],
                ':south' => $bbox['south'],
                ':east' => $bbox['east'],
                ':north' => $bbox['north']
            ]);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Log detailed results for debugging
            error_log("Total datasets found: " . count($results));
            foreach ($results as $result) {
                error_log("Dataset found: " . json_encode([
                    'id' => $result['id'],
                    'title' => $result['title'],
                    'extent' => [
                        'west' => $result['west_longitude'],
                        'east' => $result['east_longitude'],
                        'south' => $result['south_latitude'],
                        'north' => $result['north_latitude']
                    ],
                    'intersect_status' => $result['intersect_status'],
                    'within_status' => $result['within_status'],
                    'debug_info' => $result['debug_info']
                ]));
            }

            // Process results to include bbox information
            foreach ($results as &$result) {
                if ($result['west_longitude'] !== null) {
                    $result['bbox'] = [
                        'west' => $result['west_longitude'],
                        'east' => $result['east_longitude'],
                        'south' => $result['south_latitude'],
                        'north' => $result['north_latitude']
                    ];
                } else {
                    $result['bbox'] = null;
                }
                // Remove the individual coordinate fields and debug fields
                unset($result['west_longitude'], $result['east_longitude'], 
                      $result['south_latitude'], $result['north_latitude'],
                      $result['intersect_status'], $result['within_status'], 
                      $result['debug_info']);
            }

            return $results;
        } catch (Exception $e) {
            error_log("Error in searchByBbox: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Get datasets by their IDs
     * @param array $ids Array of dataset IDs
     * @param bool $isSpatialSearch Whether this is a spatial search result
     * @return array Array of datasets
     */
    public function getByIds(array $ids, bool $isSpatialSearch = false): array {
        $isAdmin = false;
        if ($this->container) {
            $auth = $this->container->get('auth');
            $isAdmin = $auth->isLoggedIn() && $auth->getCurrentUser()->hasPermission('edit_dataset');
        }
        
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $query = "SELECT 
            m.*,
            t.topic as topic_name,
            k.keyword as inspire_theme_name,
            g.west_longitude, g.east_longitude, g.south_latitude, g.north_latitude
        FROM metadata_records m 
        LEFT JOIN geographic_extents g ON m.id = g.metadata_id 
        LEFT JOIN topics t ON t.id = m.topic_id
        LEFT JOIN keywords k ON k.id = m.inspire_theme_id
        WHERE m.id IN ($placeholders) " . 
        ($isAdmin ? "" : "AND m.is_public = TRUE") . "
        ORDER BY m.title ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($ids);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Process results to include bbox information
        foreach ($results as &$dataset) {
            if ($dataset['west_longitude'] !== null) {
                $dataset['bbox'] = [
                    'west' => floatval($dataset['west_longitude']),
                    'east' => floatval($dataset['east_longitude']),
                    'south' => floatval($dataset['south_latitude']),
                    'north' => floatval($dataset['north_latitude'])
                ];
            } else {
                $dataset['bbox'] = null;
            }
            // Remove the individual coordinate fields
            unset($dataset['west_longitude'], $dataset['east_longitude'], 
                  $dataset['south_latitude'], $dataset['north_latitude']);
        }
        
        return $results;
    }

    /**
     * Search datasets by text, topic, keyword, and date range
     * 
     * @param string $searchTerm Search term to match against title and abstract
     * @param string|null $topicId Topic ID to filter by
     * @param string|null $keyword Keyword to filter by
     * @param string|null $dateFrom Start date for filtering
     * @param string|null $dateTo End date for filtering
     * @param int $page Current page number (1-based)
     * @param int $perPage Number of items per page
     * @return array Array containing datasets and pagination info
     */
    public function search(string $searchTerm = '', ?string $topicId = null, ?string $keyword = null, 
                         ?string $dateFrom = null, ?string $dateTo = null, 
                         int $page = 1, int $perPage = 12): array {
        $isAdmin = false;
        if ($this->container) {
            $auth = $this->container->get('auth');
            $isAdmin = $auth->isLoggedIn() && $auth->getCurrentUser()->hasPermission('edit_dataset');
        }
        
        // Calculate offset
        $offset = ($page - 1) * $perPage;
        
        // Build the base query conditions
        $conditions = [$isAdmin ? "TRUE" : "m.is_public = TRUE"];
        $params = [];
        
        // Add search term condition
        if (!empty($searchTerm)) {
            $conditions[] = "(LOWER(m.title) LIKE :search OR LOWER(m.abstract) LIKE :search)";
            $params[':search'] = '%' . strtolower($searchTerm) . '%';
        }
        
        // Add topic condition
        if (!empty($topicId)) {
            $conditions[] = "m.topic_id = :topic_id";
            $params[':topic_id'] = $topicId;
        }
        
        // Add keyword condition
        if (!empty($keyword)) {
            $conditions[] = "(EXISTS(SELECT 1 FROM unnest(m.keywords) AS keyword WHERE LOWER(keyword) LIKE :keyword) OR LOWER(k.keyword) = :keyword_exact)";
            $params[':keyword'] = '%' . strtolower($keyword) . '%';
            $params[':keyword_exact'] = strtolower($keyword);
        }
        
        // Add date range conditions
        if (!empty($dateFrom)) {
            $conditions[] = "m.metadata_date >= :date_from";
            $params[':date_from'] = $dateFrom;
        }
        if (!empty($dateTo)) {
            $conditions[] = "m.metadata_date <= :date_to";
            $params[':date_to'] = $dateTo . ' 23:59:59';
        }
        
        // Build the WHERE clause
        $whereClause = implode(' AND ', $conditions);
        
        // First get total count
        $countSql = "
            SELECT COUNT(DISTINCT m.id) as total
            FROM metadata_records m
            LEFT JOIN keywords k ON k.id = m.inspire_theme_id
            WHERE " . $whereClause;
        
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Then get paginated results
        $sql = "
            SELECT 
                m.id, 
                m.title, 
                m.abstract, 
                m.keywords,
                m.metadata_date,
                m.is_public,
                m.created_at,
                m.updated_at,
                MAX(g.thumbnail_path) AS thumbnail_path, 
                MAX(g.created_at) AS last_uploaded,
                t.topic as topic_name,
                t.id as topic_id,
                k.keyword as inspire_theme_name,
                k.id as inspire_theme_id
            FROM metadata_records m
            LEFT JOIN gis_files g ON g.metadata_id = m.id AND g.file_type = 'thumbnail'
            LEFT JOIN topics t ON t.id = m.topic_id
            LEFT JOIN keywords k ON k.id = m.inspire_theme_id
            WHERE " . $whereClause . "
            GROUP BY m.id, m.title, m.abstract, m.keywords, m.metadata_date, m.is_public, m.created_at, m.updated_at, t.topic, t.id, k.keyword, k.id
            ORDER BY GREATEST(COALESCE(m.updated_at, m.created_at), COALESCE(MAX(g.created_at), '1970-01-01'::timestamp)) DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate pagination info
        $totalPages = ceil($totalCount / $perPage);
        
        return [
            'datasets' => $results,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $totalCount,
                'total_pages' => $totalPages,
                'has_previous' => $page > 1,
                'has_next' => $page < $totalPages
            ]
        ];
    }
}
