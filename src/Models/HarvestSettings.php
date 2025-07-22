<?php

namespace Novella\Models;

use PDO;
use Exception;

class HarvestSettings {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function create(array $data): array {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO harvest_settings (name, wms_url, layers, interval_minutes, next_run)
                VALUES (
                    :name::text, 
                    :wms_url::text, 
                    :layers::jsonb, 
                    :interval_minutes::integer, 
                    CURRENT_TIMESTAMP + (:interval_minutes::integer || ' minutes')::interval
                )
                RETURNING id, name, wms_url, layers, interval_minutes, last_run, next_run, is_active, created_at, updated_at
            ");

            $stmt->execute([
                'name' => $data['name'],
                'wms_url' => $data['wms_url'],
                'layers' => json_encode($data['layers']),
                'interval_minutes' => (int)$data['interval_minutes']
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                throw new Exception("Failed to create harvest settings");
            }

            return [
                'success' => true,
                'message' => 'Harvest settings created successfully',
                'data' => $result
            ];
        } catch (Exception $e) {
            error_log('Error creating harvest settings: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function update(int $id, array $data): array {
        try {
            // Convert boolean values to proper PostgreSQL boolean
            $isActive = isset($data['is_active']) ? ($data['is_active'] ? 'true' : 'false') : 'true';
            $updateNextRun = isset($data['update_next_run']) ? ($data['update_next_run'] ? 'true' : 'false') : 'false';

            $stmt = $this->db->prepare("
                UPDATE harvest_settings 
                SET name = :name::text,
                    wms_url = :wms_url::text,
                    layers = :layers::jsonb,
                    interval_minutes = :interval_minutes::integer,
                    next_run = CASE 
                        WHEN :update_next_run THEN 
                            CURRENT_TIMESTAMP + (:interval_minutes::integer || ' minutes')::interval
                        ELSE next_run
                    END,
                    is_active = :is_active
                WHERE id = :id::integer
                RETURNING id, name, wms_url, layers, interval_minutes, last_run, next_run, is_active, created_at, updated_at
            ");

            $stmt->execute([
                'id' => $id,
                'name' => $data['name'],
                'wms_url' => $data['wms_url'],
                'layers' => json_encode($data['layers']),
                'interval_minutes' => (int)$data['interval_minutes'],
                'is_active' => $isActive,
                'update_next_run' => $updateNextRun
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                throw new Exception("Harvest settings not found");
            }

            return [
                'success' => true,
                'message' => 'Harvest settings updated successfully',
                'data' => $result
            ];
        } catch (Exception $e) {
            error_log('Error updating harvest settings: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function delete(int $id): array {
        try {
            // Start a transaction
            $this->db->beginTransaction();

            // Get the harvest settings to get the WMS URL
            $stmt = $this->db->prepare("SELECT wms_url FROM harvest_settings WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $harvest = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$harvest) {
                throw new Exception("Harvest settings not found");
            }

            // Delete all datasets associated with this harvest
            $stmt = $this->db->prepare("
                DELETE FROM metadata_records 
                WHERE wms_url = :wms_url 
                AND lineage LIKE 'Data harvested from WMS service%'
            ");
            $stmt->execute(['wms_url' => $harvest['wms_url']]);
            $deletedDatasets = $stmt->rowCount();
            error_log("Deleted {$deletedDatasets} datasets associated with harvest {$id}");

            // Delete the harvest settings
            $stmt = $this->db->prepare("DELETE FROM harvest_settings WHERE id = :id RETURNING id");
            $stmt->execute(['id' => $id]);
            
            if (!$stmt->fetch()) {
                throw new Exception("Harvest settings not found");
            }

            // Commit the transaction
            $this->db->commit();

            return [
                'success' => true,
                'message' => "Harvest settings and {$deletedDatasets} associated datasets deleted successfully"
            ];
        } catch (Exception $e) {
            // Rollback the transaction on error
            $this->db->rollBack();
            error_log('Error deleting harvest settings: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getAll(): array {
        try {
            error_log('HarvestSettings::getAll() - Starting database query');
            
            $stmt = $this->db->query("
                SELECT id, name, wms_url, layers, interval_minutes, last_run, next_run, is_active, created_at, updated_at
                FROM harvest_settings
                ORDER BY created_at DESC
            ");
            
            if ($stmt === false) {
                $error = $this->db->errorInfo();
                error_log('Database error in getAll(): ' . json_encode($error));
                throw new Exception('Database error: ' . ($error[2] ?? 'Unknown error'));
            }
            
            $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log('HarvestSettings::getAll() - Retrieved ' . count($settings) . ' settings');
            
            // Parse JSON layers for each setting
            foreach ($settings as &$setting) {
                try {
                    $setting['layers'] = json_decode($setting['layers'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        error_log('JSON decode error for setting ' . $setting['id'] . ': ' . json_last_error_msg());
                        $setting['layers'] = [];
                    }
                } catch (Exception $e) {
                    error_log('Error parsing layers JSON for setting ' . $setting['id'] . ': ' . $e->getMessage());
                    $setting['layers'] = [];
                }
            }

            return [
                'success' => true,
                'data' => $settings
            ];
        } catch (Exception $e) {
            error_log('Error in HarvestSettings::getAll(): ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            throw $e; // Re-throw to be handled by the route handler
        }
    }

    public function getById(int $id): ?array {
        try {
            $stmt = $this->db->prepare("
                SELECT id, name, wms_url, layers, interval_minutes, last_run, next_run, is_active, created_at, updated_at
                FROM harvest_settings
                WHERE id = :id
            ");
            
            $stmt->execute(['id' => $id]);
            $setting = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$setting) {
                error_log("No harvest setting found with id: {$id}");
                return null;
            }

            // Parse JSON layers
            try {
                $setting['layers'] = json_decode($setting['layers'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    error_log("Error decoding layers JSON for setting {$id}: " . json_last_error_msg());
                    $setting['layers'] = [];
                }
            } catch (Exception $e) {
                error_log("Error parsing layers for setting {$id}: " . $e->getMessage());
                $setting['layers'] = [];
            }

            error_log("Retrieved harvest setting: " . json_encode($setting));
            return $setting;
        } catch (Exception $e) {
            error_log('Error getting harvest setting: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    public function getDueHarvests(): array {
        try {
            $stmt = $this->db->query("
                SELECT id, name, wms_url, layers, interval_minutes, last_run, next_run
                FROM harvest_settings
                WHERE is_active = true 
                AND next_run <= CURRENT_TIMESTAMP
                ORDER BY next_run ASC
            ");
            
            $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Parse JSON layers for each setting
            foreach ($settings as &$setting) {
                $setting['layers'] = json_decode($setting['layers'], true);
            }

            return $settings;
        } catch (Exception $e) {
            error_log('Error getting due harvests: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateLastRun(int $id): void {
        try {
            $stmt = $this->db->prepare("
                UPDATE harvest_settings 
                SET last_run = CURRENT_TIMESTAMP,
                    next_run = CURRENT_TIMESTAMP + (interval_minutes || ' minutes')::interval
                WHERE id = :id
            ");
            
            $stmt->execute(['id' => $id]);
        } catch (Exception $e) {
            error_log('Error updating last run: ' . $e->getMessage());
            throw $e;
        }
    }
} 