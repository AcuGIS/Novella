<?php

declare(strict_types=1);

namespace GeoLibre\Controller;

use GeoLibre\Model\HarvestSource;
use GeoLibre\Model\OaiPmh;
use GeoLibre\Model\Metadata;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use DateTime;
use Slim\Views\Twig;

class HarvestController
{
    private HarvestSource $harvestSource;
    private OaiPmh $oaiPmh;
    private Metadata $metadata;
    private Twig $twig;
    private \Doctrine\DBAL\Connection $db;
    private \GuzzleHttp\Client $client;

    public function __construct(
        HarvestSource $harvestSource, 
        OaiPmh $oaiPmh,
        Twig $twig,
        \Doctrine\DBAL\Connection $db,
        Metadata $metadata
    ) {
        $this->harvestSource = $harvestSource;
        $this->oaiPmh = $oaiPmh;
        $this->twig = $twig;
        $this->db = $db;
        $this->metadata = $metadata;
        $this->client = new \GuzzleHttp\Client([
            'timeout' => 30,
            'connect_timeout' => 10
        ]);
    }

    public function index(Request $request, Response $response): Response
    {
        $sources = $this->harvestSource->getAllSources();
        return $this->render($response, 'oai/harvest.twig', ['sources' => $sources]);
    }

    public function add(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        try {
            $this->harvestSource->createSource([
                'name' => $data['name'],
                'url' => $data['url'],
                'username' => $data['username'] ?? null,
                'password' => $data['password'] ?? null,
                'set' => $data['set'] ?? null,
                'schedule' => $data['schedule']
            ]);
            // Get the new source ID
            $newSource = $this->harvestSource->getSourceByUrl($data['url']);
            $newId = $newSource['id'] ?? null;
            if ($newId) {
                // Get the source with selected layers
                $source = $this->harvestSource->getSourceById($newId);
                $source['selected_layers'] = $this->harvestSource->getSelectedLayers($newId);
                return $this->render($response, 'oai/harvest_layers.twig', ['source' => $source]);
            }
            $this->flash('success', 'Harvest source added successfully');
        } catch (\Exception $e) {
            $this->flash('error', 'Failed to add harvest source: ' . $e->getMessage());
        }
        return $response->withHeader('Location', '/oai/harvest')->withStatus(302);
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $source = $this->harvestSource->getSourceById($id);

        if (!$source) {
            $this->flash('error', 'Harvest source not found');
            return $response->withHeader('Location', '/oai/harvest')->withStatus(302);
        }

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            try {
                $this->harvestSource->updateSource($id, [
                    'name' => $data['name'],
                    'url' => $data['url'],
                    'set' => $data['set'] ?? null,
                    'schedule' => $data['schedule']
                ]);

                // Save selected layers if provided
                if (isset($data['layers']) && is_array($data['layers'])) {
                    $this->harvestSource->setSelectedLayers($id, $data['layers']);
                }

                $this->flash('success', 'Harvest source updated successfully');
                return $response->withHeader('Location', '/oai/harvest')->withStatus(302);
            } catch (\Exception $e) {
                $this->flash('error', 'Failed to update harvest source: ' . $e->getMessage());
            }
        }

        // Get selected layers for this source
        $selectedLayers = $this->harvestSource->getSelectedLayers($id);
        $source['selected_layers'] = $selectedLayers;

        return $this->render($response, 'oai/harvest_edit.twig', ['source' => $source]);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $source = $this->harvestSource->getSourceById($id);

            if (!$source) {
                $this->flash('error', 'Harvest source not found');
                return $response->withHeader('Location', '/oai/harvest')->withStatus(302);
            }

            // Start transaction
            $this->db->beginTransaction();

            try {
                // First, get all dataset IDs associated with this harvest source
                $datasetIds = $this->db->executeQuery(
                    'SELECT DISTINCT d.id 
                     FROM datasets d 
                     JOIN oai_records r ON d.id = r.dataset_id 
                     WHERE r.harvest_source_id = ?',
                    [$id]
                )->fetchFirstColumn();

                // Delete the associated datasets first
                if (!empty($datasetIds)) {
                    // This will cascade delete related records in metadata, keywords, contacts, etc.
                    $this->db->executeStatement(
                        'DELETE FROM datasets WHERE id IN (?)',
                        [$datasetIds],
                        [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY]
                    );
                }

                // Delete the harvest source - this will cascade delete associated OAI records and logs
                $this->harvestSource->delete($id);

                $this->db->commit();
                $this->flash('success', 'Harvest source and associated datasets deleted successfully');
            } catch (\Exception $e) {
                $this->db->rollBack();
                throw $e;
            }

            return $response->withHeader('Location', '/oai/harvest')->withStatus(302);
        } catch (\Exception $e) {
            error_log("Error deleting harvest source: " . $e->getMessage());
            $this->flash('error', 'Failed to delete harvest source: ' . $e->getMessage());
            return $response->withHeader('Location', '/oai/harvest')->withStatus(302);
        }
    }

    public function run(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $source = $this->harvestSource->getSourceById($id);

        if (!$source) {
            error_log("Error: Harvest source not found for ID: " . $id);
            $this->flash('error', 'Harvest source not found');
            return $response->withHeader('Location', '/oai/harvest')->withStatus(302);
        }

        try {
            error_log("[RUN] Starting harvest for source: " . $source['name']);
            error_log("[RUN] Source URL: " . $source['url']);
            
            // Get selected layers for this source
            $selectedLayers = $this->harvestSource->getSelectedLayers($id);
            error_log("[RUN] Selected layers: " . json_encode($selectedLayers));
            
            if (empty($selectedLayers)) {
                error_log("[RUN] No layers selected for this source");
                $this->flash('error', 'No layers selected for this source. Please select layers first.');
                return $response->withHeader('Location', '/oai/harvest')->withStatus(302);
            }
            
            // Call wmsImport with the selected layers
            $params = [
                'url' => $source['url'],
                'layers' => $selectedLayers,
                'schedule' => $source['schedule']
            ];
            $importRequest = $request->withParsedBody($params);
            error_log("[RUN] Calling wmsImport...");
            $importResponse = $this->wmsImport($importRequest, $response, ['id' => $id]);
            error_log("[RUN] wmsImport called.");

            // Try to parse the response body for error or success
            $body = (string)$importResponse->getBody();
            error_log("[RUN] wmsImport response body: " . $body);
            $result = json_decode($body, true);
            if (isset($result['error'])) {
                $this->flash('error', 'Harvest error: ' . $result['error']);
            } elseif (isset($result['success']) && $result['success']) {
                $this->flash('success', 'Harvest completed successfully. Layers created: ' . ($result['created'] ?? 0));
            } elseif (!empty($body)) {
                $this->flash('success', 'Harvest completed.');
            } else {
                $this->flash('error', 'Harvest did not return a response. Please check logs.');
            }

            $this->harvestSource->updateLastHarvest($id, new DateTime());
            error_log("[RUN] Harvest completed for source: " . $source['name']);
            return $response->withHeader('Location', '/oai/harvest')->withStatus(302);
        } catch (\Exception $e) {
            error_log("[RUN] Error running harvest: " . $e->getMessage());
            $this->flash('error', 'An error occurred while running the harvest: ' . $e->getMessage());
            return $response->withHeader('Location', '/oai/harvest')->withStatus(302);
        }
    }

    public function progress(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $source = $this->harvestSource->getSourceById($id);

            if (!$source) {
                $responseData = ['error' => 'Harvest source not found'];
                $response->getBody()->write(json_encode($responseData));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            // Get the current harvest status from the database
            $stats = [
                'total_layers' => 0,
                'processed' => 0,
                'skipped' => 0,
                'errors' => 0
            ];

            // Get the total number of layers from the WMS response
            try {
                $wmsUrl = rtrim($source['url'], '/');
                $wmsUrl .= '/ows?service=WMS&version=1.3.0&request=GetCapabilities';
                
                error_log("Requesting WMS capabilities from: " . $wmsUrl);
                $wmsResponse = $this->client->get($wmsUrl, [
                    'timeout' => 10,
                    'connect_timeout' => 5
                ]);

                if ($wmsResponse->getStatusCode() === 200) {
                    $xmlContent = (string) $wmsResponse->getBody();
                    $xml = new \SimpleXMLElement($xmlContent);
                    $xml->registerXPathNamespace('wms', 'http://www.opengis.net/wms');
                    $layers = $xml->xpath('//wms:Layer');
                    // Only count layers with a <Name>
                    $namedLayers = array_filter($layers, function($layer) {
                        return !empty((string)$layer->Name);
                    });
                    $stats['total_layers'] = count($namedLayers);
                    error_log("Found " . $stats['total_layers'] . " named layers in WMS response");
                }
            } catch (\Exception $e) {
                error_log("Error getting WMS capabilities: " . $e->getMessage());
                // Continue with default stats if WMS request fails
            }

            // Get the number of processed records
            $processed = $this->oaiPmh->getProcessedCount($id);
            $stats['processed'] = $processed;

            // Get the number of skipped records
            $skipped = $this->oaiPmh->getSkippedCount($id);
            $stats['skipped'] = $skipped;

            // Get the number of errors
            $errors = $this->oaiPmh->getErrorCount($id);
            $stats['errors'] = $errors;

            // Calculate progress percentage
            $progress = 0;
            if ($stats['total_layers'] > 0) {
                $totalProcessed = $stats['processed'] + $stats['skipped'] + $stats['errors'];
                $progress = (int) min(100, round(($totalProcessed / $stats['total_layers']) * 100));
            }

            // Get the latest logs
            $logs = $this->oaiPmh->getLatestLogs($id, 50);

            // Determine status
            $status = 'in_progress';
            if ($progress >= 100) {
                $status = $errors > 0 ? 'completed_with_errors' : 'completed';
            } elseif ($errors > 0 && $progress >= 100) {
                $status = 'failed';
            }

            $responseData = [
                'status' => $status,
                'progress' => $progress,
                'message' => $this->getStatusMessage($status, $progress),
                'stats' => $stats,
                'logs' => $logs
            ];

            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            error_log("Error in progress endpoint: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            $responseData = [
                'error' => 'An error occurred while fetching progress',
                'message' => $e->getMessage()
            ];
            
            $response->getBody()->write(json_encode($responseData));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function progressView(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $source = $this->harvestSource->getSourceById($id);
        if (!$source) {
            $response->getBody()->write('Harvest source not found');
            return $response->withStatus(404);
        }
        return $this->render($response, 'oai/harvest_progress.twig', ['source' => $source]);
    }

    public function getWmsLayers(Request $request, Response $response, array $args = []): Response
    {
        try {
            error_log("=== WMS Layers Request Debug ===");
            $rawBody = (string)$request->getBody();
            error_log("Raw request body: " . $rawBody);
            
            // Try to parse the raw body as JSON
            $params = json_decode($rawBody, true);
            error_log("Decoded JSON body: " . json_encode($params));
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("JSON decode error: " . json_last_error_msg());
                // Fallback to parsed body
                $params = (array)$request->getParsedBody();
                error_log("Falling back to parsed body: " . json_encode($params));
            }
            
            $url = $params['url'] ?? null;
            error_log("Extracted URL: " . ($url ?? 'null'));
            
            if (!$url) {
                error_log("Missing URL in WMS layers request");
                $response->getBody()->write(json_encode([
                    'error' => 'Missing URL',
                    'debug' => [
                        'raw_body' => $rawBody,
                        'decoded_json' => $params,
                        'parsed_body' => (array)$request->getParsedBody()
                    ]
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Clean up the URL
            $url = rtrim($url, '/');
            error_log("Cleaned URL: " . $url);

            // Try different WMS endpoint patterns
            $wmsEndpoints = [
                $url . '/ows?service=WMS&version=1.3.0&request=GetCapabilities',
                $url . '/wms?service=WMS&version=1.3.0&request=GetCapabilities',
                $url . '/geoserver/wms?service=WMS&version=1.3.0&request=GetCapabilities',
                $url . '/geoserver/ows?service=WMS&version=1.3.0&request=GetCapabilities',
                $url . '?service=WMS&version=1.3.0&request=GetCapabilities'
            ];

            error_log("Trying WMS endpoints: " . json_encode($wmsEndpoints));

            $layers = [];
            $lastError = null;
            $lastResponse = null;

            foreach ($wmsEndpoints as $endpoint) {
                try {
                    error_log("Trying WMS endpoint: " . $endpoint);
                    $wmsResponse = $this->client->request('GET', $endpoint, [
                        'timeout' => 30,
                        'connect_timeout' => 10,
                        'headers' => [
                            'Accept' => 'application/xml, text/xml, */*'
                        ],
                        'http_errors' => false,
                        'verify' => false // Disable SSL verification for testing
                    ]);

                    $statusCode = $wmsResponse->getStatusCode();
                    error_log("Response status code: " . $statusCode);
                    error_log("Response headers: " . json_encode($wmsResponse->getHeaders()));

                    if ($statusCode === 200) {
                        $xmlContent = (string) $wmsResponse->getBody();
                        error_log("Received WMS response, length: " . strlen($xmlContent));
                        error_log("First 500 characters of response: " . substr($xmlContent, 0, 500));
                        
                        try {
                            $xml = new \SimpleXMLElement($xmlContent);
                            
                            // Register all possible WMS namespaces
                            $namespaces = $xml->getNamespaces(true);
                            foreach ($namespaces as $prefix => $uri) {
                                $xml->registerXPathNamespace($prefix ?: 'wms', $uri);
                            }
                            
                            // Try different XPath patterns to find layers
                            $layerNodes = $xml->xpath('//wms:Layer');
                            if (empty($layerNodes)) {
                                $layerNodes = $xml->xpath('//*[local-name()="Layer"]');
                            }

                            foreach ($layerNodes as $layer) {
                                $name = (string)$layer->Name;
                                $title = (string)$layer->Title;
                                
                                if (!empty($name)) {
                                    error_log("Found layer - Name: " . $name . ", Title: " . $title);
                                    $layers[] = [
                                        'name' => $name,
                                        'title' => $title ?: $name
                                    ];
                                }
                            }

                            if (!empty($layers)) {
                                error_log("Successfully found " . count($layers) . " layers using endpoint: " . $endpoint);
                                $lastResponse = $wmsResponse;
                                break;
                            } else {
                                error_log("No layers found in XML response");
                                $lastResponse = $wmsResponse;
                            }
                        } catch (\Exception $e) {
                            error_log("Error parsing XML: " . $e->getMessage());
                            $lastError = "Error parsing XML response: " . $e->getMessage();
                            $lastResponse = $wmsResponse;
                        }
                    } else {
                        error_log("WMS endpoint returned status code: " . $statusCode);
                        $lastError = "WMS endpoint returned status code: " . $statusCode;
                        $lastResponse = $wmsResponse;
                    }
                } catch (\Exception $e) {
                    error_log("Error trying endpoint " . $endpoint . ": " . $e->getMessage());
                    $lastError = $e->getMessage();
                    continue;
                }
            }
            
            if (empty($layers)) {
                error_log("No layers found for URL: " . $url . ". Last error: " . ($lastError ?? 'No specific error'));
                
                // If we got a response but no layers, include the response content in debug info
                $debugInfo = [
                    'last_error' => $lastError,
                    'tried_endpoints' => $wmsEndpoints
                ];
                
                if ($lastResponse) {
                    $debugInfo['last_response'] = [
                        'status' => $lastResponse->getStatusCode(),
                        'headers' => $lastResponse->getHeaders(),
                        'body' => substr((string)$lastResponse->getBody(), 0, 1000)
                    ];
                }
                
                $response->getBody()->write(json_encode([
                    'error' => 'No layers found in the WMS service. Please check the URL and try again.',
                    'debug' => $debugInfo
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            error_log("Found " . count($layers) . " layers for URL: " . $url);
            $response->getBody()->write(json_encode(['layers' => $layers]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            error_log("Error in getWmsLayers: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $response->getBody()->write(json_encode([
                'error' => 'Failed to get WMS layers: ' . $e->getMessage(),
                'debug' => [
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    private function getStatusMessage(string $status, int $progress): string
    {
        switch ($status) {
            case 'completed':
                return 'Harvest completed successfully';
            case 'completed_with_errors':
                return 'Harvest completed with some errors';
            case 'failed':
                return 'Harvest failed';
            default:
                return "Harvest in progress: {$progress}% complete";
        }
    }

    private function harvestSource(array $source): void
    {
        error_log("=== Starting Harvest Process for Source ===");
        error_log("Source details: " . json_encode($source));

        try {
            // Get all existing records for this harvest source
            $existingRecords = $this->db->executeQuery(
                'SELECT oai_identifier FROM oai_records WHERE harvest_source_id = ?',
                [$source['id']]
            )->fetchAllAssociative();
            $existingIdentifiers = array_column($existingRecords, 'oai_identifier');
            error_log("Found " . count($existingIdentifiers) . " existing records for harvest source");
            $this->oaiPmh->addLog($source['id'], "Found " . count($existingIdentifiers) . " existing records");

            // Initialize OAI-PMH client
            error_log("Initializing OAI-PMH client");
            $oaiPmh = new OaiPmh($this->db);

            // Clean up base URL
            $baseUrl = rtrim($source['url'], '/');
            error_log("Base URL after cleanup: " . $baseUrl);
            $this->oaiPmh->addLog($source['id'], "Connecting to WMS endpoint: " . $baseUrl);

            // Try different WMS endpoint patterns
            $wmsEndpoints = [];
            $wmsResponseBody = null;  // Initialize the variable
            
            // If URL already ends with wms.php?, just append the parameters
            if (str_ends_with($baseUrl, 'wms.php?')) {
                $wmsEndpoints[] = $baseUrl . 'service=WMS&version=1.3.0&request=GetCapabilities';
                error_log("Using direct wms.php? endpoint: " . $wmsEndpoints[0]);
            } else {
                // For other URLs, try standard patterns
                $wmsEndpoints = [
                    $baseUrl . '/ows?service=WMS&version=1.3.0&request=GetCapabilities',
                    $baseUrl . '/wms?service=WMS&version=1.3.0&request=GetCapabilities',
                    $baseUrl . '/geoserver/wms?service=WMS&version=1.3.0&request=GetCapabilities',
                    $baseUrl . '/geoserver/ows?service=WMS&version=1.3.0&request=GetCapabilities',
                    $baseUrl . '?service=WMS&version=1.3.0&request=GetCapabilities',
                    $baseUrl . '/wms.php?service=WMS&version=1.3.0&request=GetCapabilities'
                ];
                error_log("Using standard endpoints: " . implode(", ", $wmsEndpoints));
            }

            $wmsUrl = null;
            $capabilities = null;
            $wmsResponse = null;

            foreach ($wmsEndpoints as $endpoint) {
                try {
                    $this->oaiPmh->addLog($source['id'], "Trying WMS endpoint: " . $endpoint);
                    error_log("Trying WMS endpoint: " . $endpoint);
                    
                    $wmsResponse = $this->client->request('GET', $endpoint, [
                        'http_errors' => false,
                        'timeout' => 30,
                        'connect_timeout' => 10,
                        'headers' => [
                            'Accept' => 'application/xml, text/xml, */*'
                        ]
                    ]);

                    error_log("Response status code: " . $wmsResponse->getStatusCode());
                    error_log("Response headers: " . json_encode($wmsResponse->getHeaders()));
                    
                    if ($wmsResponse->getStatusCode() === 200) {
                        $wmsResponseBody = (string) $wmsResponse->getBody();
                        error_log("Raw WMS response content:");
                        error_log("----------------------------------------");
                        error_log($wmsResponseBody);
                        error_log("----------------------------------------");
                        
                        try {
                            // Try to parse as XML first
                            error_log("Attempting to parse XML response...");
                            $capabilities = new \SimpleXMLElement($wmsResponseBody);
                            error_log("Successfully parsed XML response");
                            
                            // Log the root element and its namespaces
                            error_log("Root element: " . $capabilities->getName());
                            $namespaces = $capabilities->getNamespaces(true);
                            error_log("Namespaces: " . json_encode($namespaces));
                            
                            // Register all namespaces
                            foreach ($namespaces as $prefix => $uri) {
                                error_log("Registering namespace: $prefix => $uri");
                                $capabilities->registerXPathNamespace($prefix ?: 'wms', $uri);
                            }
                            
                            // First, try to get the Capability element
                            $capability = $capabilities->xpath('//*[local-name()="Capability"]');
                            if (!empty($capability)) {
                                error_log("Found Capability element");
                                $capability = $capability[0];
                                
                                // Try to get the Layer element under Capability
                                $rootLayer = $capability->xpath('.//*[local-name()="Layer"]');
                                if (!empty($rootLayer)) {
                                    error_log("Found root Layer element");
                                    $rootLayer = $rootLayer[0];
                                    
                                    // Get all child layers
                                    $layers = $rootLayer->xpath('.//*[local-name()="Layer"]');
                                    error_log("Found " . count($layers) . " child layers");
                                    
                                    // Log details of each layer
                                    foreach ($layers as $index => $layer) {
                                        error_log("Layer " . ($index + 1) . ":");
                                        error_log("  XML: " . $layer->asXML());
                                        error_log("  Name: " . (string)$layer->Name);
                                        error_log("  Title: " . (string)$layer->Title);
                                    }
                                    
                                    if (!empty($layers)) {
                                        $wmsUrl = $endpoint;
                                        error_log("Using original endpoint URL: " . $wmsUrl);
                                        $this->oaiPmh->addLog($source['id'], "Successfully connected to WMS endpoint: " . $endpoint);
                                        error_log("Successfully connected to WMS endpoint: " . $endpoint);
                                        break;
                                    }
                                } else {
                                    error_log("No root Layer element found under Capability");
                                }
                            } else {
                                error_log("No Capability element found");
                                
                                // Try alternative approach - look for any Layer elements
                                $layers = $capabilities->xpath('//*[local-name()="Layer"]');
                                error_log("Found " . count($layers) . " layers using alternative approach");
                                
                                if (!empty($layers)) {
                                    foreach ($layers as $index => $layer) {
                                        error_log("Layer " . ($index + 1) . ":");
                                        error_log("  XML: " . $layer->asXML());
                                        error_log("  Name: " . (string)$layer->Name);
                                        error_log("  Title: " . (string)$layer->Title);
                                    }
                                    
                                    $wmsUrl = $endpoint;
                                    error_log("Using original endpoint URL: " . $wmsUrl);
                                    $this->oaiPmh->addLog($source['id'], "Successfully connected to WMS endpoint: " . $endpoint);
                                    error_log("Successfully connected to WMS endpoint: " . $endpoint);
                                    break;
                                }
                            }
                            
                            if (empty($layers)) {
                                error_log("No layers found in XML response");
                                // Try to find any layer-like content in the response
                                if (preg_match_all('/<Layer[^>]*>.*?<\/Layer>/s', $wmsResponseBody, $matches)) {
                                    error_log("Found potential layer tags in response: " . count($matches[0]));
                                    foreach ($matches[0] as $match) {
                                        error_log("Layer tag: " . $match);
                                    }
                                }
                                
                                // Try to find any XML elements that might contain layer information
                                try {
                                    $allElements = $capabilities->xpath('//*');
                                    if ($allElements !== false) {
                                        error_log("Total XML elements found: " . count($allElements));
                                        foreach ($allElements as $index => $element) {
                                            if ($index < 10) {  // Only log first 10 elements
                                                error_log("Element " . ($index + 1) . ": " . $element->getName());
                                                error_log("  Content: " . $element->asXML());
                                            }
                                        }
                                    }
                                } catch (\Exception $e) {
                                    error_log("Error getting all elements: " . $e->getMessage());
                                }
                            }
                        } catch (\Exception $e) {
                            error_log("Error parsing XML response: " . $e->getMessage());
                            error_log("Error trace: " . $e->getTraceAsString());
                            
                            // Try to find any XML-like content in the response
                            if (preg_match('/<\?xml[^>]*\?>/', $wmsResponseBody)) {
                                error_log("Response contains XML declaration");
                            }
                            
                            // Try to find any potential layer information
                            if (preg_match_all('/<[^>]*Layer[^>]*>/', $wmsResponseBody, $matches)) {
                                error_log("Found potential layer tags: " . count($matches[0]));
                                foreach ($matches[0] as $match) {
                                    error_log("Potential layer tag: " . $match);
                                }
                            }
                        }
                    } else {
                        error_log("WMS endpoint returned status code: " . $wmsResponse->getStatusCode());
                        error_log("Response body: " . (string) $wmsResponse->getBody());
                        $this->oaiPmh->addLog($source['id'], "WMS endpoint returned status code: " . $wmsResponse->getStatusCode());
                    }
                } catch (\Exception $e) {
                    error_log("Failed to connect to WMS endpoint: " . $endpoint . " - " . $e->getMessage());
                    error_log("Exception trace: " . $e->getTraceAsString());
                    $this->oaiPmh->addLog($source['id'], "Failed to connect to WMS endpoint: " . $endpoint . " - " . $e->getMessage());
                    continue;
                }
            }

            if (!$capabilities) {
                throw new \Exception("Could not connect to any WMS endpoint or no layers found in the response");
            }

            error_log("Successfully connected to WMS endpoint: " . $wmsUrl);
            error_log("Received WMS response");

            // Parse the WMS response
            $xml = new \SimpleXMLElement($wmsResponseBody);
            $xml->registerXPathNamespace('wms', 'http://www.opengis.net/wms');
            $xml->registerXPathNamespace('xlink', 'http://www.w3.org/1999/xlink');

            // Get all layers
            $layers = $xml->xpath('//wms:Layer');
            $totalLayers = count($layers);
            error_log("Found " . $totalLayers . " layers in WMS response");
            $this->oaiPmh->addLog($source['id'], "Found " . $totalLayers . " layers in WMS response");

            $processedIdentifiers = [];
            $processedCount = 0;
            $skippedCount = 0;
            $errorCount = 0;

            foreach ($layers as $layer) {
                $layerName = (string) $layer->Name;
                if (empty($layerName)) {
                    continue;
                }

                error_log("Processing layer: " . $layerName);
                $this->oaiPmh->addLog($source['id'], "Processing layer: " . $layerName);
                $processedIdentifiers[] = $layerName;

                // Check if this record needs to be updated based on last harvest time
                $needsUpdate = true;
                if ($source['last_harvest']) {
                    $lastHarvestTime = new DateTime($source['last_harvest']);
                    $record = $this->db->executeQuery(
                        'SELECT updated_at FROM oai_records WHERE oai_identifier = ? AND harvest_source_id = ?',
                        [$layerName, $source['id']]
                    )->fetchAssociative();

                    if ($record && new DateTime($record['updated_at']) <= $lastHarvestTime) {
                        error_log("Skipping unchanged record: " . $layerName);
                        $this->oaiPmh->addLog($source['id'], "Skipping unchanged record: " . $layerName);
                        $needsUpdate = false;
                        $skippedCount++;
                        $this->oaiPmh->storeRecord($layerName, '', $source['id'], 'skipped');
                    }
                }

                if (!$needsUpdate) {
                    continue;
                }

                try {
                    // Extract layer information
                    $title = (string) $layer->Title;
                    $abstract = (string) $layer->Abstract;
                    $keywords = [];
                    if ($layer->KeywordList) {
                        foreach ($layer->KeywordList->Keyword as $keyword) {
                            $keywords[] = (string) $keyword;
                        }
                    }

                    // Extract bounds
                    $bounds = null;
                    if ($layer->EX_GeographicBoundingBox) {
                        $bounds = [
                            'minx' => (float) $layer->EX_GeographicBoundingBox->westBoundLongitude,
                            'maxx' => (float) $layer->EX_GeographicBoundingBox->eastBoundLongitude,
                            'miny' => (float) $layer->EX_GeographicBoundingBox->southBoundLatitude,
                            'maxy' => (float) $layer->EX_GeographicBoundingBox->northBoundLatitude
                        ];
                        error_log("Extracted bounds for layer " . $layerName . ": " . json_encode($bounds));
                        $this->oaiPmh->addLog($source['id'], "Extracted bounds for layer " . $layerName);
                    } else {
                        error_log("No bounds found for layer " . $layerName . ", using default global extent");
                        $this->oaiPmh->addLog($source['id'], "No bounds found for layer " . $layerName . ", using default global extent");
                    }

                    // Create ISO metadata
                    $metadataXml = $this->createIsoMetadata([
                        'identifier' => $layerName,
                        'title' => $title ?: $layerName,
                        'abstract' => $abstract ?: 'No description available',
                        'keywords' => $keywords,
                        'bounds' => $bounds,
                        'serviceUrl' => $wmsUrl,
                        'serviceType' => 'WMS',
                        'serviceVersion' => '1.3.0'
                    ]);

                    error_log("Created ISO metadata for layer " . $layerName);
                    $this->oaiPmh->addLog($source['id'], "Created ISO metadata for layer " . $layerName);

                    // Store the record
                    $recordId = $oaiPmh->storeRecord($layerName, $metadataXml, $source['id'], 'active');
                    error_log("Successfully stored record for layer: " . $layerName . " with ID: " . $recordId);
                    $this->oaiPmh->addLog($source['id'], "Successfully stored record for layer: " . $layerName);

                    // Calculate quality score
                    $qualityScore = $this->metadata->calculateQualityScore($metadataXml);
                    error_log("Calculated quality score: " . $qualityScore);
                    $this->oaiPmh->addLog($source['id'], "Calculated quality score: " . $qualityScore);

                    // Get the dataset ID for this record
                    $datasetId = $this->db->executeQuery(
                        'SELECT dataset_id FROM oai_records WHERE id = ?',
                        [$recordId]
                    )->fetchOne();

                    // Update the quality score
                    $this->db->executeStatement(
                        'UPDATE metadata SET quality_score = ? WHERE dataset_id = ?',
                        [$qualityScore, $datasetId]
                    );
                    error_log("Updated quality score in database");
                    
                    // Create dataset
                    error_log("Inserting dataset for layer: " . $layerName);
                    $datasetData = [
                        'title' => $title,
                        'description' => $abstract,
                        'wms_url' => $wmsUrl,
                        'wms_layer' => $layerName,
                        'is_public' => true,
                        'status' => 'published',
                        'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                        'updated_at' => (new \DateTime())->format('Y-m-d H:i:s')
                    ];

                    // Add spatial extent if bounds are available
                    if ($bounds) {
                        $datasetData['spatial_extent'] = [
                            'type' => 'Polygon',
                            'coordinates' => [[
                                [$bounds['minx'], $bounds['miny']],
                                [$bounds['minx'], $bounds['maxy']],
                                [$bounds['maxx'], $bounds['maxy']],
                                [$bounds['maxx'], $bounds['miny']],
                                [$bounds['minx'], $bounds['miny']]
                            ]]
                        ];
                    }

                    // Create harvest source if schedule is provided
                    if (isset($source['schedule']) && !empty($source['schedule'])) {
                        $harvestSourceId = $this->db->insert('harvest_sources', [
                            'name' => $title . ' Harvest',
                            'type' => 'wms',
                            'url' => $wmsUrl,
                            'schedule' => $source['schedule'],
                            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                            'updated_at' => (new \DateTime())->format('Y-m-d H:i:s')
                        ]);
                        $harvestSourceId = $this->db->lastInsertId();
                    }

                    error_log("Dataset data: " . json_encode($datasetData));
                    
                    // Insert dataset without spatial extent first
                    $this->db->insert('datasets', $datasetData);
                    $datasetId = $this->db->lastInsertId();
                    error_log("Created dataset with ID: " . $datasetId);

                    if (!$datasetId) {
                        throw new \Exception("Failed to get dataset ID after insert");
                    }

                    // Update spatial extent if available
                    if ($bounds) {
                        try {
                            $this->db->executeStatement(
                                'UPDATE datasets SET spatial_extent = ST_GeomFromText(?, 4326) WHERE id = ?',
                                [$geometry, $datasetId]
                            );
                            error_log("Updated spatial extent for dataset ID: " . $datasetId);
                        } catch (\Exception $e) {
                            error_log("Error updating spatial extent: " . $e->getMessage());
                            // Continue processing even if spatial extent update fails
                        }
                    }

                    // Create OAI record if harvest source exists
                    if (isset($harvestSourceId)) {
                        $this->db->insert('oai_records', [
                            'harvest_source_id' => $harvestSourceId,
                            'dataset_id' => $datasetId,
                            'identifier' => $layerName,
                            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                            'updated_at' => (new \DateTime())->format('Y-m-d H:i:s')
                        ]);
                    }
                    
                    // Create metadata
                    error_log("Inserting metadata for dataset ID: " . $datasetId);
                    $metadataData = [
                        'dataset_id' => $datasetId,
                        'metadata_xml' => $metadataXml,
                        'metadata_standard' => 'ISO 19115',
                        'metadata_version' => '2018',
                        'quality_score' => $this->metadata->calculateQualityScore($metadataXml),
                        'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                        'updated_at' => (new \DateTime())->format('Y-m-d H:i:s')
                    ];
                    error_log("Metadata data length: " . strlen($metadataXml));
                    
                    $this->db->insert('metadata', $metadataData);
                    error_log("Created metadata record");
                    
                    // Calculate and store quality score
                    $qualityScore = $this->metadata->calculateQualityScore($metadataXml);
                    error_log("Calculated quality score: " . $qualityScore);
                    
                    $this->db->update('metadata', 
                        ['quality_score' => $qualityScore],
                        ['dataset_id' => $datasetId]
                    );
                    error_log("Updated quality score");
                    
                    $processedCount++;
                } catch (\Exception $e) {
                    error_log("Error processing layer " . $layerName . ": " . $e->getMessage());
                    error_log("Stack trace: " . $e->getTraceAsString());
                    $this->oaiPmh->addLog($source['id'], "Error processing layer " . $layerName . ": " . $e->getMessage());
                    $errorCount++;
                    $this->oaiPmh->storeRecord($layerName, '', $source['id'], 'error');
                }
            }

            // Mark records that are no longer present as deleted
            $deletedIdentifiers = array_diff($existingIdentifiers, $processedIdentifiers);
            foreach ($deletedIdentifiers as $identifier) {
                error_log("Marking record as deleted: " . $identifier);
                $this->oaiPmh->addLog($source['id'], "Marking record as deleted: " . $identifier);
                $oaiPmh->storeRecord($identifier, '', $source['id'], 'deleted');
            }

            error_log("=== WMS Harvest Process Completed ===");
            error_log("Summary:");
            error_log("- Total layers found: " . $totalLayers);
            error_log("- Successfully processed: " . $processedCount);
            error_log("- Skipped (unchanged): " . $skippedCount);
            error_log("- Errors: " . $errorCount);
            error_log("- Marked as deleted: " . count($deletedIdentifiers));

            $this->oaiPmh->addLog($source['id'], "=== Harvest Summary ===");
            $this->oaiPmh->addLog($source['id'], "Total layers found: " . $totalLayers);
            $this->oaiPmh->addLog($source['id'], "Successfully processed: " . $processedCount);
            $this->oaiPmh->addLog($source['id'], "Skipped (unchanged): " . $skippedCount);
            $this->oaiPmh->addLog($source['id'], "Errors: " . $errorCount);
            $this->oaiPmh->addLog($source['id'], "Marked as deleted: " . count($deletedIdentifiers));
        } catch (\Exception $e) {
            error_log("Error processing WMS response: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            error_log("Response body: " . substr($wmsResponseBody, 0, 1000));
            $this->oaiPmh->addLog($source['id'], "Fatal error: " . $e->getMessage());
            throw $e;
        }
    }

    private function createIsoMetadata(array $data): string
    {
        error_log("=== Creating ISO Metadata ===");
        error_log("Input data: " . json_encode($data));
        
        $now = new DateTime();
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
            <gmd:MD_Metadata xmlns:gmd="http://www.isotc211.org/2005/gmd"
                            xmlns:gco="http://www.isotc211.org/2005/gco"
                            xmlns:gml="http://www.opengis.net/gml/3.2"
                            xmlns:xlink="http://www.w3.org/1999/xlink">
            </gmd:MD_Metadata>');
        
        // File identifier
        $fileId = $xml->addChild('gmd:fileIdentifier', null, 'http://www.isotc211.org/2005/gmd');
        $fileId->addChild('gco:CharacterString', htmlspecialchars($data['identifier'], ENT_XML1, 'UTF-8'), 'http://www.isotc211.org/2005/gco');
        error_log("Added file identifier: " . $data['identifier']);
        
        // Language
        $language = $xml->addChild('gmd:language', null, 'http://www.isotc211.org/2005/gmd');
        $language->addChild('gco:CharacterString', 'eng', 'http://www.isotc211.org/2005/gco');
        
        // Character set
        $charSet = $xml->addChild('gmd:characterSet', null, 'http://www.isotc211.org/2005/gmd');
        $charSet->addChild('gmd:MD_CharacterSetCode', 'utf8', 'http://www.isotc211.org/2005/gmd');
        
        // Parent identifier
        $parentId = $xml->addChild('gmd:parentIdentifier', null, 'http://www.isotc211.org/2005/gmd');
        $parentId->addChild('gco:CharacterString', htmlspecialchars($data['serviceUrl'], ENT_XML1, 'UTF-8'), 'http://www.isotc211.org/2005/gco');
        
        // Hierarchy level
        $hierarchy = $xml->addChild('gmd:hierarchyLevel', null, 'http://www.isotc211.org/2005/gmd');
        $hierarchy->addChild('gmd:MD_ScopeCode', 'dataset', 'http://www.isotc211.org/2005/gmd');
        
        // Contact - Always include default contact for WMS layers
        $contact = $xml->addChild('gmd:contact', null, 'http://www.isotc211.org/2005/gmd');
        $party = $contact->addChild('gmd:CI_ResponsibleParty', null, 'http://www.isotc211.org/2005/gmd');
        $party->addChild('gmd:individualName', null, 'http://www.isotc211.org/2005/gmd')
              ->addChild('gco:CharacterString', 'GeoLibre Administrator', 'http://www.isotc211.org/2005/gco');
        $party->addChild('gmd:organisationName', null, 'http://www.isotc211.org/2005/gmd')
              ->addChild('gco:CharacterString', 'GeoLibre', 'http://www.isotc211.org/2005/gco');
        $party->addChild('gmd:positionName', null, 'http://www.isotc211.org/2005/gmd')
              ->addChild('gco:CharacterString', 'System Administrator', 'http://www.isotc211.org/2005/gco');
        $contactInfo = $party->addChild('gmd:contactInfo', null, 'http://www.isotc211.org/2005/gmd');
        $address = $contactInfo->addChild('gmd:CI_Contact', null, 'http://www.isotc211.org/2005/gmd');
        $address->addChild('gmd:electronicMailAddress', null, 'http://www.isotc211.org/2005/gmd')
                ->addChild('gco:CharacterString', 'admin@geolibre.org', 'http://www.isotc211.org/2005/gco');
        $role = $party->addChild('gmd:role', null, 'http://www.isotc211.org/2005/gmd');
        $role->addChild('gmd:CI_RoleCode', 'pointOfContact', 'http://www.isotc211.org/2005/gmd');
        error_log("Added default contact information for WMS layer");
        
        // Date stamp
        $dateStamp = $xml->addChild('gmd:dateStamp', null, 'http://www.isotc211.org/2005/gmd');
        $dateStamp->addChild('gco:DateTime', $now->format('Y-m-d\TH:i:s\Z'), 'http://www.isotc211.org/2005/gco');
        
        // Metadata standard
        $metadataStandard = $xml->addChild('gmd:metadataStandardName', null, 'http://www.isotc211.org/2005/gmd');
        $metadataStandard->addChild('gco:CharacterString', 'ISO 19115', 'http://www.isotc211.org/2005/gco');
        
        // Metadata version
        $metadataVersion = $xml->addChild('gmd:metadataStandardVersion', null, 'http://www.isotc211.org/2005/gmd');
        $metadataVersion->addChild('gco:CharacterString', '2018', 'http://www.isotc211.org/2005/gco');
        
        // Identification info
        $identificationInfo = $xml->addChild('gmd:identificationInfo', null, 'http://www.isotc211.org/2005/gmd');
        $dataIdentification = $identificationInfo->addChild('gmd:MD_DataIdentification', null, 'http://www.isotc211.org/2005/gmd');
        
        // Citation
        $citation = $dataIdentification->addChild('gmd:citation', null, 'http://www.isotc211.org/2005/gmd');
        $ciCitation = $citation->addChild('gmd:CI_Citation', null, 'http://www.isotc211.org/2005/gmd');
        
        // Title - Use layer name if title is empty
        $title = $ciCitation->addChild('gmd:title', null, 'http://www.isotc211.org/2005/gmd');
        $titleText = !empty($data['title']) ? $data['title'] : $data['identifier'];
        $title->addChild('gco:CharacterString', htmlspecialchars($titleText, ENT_XML1, 'UTF-8'), 'http://www.isotc211.org/2005/gco');
        error_log("Added title: " . $titleText);
        
        // Date
        $date = $ciCitation->addChild('gmd:date', null, 'http://www.isotc211.org/2005/gmd');
        $ciDate = $date->addChild('gmd:CI_Date', null, 'http://www.isotc211.org/2005/gmd');
        $ciDate->addChild('gmd:date', null, 'http://www.isotc211.org/2005/gmd')
               ->addChild('gco:Date', $now->format('Y-m-d'), 'http://www.isotc211.org/2005/gco');
        $ciDate->addChild('gmd:dateType', null, 'http://www.isotc211.org/2005/gmd')
               ->addChild('gmd:CI_DateTypeCode', 'publication', 'http://www.isotc211.org/2005/gmd');
        
        // Abstract - Use default if empty
        $abstract = $dataIdentification->addChild('gmd:abstract', null, 'http://www.isotc211.org/2005/gmd');
        $abstractText = !empty($data['abstract']) ? $data['abstract'] : 'WMS Layer: ' . $data['identifier'];
        $abstract->addChild('gco:CharacterString', htmlspecialchars($abstractText, ENT_XML1, 'UTF-8'), 'http://www.isotc211.org/2005/gco');
        error_log("Added abstract: " . substr($abstractText, 0, 100) . "...");
        
        // Purpose
        $purpose = $dataIdentification->addChild('gmd:purpose', null, 'http://www.isotc211.org/2005/gmd');
        $purpose->addChild('gco:CharacterString', 'WMS Layer', 'http://www.isotc211.org/2005/gco');
        
        // Status
        $status = $dataIdentification->addChild('gmd:status', null, 'http://www.isotc211.org/2005/gmd');
        $status->addChild('gmd:MD_ProgressCode', 'completed', 'http://www.isotc211.org/2005/gmd');
        
        // Keywords - Add default keywords if none provided
        $descriptiveKeywords = $dataIdentification->addChild('gmd:descriptiveKeywords', null, 'http://www.isotc211.org/2005/gmd');
        $mdKeywords = $descriptiveKeywords->addChild('gmd:MD_Keywords', null, 'http://www.isotc211.org/2005/gmd');
        if (!empty($data['keywords'])) {
            foreach ($data['keywords'] as $keyword) {
                $keywordElement = $mdKeywords->addChild('gmd:keyword', null, 'http://www.isotc211.org/2005/gmd');
                $keywordElement->addChild('gco:CharacterString', htmlspecialchars($keyword, ENT_XML1, 'UTF-8'), 'http://www.isotc211.org/2005/gco');
            }
            error_log("Added keywords: " . implode(", ", $data['keywords']));
        } else {
            // Add default keywords for WMS layers
            $defaultKeywords = ['WMS', 'Web Map Service', 'OGC', 'GIS'];
            foreach ($defaultKeywords as $keyword) {
                $keywordElement = $mdKeywords->addChild('gmd:keyword', null, 'http://www.isotc211.org/2005/gmd');
                $keywordElement->addChild('gco:CharacterString', $keyword, 'http://www.isotc211.org/2005/gco');
            }
            error_log("Added default keywords for WMS layer");
        }
        
        // Resource constraints
        $resourceConstraints = $dataIdentification->addChild('gmd:resourceConstraints', null, 'http://www.isotc211.org/2005/gmd');
        $mdConstraints = $resourceConstraints->addChild('gmd:MD_LegalConstraints', null, 'http://www.isotc211.org/2005/gmd');
        $mdConstraints->addChild('gmd:accessConstraints', null, 'http://www.isotc211.org/2005/gmd')
                     ->addChild('gmd:MD_RestrictionCode', 'otherRestrictions', 'http://www.isotc211.org/2005/gmd');
        $mdConstraints->addChild('gmd:useConstraints', null, 'http://www.isotc211.org/2005/gmd')
                     ->addChild('gmd:MD_RestrictionCode', 'otherRestrictions', 'http://www.isotc211.org/2005/gmd');
        
        // Extent - Always include spatial extent
        $extent = $dataIdentification->addChild('gmd:extent', null, 'http://www.isotc211.org/2005/gmd');
        $geographicElement = $extent->addChild('gmd:EX_Extent', null, 'http://www.isotc211.org/2005/gmd')
                                  ->addChild('gmd:geographicElement', null, 'http://www.isotc211.org/2005/gmd');
        $boundingBox = $geographicElement->addChild('gmd:EX_GeographicBoundingBox', null, 'http://www.isotc211.org/2005/gmd');
        
        if (isset($data['bounds'])) {
            $boundingBox->addChild('gmd:westBoundLongitude', null, 'http://www.isotc211.org/2005/gmd')
                       ->addChild('gco:Decimal', (string)$data['bounds']['minx'], 'http://www.isotc211.org/2005/gco');
            $boundingBox->addChild('gmd:eastBoundLongitude', null, 'http://www.isotc211.org/2005/gmd')
                       ->addChild('gco:Decimal', (string)$data['bounds']['maxx'], 'http://www.isotc211.org/2005/gco');
            $boundingBox->addChild('gmd:southBoundLatitude', null, 'http://www.isotc211.org/2005/gmd')
                       ->addChild('gco:Decimal', (string)$data['bounds']['miny'], 'http://www.isotc211.org/2005/gco');
            $boundingBox->addChild('gmd:northBoundLatitude', null, 'http://www.isotc211.org/2005/gmd')
                       ->addChild('gco:Decimal', (string)$data['bounds']['maxy'], 'http://www.isotc211.org/2005/gco');
            error_log("Added spatial extent from WMS bounds");
        } else {
            // Use default global extent if no bounds provided
            $boundingBox->addChild('gmd:westBoundLongitude', null, 'http://www.isotc211.org/2005/gmd')
                       ->addChild('gco:Decimal', '-180', 'http://www.isotc211.org/2005/gco');
            $boundingBox->addChild('gmd:eastBoundLongitude', null, 'http://www.isotc211.org/2005/gmd')
                       ->addChild('gco:Decimal', '180', 'http://www.isotc211.org/2005/gco');
            $boundingBox->addChild('gmd:southBoundLatitude', null, 'http://www.isotc211.org/2005/gmd')
                       ->addChild('gco:Decimal', '-90', 'http://www.isotc211.org/2005/gco');
            $boundingBox->addChild('gmd:northBoundLatitude', null, 'http://www.isotc211.org/2005/gmd')
                       ->addChild('gco:Decimal', '90', 'http://www.isotc211.org/2005/gco');
            error_log("Added default global spatial extent");
        }
        
        // Temporal extent - Always include current date
        $temporalElement = $extent->addChild('gmd:EX_Extent', null, 'http://www.isotc211.org/2005/gmd')
                                ->addChild('gmd:temporalElement', null, 'http://www.isotc211.org/2005/gmd');
        $temporalExtent = $temporalElement->addChild('gmd:EX_TemporalExtent', null, 'http://www.isotc211.org/2005/gmd');
        $extent = $temporalExtent->addChild('gmd:extent', null, 'http://www.isotc211.org/2005/gmd');
        $timePeriod = $extent->addChild('gml:TimePeriod', null, 'http://www.opengis.net/gml/3.2');
        $timePeriod->addAttribute('gml:id', 'TP1');
        $timePeriod->addChild('gml:beginPosition', $now->format('Y-m-d'), 'http://www.opengis.net/gml/3.2');
        $timePeriod->addChild('gml:endPosition', $now->format('Y-m-d'), 'http://www.opengis.net/gml/3.2');
        error_log("Added temporal extent with current date");
        
        // Distribution info - Always include WMS service info
        $distribution = $xml->addChild('gmd:distributionInfo', null, 'http://www.isotc211.org/2005/gmd');
        $mdDistribution = $distribution->addChild('gmd:MD_Distribution', null, 'http://www.isotc211.org/2005/gmd');
        $transferOptions = $mdDistribution->addChild('gmd:transferOptions', null, 'http://www.isotc211.org/2005/gmd');
        $mdDigitalTransferOptions = $transferOptions->addChild('gmd:MD_DigitalTransferOptions', null, 'http://www.isotc211.org/2005/gmd');
        $onLine = $mdDigitalTransferOptions->addChild('gmd:onLine', null, 'http://www.isotc211.org/2005/gmd');
        $ciOnlineResource = $onLine->addChild('gmd:CI_OnlineResource', null, 'http://www.isotc211.org/2005/gmd');
        $ciOnlineResource->addChild('gmd:linkage', null, 'http://www.isotc211.org/2005/gmd')
                        ->addChild('gmd:URL', htmlspecialchars($data['serviceUrl'], ENT_XML1, 'UTF-8'), 'http://www.isotc211.org/2005/gmd');
        $ciOnlineResource->addChild('gmd:protocol', null, 'http://www.isotc211.org/2005/gmd')
                        ->addChild('gco:CharacterString', 'OGC:WMS', 'http://www.isotc211.org/2005/gco');
        $ciOnlineResource->addChild('gmd:name', null, 'http://www.isotc211.org/2005/gmd')
                        ->addChild('gco:CharacterString', htmlspecialchars($data['identifier'], ENT_XML1, 'UTF-8'), 'http://www.isotc211.org/2005/gco');
        $ciOnlineResource->addChild('gmd:description', null, 'http://www.isotc211.org/2005/gmd')
                        ->addChild('gco:CharacterString', htmlspecialchars($titleText, ENT_XML1, 'UTF-8'), 'http://www.isotc211.org/2005/gco');
        $ciOnlineResource->addChild('gmd:function', null, 'http://www.isotc211.org/2005/gmd')
                        ->addChild('gmd:CI_OnLineFunctionCode', 'download', 'http://www.isotc211.org/2005/gmd');
        error_log("Added distribution info with WMS service URL");
        
        // Add debug logging for the XML
        error_log("Generated XML for quality score calculation: " . $xml->asXML());
        error_log("=== End Creating ISO Metadata ===");
        
        return $xml->asXML();
    }

    private function render(Response $response, string $template, array $data = []): Response
    {
        return $this->twig->render($response, $template, $data);
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    public function layersSelectPage(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $source = $this->harvestSource->getSourceById($id);
        if (!$source) {
            $this->flash('error', 'Harvest source not found');
            return $response->withHeader('Location', '/oai/harvest')->withStatus(302);
        }

        // Get selected layers for this source
        $selectedLayers = $this->harvestSource->getSelectedLayers($id);
        $source['selected_layers'] = $selectedLayers;

        return $this->render($response, 'oai/harvest_layers.twig', ['source' => $source]);
    }

    public function wmsImport(Request $request, Response $response, array $args = []): Response
    {
        error_log('[WMSIMPORT] wmsImport called with args: ' . json_encode($args));
        try {
            error_log("=== WMS Import Request Debug ===");
            $rawBody = (string)$request->getBody();
            error_log("Raw request body: " . $rawBody);
            
            // Try to parse the raw body as JSON
            $params = json_decode($rawBody, true);
            error_log("Decoded JSON body: " . json_encode($params));
            
            // Fallback to parsed body if JSON is invalid or empty
            if (json_last_error() !== JSON_ERROR_NONE || !$params) {
                $params = (array)$request->getParsedBody();
                error_log("Falling back to parsed body: " . json_encode($params));
            }
            
            $url = $params['url'] ?? null;
            error_log("Extracted URL: " . ($url ?? 'null'));
            
            if (!$url) {
                error_log("Missing URL in WMS layers request");
                $response->getBody()->write(json_encode([
                    'error' => 'Missing URL',
                    'debug' => [
                        'raw_body' => $rawBody,
                        'decoded_json' => $params,
                        'parsed_body' => (array)$request->getParsedBody()
                    ]
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (empty($params['layers'])) {
                error_log("No layers selected for import");
                $response->getBody()->write(json_encode([
                    'error' => 'No layers selected for import',
                    'debug' => [
                        'raw_body' => $rawBody,
                        'decoded_json' => $params
                    ]
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Get WMS capabilities to extract layer information
            $wmsUrl = rtrim($url, '/');
            $capabilitiesUrl = $wmsUrl . '/ows?service=WMS&version=1.3.0&request=GetCapabilities';
            error_log("Fetching WMS capabilities from: " . $capabilitiesUrl);
            
            try {
                $wmsResponse = $this->client->request('GET', $capabilitiesUrl, [
                    'timeout' => 30,
                    'connect_timeout' => 10,
                    'headers' => [
                        'Accept' => 'application/xml, text/xml, */*'
                    ],
                    'http_errors' => false,
                    'verify' => false
                ]);

                if ($wmsResponse->getStatusCode() !== 200) {
                    throw new \Exception("Failed to get WMS capabilities. Status code: " . $wmsResponse->getStatusCode());
                }

                $xmlContent = (string) $wmsResponse->getBody();
                error_log("WMS capabilities response length: " . strlen($xmlContent));
                error_log("First 500 characters of WMS response: " . substr($xmlContent, 0, 500));
                
                $xml = new \SimpleXMLElement($xmlContent);
                
                // Register all possible WMS namespaces
                $namespaces = $xml->getNamespaces(true);
                foreach ($namespaces as $prefix => $uri) {
                    $xml->registerXPathNamespace($prefix ?: 'wms', $uri);
                }

                // Get the WMS endpoint URL from capabilities
                $wmsEndpointUrl = $wmsUrl . '/geoserver/wms';
                if ($xml->Capability->Request->GetMap->DCPType->HTTP->Get->OnlineResource) {
                    $endpointUrl = (string)$xml->Capability->Request->GetMap->DCPType->HTTP->Get->OnlineResource['xlink:href'];
                    // If the endpoint URL is relative, make it absolute
                    if (strpos($endpointUrl, 'http') !== 0) {
                        $wmsEndpointUrl = rtrim($wmsUrl, '/') . '/' . ltrim($endpointUrl, '/');
                    } else {
                        $wmsEndpointUrl = $endpointUrl;
                    }
                }
                error_log("Using WMS endpoint URL: " . $wmsEndpointUrl);
            } catch (\Exception $e) {
                error_log("Error fetching WMS capabilities: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                throw new \Exception("Failed to get WMS capabilities: " . $e->getMessage());
            }

            // Start transaction
            $this->db->beginTransaction();
            
            try {
                $created = 0;
                $errors = [];
                
                foreach ($params['layers'] as $layerName) {
                    try {
                        error_log("Processing layer: " . $layerName);
                        
                        // Find layer in WMS capabilities
                        $layerNodes = $xml->xpath("//wms:Layer[wms:Name='{$layerName}']");
                        if (empty($layerNodes)) {
                            $layerNodes = $xml->xpath("//*[local-name()='Layer'][*[local-name()='Name']='{$layerName}']");
                        }
                        
                        if (empty($layerNodes)) {
                            throw new \Exception("Layer not found in WMS capabilities");
                        }
                        
                        $layer = $layerNodes[0];
                        $title = (string)$layer->Title ?: $layerName;
                        $abstract = (string)$layer->Abstract ?: 'No description available';
                        
                        error_log("Layer details - Title: " . $title . ", Abstract: " . substr($abstract, 0, 100));
                        
                        // Extract keywords
                        $keywords = [];
                        if ($layer->KeywordList) {
                            foreach ($layer->KeywordList->Keyword as $keyword) {
                                $keywords[] = (string)$keyword;
                            }
                        }
                        error_log("Extracted keywords: " . implode(", ", $keywords));
                        
                        // Extract bounds and convert to geometry
                        $bounds = null;
                        $geometry = null;
                        if ($layer->EX_GeographicBoundingBox) {
                            $bounds = [
                                'minx' => (float) $layer->EX_GeographicBoundingBox->westBoundLongitude,
                                'maxx' => (float) $layer->EX_GeographicBoundingBox->eastBoundLongitude,
                                'miny' => (float) $layer->EX_GeographicBoundingBox->southBoundLatitude,
                                'maxy' => (float) $layer->EX_GeographicBoundingBox->northBoundLatitude
                            ];
                            
                            // Convert bounds to WKT polygon
                            $wkt = sprintf(
                                'POLYGON((%f %f, %f %f, %f %f, %f %f, %f %f))',
                                $bounds['minx'], $bounds['miny'],
                                $bounds['maxx'], $bounds['miny'],
                                $bounds['maxx'], $bounds['maxy'],
                                $bounds['minx'], $bounds['maxy'],
                                $bounds['minx'], $bounds['miny']
                            );
                            $geometry = $wkt;
                            error_log("Extracted bounds and created geometry: " . $geometry);
                        } else {
                            error_log("No bounds found for layer");
                        }
                        
                        // Create ISO metadata
                        $metadataXml = $this->createIsoMetadata([
                            'identifier' => $layerName,
                            'title' => $title,
                            'abstract' => $abstract,
                            'keywords' => $keywords,
                            'bounds' => $bounds,
                            'serviceUrl' => $wmsEndpointUrl,
                            'serviceType' => 'WMS',
                            'serviceVersion' => '1.3.0'
                        ]);
                        error_log("Created ISO metadata XML");

                        // Create harvest source if it doesn't exist
                        $harvestSourceId = null;
                        $existingSource = $this->db->executeQuery(
                            'SELECT id FROM harvest_sources WHERE url = ?',
                            [$url]
                        )->fetchAssociative();

                        if ($existingSource) {
                            $harvestSourceId = $existingSource['id'];
                            error_log("Using existing harvest source ID: " . $harvestSourceId);
                            // Update schedule if it has changed
                            $this->db->executeStatement(
                                'UPDATE harvest_sources SET schedule = ? WHERE id = ?',
                                [$params['schedule'], $harvestSourceId]
                            );
                        } else {
                            $this->db->insert('harvest_sources', [
                                'name' => 'WMS Import - ' . parse_url($url, PHP_URL_HOST),
                                'url' => $url,
                                'type' => 'wms',
                                'schedule' => $params['schedule'],
                                'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                                'updated_at' => (new \DateTime())->format('Y-m-d H:i:s')
                            ]);
                            $harvestSourceId = $this->db->lastInsertId();
                            error_log("Created new harvest source with ID: " . $harvestSourceId);
                        }

                        // Create dataset
                        $datasetData = [
                            'title' => $title,
                            'description' => $abstract,
                            'wms_url' => $wmsEndpointUrl,
                            'wms_layer' => $layerName,
                            'is_public' => true,
                            'status' => 'published',
                            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                            'updated_at' => (new \DateTime())->format('Y-m-d H:i:s')
                        ];
                        error_log("Inserting dataset with data: " . json_encode($datasetData));

                        // Insert dataset without spatial extent first
                        $this->db->insert('datasets', $datasetData);
                        $datasetId = $this->db->lastInsertId();
                        error_log("Created dataset with ID: " . $datasetId);

                        if (!$datasetId) {
                            throw new \Exception("Failed to get dataset ID after insert");
                        }

                        // Update spatial extent if available
                        if ($geometry) {
                            try {
                                $this->db->executeStatement(
                                    'UPDATE datasets SET spatial_extent = ST_GeomFromText(?, 4326) WHERE id = ?',
                                    [$geometry, $datasetId]
                                );
                                error_log("Updated spatial extent for dataset ID: " . $datasetId);
                            } catch (\Exception $e) {
                                error_log("Error updating spatial extent: " . $e->getMessage());
                                // Continue processing even if spatial extent update fails
                            }
                        }

                        // Create OAI record
                        $oaiData = [
                            'harvest_source_id' => $harvestSourceId,
                            'dataset_id' => $datasetId,
                            'oai_identifier' => $layerName,
                            'status' => 'active',
                            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                            'updated_at' => (new \DateTime())->format('Y-m-d H:i:s')
                        ];
                        error_log("Inserting OAI record with data: " . json_encode($oaiData));

                        $this->db->insert('oai_records', $oaiData);
                        error_log("Created OAI record");

                        // Create metadata
                        $metadataData = [
                            'dataset_id' => $datasetId,
                            'metadata_xml' => $metadataXml,
                            'metadata_standard' => 'ISO 19115',
                            'metadata_version' => '2018',
                            'quality_score' => $this->metadata->calculateQualityScore($metadataXml),
                            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                            'updated_at' => (new \DateTime())->format('Y-m-d H:i:s')
                        ];
                        error_log("Inserting metadata with data: " . json_encode($metadataData));
                        
                        $this->db->insert('metadata', $metadataData);
                        error_log("Created metadata record");
                        
                        $created++;
                        error_log("Successfully processed layer: " . $layerName);
                    } catch (\Exception $e) {
                        error_log("Error processing layer {$layerName}: " . $e->getMessage());
                        error_log("Stack trace: " . $e->getTraceAsString());
                        $errors[] = "Error processing layer {$layerName}: " . $e->getMessage();
                    }
                }

                if ($created > 0) {
                    $this->db->commit();
                    error_log("Transaction committed successfully");
                } else {
                    $this->db->rollBack();
                    error_log("No layers were created, rolling back transaction");
                }

                $response->getBody()->write(json_encode([
                    'success' => true,
                    'created' => $created,
                    'errors' => $errors
                ]));
                return $response->withHeader('Content-Type', 'application/json');
            } catch (\Exception $e) {
                $this->db->rollBack();
                error_log("Transaction rolled back due to error: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                throw $e;
            }
        } catch (\Exception $e) {
            error_log("Error in wmsImport: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function saveLayers(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $data = $request->getParsedBody();
            
            if (!isset($data['layers']) || !is_array($data['layers'])) {
                $this->flash('error', 'No layers selected');
                return $response->withHeader('Location', "/oai/harvest/{$id}/layers")->withStatus(302);
            }

            // Update schedule if provided
            if (isset($data['schedule'])) {
                $this->harvestSource->updateSource($id, ['schedule' => $data['schedule']]);
            }

            // Save selected layers
            if ($this->harvestSource->setSelectedLayers($id, $data['layers'])) {
                $this->flash('success', 'Layer settings saved successfully. You can now run the harvest from the main page.');
            } else {
                $this->flash('error', 'Failed to save layer settings');
            }

            return $response->withHeader('Location', '/oai/harvest')->withStatus(302);
        } catch (\Exception $e) {
            error_log("Error saving layers: " . $e->getMessage());
            $this->flash('error', 'An error occurred while saving layer settings');
            return $response->withHeader('Location', '/oai/harvest')->withStatus(302);
        }
    }
} 