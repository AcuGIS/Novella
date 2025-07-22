<?php

namespace Novella\Controllers;

use PDO;
use Exception;
use SimpleXMLElement;

class HarvestController {
    private $db;
    private $uploadDir;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->uploadDir = dirname(__DIR__, 2) . '/storage/uploads/harvested';
        
        // Create upload directory if it doesn't exist
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0775, true);
        }
    }

    public function startHarvest(array $data): array {
        try {
            error_log("Starting harvest process with data: " . json_encode($data));
            
            // Validate input
            if (empty($data['wms_url'])) {
                throw new Exception('WMS URL is required');
            }
            if (empty($data['layers']) || !is_array($data['layers'])) {
                throw new Exception('At least one layer must be selected');
            }

            $wmsUrl = $data['wms_url'];
            $layers = $data['layers'];
            error_log("Processing harvest for WMS URL: {$wmsUrl}");
            error_log("Selected layers: " . implode(', ', $layers));

            // Get WMS capabilities to validate layers and get metadata
            $capabilities = $this->getWmsCapabilities($wmsUrl);
            $availableLayers = $this->parseWmsLayers($capabilities);

            // Log available layers for debugging
            error_log('Available layers: ' . json_encode(array_keys($availableLayers)));

            // Validate that all requested layers exist
            $missingLayers = [];
            foreach ($layers as $layerName) {
                if (!isset($availableLayers[$layerName])) {
                    $missingLayers[] = $layerName;
                }
            }

            if (!empty($missingLayers)) {
                throw new Exception(
                    'The following layers were not found in the WMS service: ' . 
                    implode(', ', $missingLayers) . 
                    '. Available layers are: ' . 
                    implode(', ', array_keys($availableLayers))
                );
            }

            // Start harvesting each layer
            $results = [];
            foreach ($layers as $layerName) {
                error_log("Starting harvest for layer: {$layerName}");
                $layerInfo = $availableLayers[$layerName];
                $result = $this->harvestLayer($wmsUrl, $layerName, $layerInfo);
                $results[$layerName] = $result;
                error_log("Completed harvest for layer {$layerName}: " . json_encode($result));
            }

            error_log("Harvest process completed successfully. Results: " . json_encode($results));

            return [
                'status' => 'success',
                'message' => 'Harvesting started successfully',
                'results' => $results
            ];

        } catch (Exception $e) {
            error_log('Error in startHarvest: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    public function getWmsCapabilities(string $wmsUrl): SimpleXMLElement {
        // Add GetCapabilities parameters if not present
        if (strpos($wmsUrl, '?') === false) {
            $wmsUrl .= '?';
        } else {
            $wmsUrl .= '&';
        }
        $wmsUrl .= 'SERVICE=WMS&REQUEST=GetCapabilities&VERSION=1.3.0';

        error_log("Fetching WMS capabilities from: " . $wmsUrl);

        // Fetch WMS capabilities
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $wmsUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Note: In production, you should verify SSL
        
        $xmlString = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception('Failed to fetch WMS capabilities: ' . curl_error($ch));
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        error_log("WMS capabilities HTTP response code: " . $httpCode);
        error_log("WMS capabilities response length: " . strlen($xmlString));
        
        curl_close($ch);

        // Parse XML
        try {
            $xml = new SimpleXMLElement($xmlString);
            error_log("Successfully parsed WMS capabilities XML");
            return $xml;
        } catch (Exception $e) {
            error_log("Failed to parse WMS capabilities XML. First 500 chars: " . substr($xmlString, 0, 500));
            throw new Exception('Failed to parse WMS capabilities XML: ' . $e->getMessage());
        }
    }

    public function parseWmsLayers(SimpleXMLElement $capabilities): array {
        $layers = [];
        
        // Register WMS namespace
        $capabilities->registerXPathNamespace('wms', 'http://www.opengis.net/wms');
        
        // Get all Layer elements
        $layerElements = $capabilities->xpath('//wms:Layer');
        
        foreach ($layerElements as $layer) {
            // Skip parent layers (those without a Name element)
            if (!isset($layer->Name)) {
                continue;
            }

            $name = (string)$layer->Name;
            $title = (string)$layer->Title;
            $abstract = (string)$layer->Abstract;
            
            // Get bounding box
            $bbox = null;
            if (isset($layer->EX_GeographicBoundingBox)) {
                $bbox = [
                    'west' => (float)$layer->EX_GeographicBoundingBox->westBoundLongitude,
                    'south' => (float)$layer->EX_GeographicBoundingBox->southBoundLatitude,
                    'east' => (float)$layer->EX_GeographicBoundingBox->eastBoundLongitude,
                    'north' => (float)$layer->EX_GeographicBoundingBox->northBoundLatitude
                ];
            } elseif (isset($layer->BoundingBox)) {
                // Try to find a geographic bounding box
                foreach ($layer->BoundingBox as $box) {
                    $crs = (string)$box['CRS'];
                    if (strpos($crs, 'EPSG:4326') !== false || strpos($crs, 'CRS:84') !== false) {
                        $bbox = [
                            'west' => (float)$box['minx'],
                            'south' => (float)$box['miny'],
                            'east' => (float)$box['maxx'],
                            'north' => (float)$box['maxy']
                        ];
                        break;
                    }
                }
            }

            // Validate bounding box if available
            if ($bbox) {
                // Check if coordinates are within valid geographic bounds
                if ($bbox['west'] < -180 || $bbox['west'] > 180 ||
                    $bbox['east'] < -180 || $bbox['east'] > 180 ||
                    $bbox['south'] < -90 || $bbox['south'] > 90 ||
                    $bbox['north'] < -90 || $bbox['north'] > 90) {
                    error_log("Invalid geographic coordinates in WMS layer {$layer->Name}: " . json_encode($bbox));
                    $bbox = null;
                }

                // Check if west is less than east and south is less than north
                if ($bbox && ($bbox['west'] >= $bbox['east'] || $bbox['south'] >= $bbox['north'])) {
                    error_log("Invalid bounding box order in WMS layer {$layer->Name}: " . json_encode($bbox));
                    $bbox = null;
                }

                if ($bbox) {
                    // Round to 6 decimal places
                    $bbox = array_map(function($value) {
                        return round((float)$value, 6);
                    }, $bbox);
                }
            }

            // Get keywords
            $keywords = [];
            if (isset($layer->KeywordList)) {
                foreach ($layer->KeywordList->Keyword as $keyword) {
                    $keywords[] = (string)$keyword;
                }
            }

            $layers[$name] = [
                'name' => $name,
                'title' => $title,
                'abstract' => $abstract,
                'bbox' => $bbox,
                'keywords' => $keywords
            ];
        }

        return $layers;
    }

    private function harvestLayer(string $wmsUrl, string $layerName, array $layerInfo): array {
        try {
            error_log("Starting harvest for layer: {$layerName}");
            
            // Check if a metadata record already exists for this WMS layer
            $stmt = $this->db->prepare("
                SELECT id 
                FROM metadata_records 
                WHERE wms_url = :wms_url 
                AND wms_layer = :wms_layer
            ");
            
            $stmt->execute([
                'wms_url' => $wmsUrl,
                'wms_layer' => $layerName
            ]);
            
            $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);
            $metadataId = $existingRecord ? $existingRecord['id'] : null;
            
            // Create a unique directory for this layer
            $layerDir = $this->uploadDir . '/' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $layerName);
            mkdir($layerDir, 0775, true);
            error_log("Created layer directory: {$layerDir}");

            // Prepare metadata content
            $metadataContent = [
                'wms_url' => $wmsUrl,
                'wms_layer' => $layerName,
                'title' => $layerInfo['title'] ?? $layerName,
                'abstract' => $layerInfo['abstract'] ?? '',
                'keywords' => is_array($layerInfo['keywords'] ?? null) ? implode(', ', $layerInfo['keywords']) : ($layerInfo['keywords'] ?? ''),
                'lineage' => 'Data harvested from WMS service: ' . $wmsUrl,
                'resource_type' => 'service',
                'metadata_date' => date('Y-m-d'),
                'metadata_language' => 'eng',
                'contact_org' => $layerInfo['responsible_org'] ?? null,
                'service_url' => $wmsUrl,
                'data_format' => ['WMS'],
                'distribution_url' => $wmsUrl,
                'citation_date' => date('Y-m-d'),
                'responsible_org' => 'Novella GIS',
                'responsible_person' => 'System',
                'role' => 'Data Provider'
            ];

            // If we have a bounding box, add it to the metadata
            if (isset($layerInfo['bbox'])) {
                $metadataContent['west_longitude'] = $layerInfo['bbox']['west'];
                $metadataContent['east_longitude'] = $layerInfo['bbox']['east'];
                $metadataContent['south_latitude'] = $layerInfo['bbox']['south'];
                $metadataContent['north_latitude'] = $layerInfo['bbox']['north'];
            }

            // Save layer metadata
            $metadataFile = $layerDir . '/metadata.json';
            $metadataContent['harvested_at'] = date('c');
            $metadataContent['dataset_id'] = $metadataId;
            file_put_contents($metadataFile, json_encode($metadataContent, JSON_PRETTY_PRINT));
            error_log("Saved metadata file for layer {$layerName} at: {$metadataFile}");

            // Create or update the metadata record
            $metadata = new \Novella\Models\Metadata($this->db);
            if ($metadataId) {
                // Update existing record
                $result = $metadata->update($metadataId, $metadataContent);
                error_log("Updated existing metadata record for layer {$layerName}: " . json_encode($result));
            } else {
                // Create new record
                $result = $metadata->create($metadataContent);
                $metadataId = $result['id'];
                error_log("Created new metadata record for layer {$layerName}: " . json_encode($result));
            }

            if (!$result['success']) {
                throw new Exception("Failed to " . ($metadataId ? "update" : "create") . " metadata record: " . $result['message']);
            }

            return [
                'status' => 'success',
                'message' => 'Layer harvested successfully',
                'metadata_file' => $metadataFile,
                'layer_info' => $layerInfo,
                'dataset_id' => $metadataId
            ];

        } catch (Exception $e) {
            error_log("Error harvesting layer {$layerName}: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function getHarvestHistory(): array {
        try {
            $stmt = $this->db->query("
                SELECT 
                    hs.id,
                    hs.name,
                    hs.wms_url,
                    hs.layers,
                    hs.last_run as start_time,
                    hs.next_run as end_time,
                    CASE 
                        WHEN hs.last_run IS NULL THEN 'pending'
                        WHEN hs.next_run > CURRENT_TIMESTAMP THEN 'completed'
                        ELSE 'in_progress'
                    END as status,
                    (
                        SELECT COUNT(*) 
                        FROM metadata_records mr 
                        WHERE mr.wms_url = hs.wms_url 
                        AND mr.lineage LIKE 'Data harvested from WMS service%'
                    ) as records_processed,
                    'Harvest completed successfully' as message
                FROM harvest_settings hs
                ORDER BY hs.last_run DESC NULLS LAST
                LIMIT 50
            ");
            
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Parse JSON layers for each record
            foreach ($history as &$record) {
                try {
                    $record['layers'] = json_decode($record['layers'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        error_log('JSON decode error for harvest history record ' . $record['id'] . ': ' . json_last_error_msg());
                        $record['layers'] = [];
                    }
                } catch (Exception $e) {
                    error_log('Error parsing layers JSON for harvest history record ' . $record['id'] . ': ' . $e->getMessage());
                    $record['layers'] = [];
                }
            }

            return $history;
        } catch (Exception $e) {
            error_log('Error getting harvest history: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Validate and normalize a coordinate value to ensure it's within the valid range
     * for the database field (numeric(10,6))
     * @deprecated Use validateBoundingBox instead
     */
    private function validateCoordinate(float $value): float {
        // Round to 6 decimal places
        $value = round($value, 6);
        // Ensure the value is within the valid range (-9999.999999 to 9999.999999)
        if ($value > 9999.999999) {
            $value = 9999.999999;
        } elseif ($value < -9999.999999) {
            $value = -9999.999999;
        }
        return $value;
    }
} 