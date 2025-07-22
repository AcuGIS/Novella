<?php

namespace Novella\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SimpleXMLElement;

class WmsController {
    public function getCapabilities(Request $request, Response $response): Response {
        try {
            error_log("WMS GetCapabilities request received");
            
            // Log request details
            error_log("Request method: " . $request->getMethod());
            error_log("Request headers: " . json_encode($request->getHeaders()));
            
            // Get and validate request body
            $contentType = $request->getHeaderLine('Content-Type');
            error_log("Content-Type: " . $contentType);
            
            if (strpos($contentType, 'application/json') === false) {
                throw new \Exception('Content-Type must be application/json');
            }
            
            $rawBody = (string)$request->getBody();
            error_log("Raw request body: " . $rawBody);
            
            $data = json_decode($rawBody, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON in request body: ' . json_last_error_msg());
            }
            
            error_log("Parsed request body: " . json_encode($data));
            
            if (empty($data['url'])) {
                throw new \Exception('WMS URL is required');
            }

            $wmsUrl = $data['url'];
            error_log("Processing WMS URL: " . $wmsUrl);
            
            // Add GetCapabilities parameters if not present
            if (strpos($wmsUrl, '?') === false) {
                $wmsUrl .= '?';
            } else {
                $wmsUrl .= '&';
            }
            $wmsUrl .= 'SERVICE=WMS&REQUEST=GetCapabilities&VERSION=1.3.0';
            error_log("Full WMS URL with parameters: " . $wmsUrl);

            // Fetch WMS capabilities
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $wmsUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Note: In production, you should verify SSL
            
            $xmlString = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            error_log("WMS capabilities HTTP response code: " . $httpCode);
            error_log("WMS capabilities response length: " . strlen($xmlString));
            error_log("WMS capabilities response preview: " . substr($xmlString, 0, 500));
            
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                error_log("Curl error: " . $error);
                throw new \Exception('Failed to fetch WMS capabilities: ' . $error);
            }
            
            curl_close($ch);

            // Parse XML response
            try {
                $xml = new SimpleXMLElement($xmlString);
                error_log("Successfully parsed WMS capabilities XML");
            } catch (\Exception $e) {
                error_log("Failed to parse WMS capabilities XML: " . $e->getMessage());
                error_log("XML content: " . $xmlString);
                throw $e;
            }
            
            $xml->registerXPathNamespace('wms', 'http://www.opengis.net/wms');
            $xml->registerXPathNamespace('ows', 'http://www.opengis.net/ows/1.1');
            $xml->registerXPathNamespace('xlink', 'http://www.w3.org/1999/xlink');
            
            // Get service metadata
            $serviceMetadata = [
                'title' => (string)$xml->Service->Title,
                'abstract' => (string)$xml->Service->Abstract,
                'keywords' => [],
                'responsible_org' => (string)$xml->Service->ContactInformation->ContactPersonPrimary->ContactOrganization,
                'responsible_person' => (string)$xml->Service->ContactInformation->ContactPersonPrimary->ContactPerson,
                'role' => (string)$xml->Service->ContactInformation->ContactPosition,
                'spatial_data_service_url' => $wmsUrl
            ];

            // Extract keywords
            if (isset($xml->Service->KeywordList)) {
                foreach ($xml->Service->KeywordList->Keyword as $keyword) {
                    $serviceMetadata['keywords'][] = (string)$keyword;
                }
            }
            
            $layers = [];
            $layerNodes = $xml->xpath('//wms:Layer');
            
            foreach ($layerNodes as $layer) {
                // Skip parent layers (those without a Name element)
                if (!isset($layer->Name)) {
                    continue;
                }

                $bbox = null;
                if (isset($layer->EX_GeographicBoundingBox)) {
                    $bbox = [
                        (float)$layer->EX_GeographicBoundingBox->westBoundLongitude,
                        (float)$layer->EX_GeographicBoundingBox->southBoundLatitude,
                        (float)$layer->EX_GeographicBoundingBox->eastBoundLongitude,
                        (float)$layer->EX_GeographicBoundingBox->northBoundLatitude
                    ];
                } elseif (isset($layer->BoundingBox)) {
                    // Try to find a geographic bounding box
                    foreach ($layer->BoundingBox as $box) {
                        $crs = (string)$box['CRS'];
                        if (strpos($crs, 'EPSG:4326') !== false || strpos($crs, 'CRS:84') !== false) {
                            $bbox = [
                                (float)$box['minx'],
                                (float)$box['miny'],
                                (float)$box['maxx'],
                                (float)$box['maxy']
                            ];
                            break;
                        }
                    }
                }

                if ($bbox) {
                    // First validate that these are reasonable geographic coordinates
                    $isValid = true;
                    $coords = [
                        'west' => (float)$bbox[0],
                        'east' => (float)$bbox[2],
                        'south' => (float)$bbox[1],
                        'north' => (float)$bbox[3]
                    ];

                    // Check if coordinates are within valid geographic bounds
                    if ($coords['west'] < -180 || $coords['west'] > 180 ||
                        $coords['east'] < -180 || $coords['east'] > 180 ||
                        $coords['south'] < -90 || $coords['south'] > 90 ||
                        $coords['north'] < -90 || $coords['north'] > 90) {
                        error_log("Invalid geographic coordinates in WMS layer {$layer->Name}: " . json_encode($coords));
                        $isValid = false;
                    }

                    // Check if west is less than east and south is less than north
                    if ($coords['west'] >= $coords['east'] || $coords['south'] >= $coords['north']) {
                        error_log("Invalid bounding box order in WMS layer {$layer->Name}: " . json_encode($coords));
                        $isValid = false;
                    }

                    if ($isValid) {
                        // Round to 6 decimal places
                        $bbox = array_map(function($value) {
                            return round((float)$value, 6);
                        }, $bbox);

                        // Extract layer-specific metadata
                        $layerMetadata = [
                            'name' => (string)$layer->Name,
                            'title' => (string)$layer->Title,
                            'abstract' => (string)$layer->Abstract,
                            'keywords' => [],
                            'bbox' => $bbox
                        ];

                        // Extract layer keywords
                        if (isset($layer->KeywordList)) {
                            foreach ($layer->KeywordList->Keyword as $keyword) {
                                $layerMetadata['keywords'][] = (string)$keyword;
                            }
                        }

                        // Extract coordinate system if available
                        if (isset($layer->CRS)) {
                            $layerMetadata['coordinate_system'] = (string)$layer->CRS[0];
                        }

                        $layers[] = $layerMetadata;
                    }
                }
            }

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'service' => $serviceMetadata,
                'layers' => $layers
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            error_log('Error in WMS GetCapabilities: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            // Ensure we're sending JSON even for errors
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $_ENV['APP_ENV'] === 'development' ? $e->getTraceAsString() : null
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        } catch (\Throwable $e) {
            // Catch any other throwable (including PHP errors)
            error_log('Fatal error in WMS GetCapabilities: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Internal server error',
                'trace' => $_ENV['APP_ENV'] === 'development' ? $e->getTraceAsString() : null
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
} 