<?php

declare(strict_types=1);

namespace GeoLibre\Controller;

use GeoLibre\Model\Metadata;
use GeoLibre\Model\Dataset;
use GeoLibre\Model\MetadataTemplate;
use GeoLibre\Model\GisData;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use DateTime;
use Slim\Exception\HttpNotFoundException;

class MetadataController
{
    private Metadata $metadata;
    private Dataset $dataset;
    private MetadataTemplate $template;
    private Twig $twig;
    private GisData $gisData;

    public function __construct(Metadata $metadata, Dataset $dataset, MetadataTemplate $template, Twig $twig, GisData $gisData)
    {
        $this->metadata = $metadata;
        $this->dataset = $dataset;
        $this->template = $template;
        $this->twig = $twig;
        $this->gisData = $gisData;
    }

    public function index(Request $request, Response $response): Response
    {
        try {
            $templates = $this->template->getAll();
            return $this->twig->render($response, 'metadata/index.twig', [
                'templates' => $templates
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to load templates: ' . $e->getMessage());
            $this->flash->addMessage('error', 'Failed to load templates. Please try again.');
            return $response->withRedirect('/catalog');
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $dataset = $this->dataset->getWithMetadata((int)$args['id']);
        
        if (!$dataset) {
            throw new HttpNotFoundException($request, 'Dataset not found');
        }
        
        // Set is_harvested flag using the same method as CatalogController
        $dataset['is_harvested'] = $this->gisData->isHarvestedDataset($dataset['id']);
        
        return $this->twig->render($response, 'metadata/show.twig', [
            'dataset' => $dataset
        ]);
    }

    public function getIso19115(Request $request, Response $response, array $args): Response
    {
        $datasetId = (int) $args['id'];
        $metadata = $this->metadata->getByDatasetId($datasetId);

        if (!$metadata) {
            return $this->jsonResponse($response, ['error' => 'Metadata not found'], 404);
        }

        return $this->jsonResponse($response, [
            'id' => $metadata['id'],
            'dataset_id' => $metadata['dataset_id'],
            'title' => $metadata['title'],
            'description' => $metadata['description'],
            'metadata_standard' => $metadata['metadata_standard'],
            'metadata_version' => $metadata['metadata_version'],
            'metadata_xml' => $metadata['metadata_xml']
        ]);
    }

    public function createIso19115(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        if (!isset($data['dataset_id']) || !isset($data['metadata_xml'])) {
            return $this->jsonResponse($response, [
                'error' => 'Missing required fields: dataset_id and metadata_xml'
            ], 400);
        }

        try {
            // Validate dataset exists
            $dataset = $this->dataset->find((int) $data['dataset_id']);
            if (!$dataset) {
                return $this->jsonResponse($response, [
                    'error' => 'Dataset not found'
                ], 404);
            }

            // Validate required metadata fields
            $missingFields = $this->metadata->validateRequiredFields($data['metadata_xml']);
            if (!empty($missingFields)) {
                return $this->jsonResponse($response, [
                    'error' => 'Missing required metadata fields: ' . implode(', ', $missingFields),
                    'missing_fields' => $missingFields
                ], 400);
            }

            // Calculate metadata quality score
            $qualityScore = $this->metadata->calculateQualityScore($data['metadata_xml']);

            // Extract spatial and temporal extents
            $spatialExtent = $this->metadata->extractSpatialExtent($data['metadata_xml']);
            $temporalExtent = $this->metadata->extractTemporalExtent($data['metadata_xml']);

            // Update dataset with extents
            if ($spatialExtent) {
                $this->dataset->updateDataset($data['dataset_id'], [
                    'spatial_extent' => [
                        'type' => 'Polygon',
                        'coordinates' => [[
                            [$spatialExtent['west'], $spatialExtent['south']],
                            [$spatialExtent['east'], $spatialExtent['south']],
                            [$spatialExtent['east'], $spatialExtent['north']],
                            [$spatialExtent['west'], $spatialExtent['north']],
                            [$spatialExtent['west'], $spatialExtent['south']]
                        ]]
                    ]
                ]);
            }

            if ($temporalExtent) {
                $this->dataset->updateDataset($data['dataset_id'], [
                    'temporal_extent' => [
                        'start' => $temporalExtent['start'],
                        'end' => $temporalExtent['end']
                    ]
                ]);
            }

            // Create metadata
            $metadataId = $this->metadata->createMetadata([
                'dataset_id' => (int) $data['dataset_id'],
                'metadata_xml' => $data['metadata_xml'],
                'metadata_standard' => $data['metadata_standard'] ?? 'ISO 19115',
                'metadata_version' => $data['metadata_version'] ?? '2018',
                'quality_score' => $qualityScore
            ]);

            // Extract and store keywords
            $keywords = $this->metadata->extractKeywords($data['metadata_xml']);
            foreach ($keywords as $keyword) {
                $this->dataset->create([
                    'dataset_id' => $data['dataset_id'],
                    'keyword' => $keyword
                ]);
            }

            // Extract and store contacts
            $contacts = $this->metadata->extractContacts($data['metadata_xml']);
            foreach ($contacts as $contact) {
                $this->dataset->create([
                    'dataset_id' => $data['dataset_id'],
                    'role' => $contact['role'] ?? null,
                    'organization' => $contact['organization'] ?? null,
                    'individual_name' => $contact['individual_name'] ?? null,
                    'position_name' => $contact['position_name'] ?? null,
                    'email' => $contact['email'] ?? null
                ]);
            }

            return $this->jsonResponse($response, [
                'id' => $metadataId,
                'quality_score' => $qualityScore,
                'message' => 'Metadata created successfully'
            ], 201);
        } catch (\Exception $e) {
            return $this->jsonResponse($response, [
                'error' => 'Failed to create metadata: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateIso19115(Request $request, Response $response, array $args): Response
    {
        $datasetId = (int) $args['id'];
        $data = $request->getParsedBody();

        if (!isset($data['metadata_xml'])) {
            return $this->jsonResponse($response, [
                'error' => 'Missing required field: metadata_xml'
            ], 400);
        }

        try {
            // Validate dataset exists
            $dataset = $this->dataset->find($datasetId);
            if (!$dataset) {
                return $this->jsonResponse($response, [
                    'error' => 'Dataset not found'
                ], 404);
            }

            // Validate required metadata fields
            $missingFields = $this->metadata->validateRequiredFields($data['metadata_xml']);
            if (!empty($missingFields)) {
                return $this->jsonResponse($response, [
                    'error' => 'Missing required metadata fields: ' . implode(', ', $missingFields),
                    'missing_fields' => $missingFields
                ], 400);
            }

            // Calculate metadata quality score
            $qualityScore = $this->metadata->calculateQualityScore($data['metadata_xml']);

            // Extract spatial and temporal extents
            $spatialExtent = $this->metadata->extractSpatialExtent($data['metadata_xml']);
            $temporalExtent = $this->metadata->extractTemporalExtent($data['metadata_xml']);

            // Update dataset with extents
            if ($spatialExtent) {
                $this->dataset->updateDataset($datasetId, [
                    'spatial_extent' => [
                        'type' => 'Polygon',
                        'coordinates' => [[
                            [$spatialExtent['west'], $spatialExtent['south']],
                            [$spatialExtent['east'], $spatialExtent['south']],
                            [$spatialExtent['east'], $spatialExtent['north']],
                            [$spatialExtent['west'], $spatialExtent['north']],
                            [$spatialExtent['west'], $spatialExtent['south']]
                        ]]
                    ]
                ]);
            }

            if ($temporalExtent) {
                $this->dataset->updateDataset($datasetId, [
                    'temporal_extent' => [
                        'start' => $temporalExtent['start'],
                        'end' => $temporalExtent['end']
                    ]
                ]);
            }

            // Update metadata
            $success = $this->metadata->updateMetadata($datasetId, [
                'metadata_xml' => $data['metadata_xml'],
                'metadata_version' => $data['metadata_version'] ?? '2018',
                'quality_score' => $qualityScore
            ]);

            if (!$success) {
                return $this->jsonResponse($response, [
                    'error' => 'Failed to update metadata'
                ], 500);
            }

            // Update keywords
            $keywords = $this->metadata->extractKeywords($data['metadata_xml']);
            $this->dataset->delete($datasetId); // Delete existing keywords
            foreach ($keywords as $keyword) {
                $this->dataset->create([
                    'dataset_id' => $datasetId,
                    'keyword' => $keyword
                ]);
            }

            // Update contacts
            $contacts = $this->metadata->extractContacts($data['metadata_xml']);
            $this->dataset->delete($datasetId); // Delete existing contacts
            foreach ($contacts as $contact) {
                $this->dataset->create([
                    'dataset_id' => $datasetId,
                    'role' => $contact['role'] ?? null,
                    'organization' => $contact['organization'] ?? null,
                    'individual_name' => $contact['individual_name'] ?? null,
                    'position_name' => $contact['position_name'] ?? null,
                    'email' => $contact['email'] ?? null
                ]);
            }

            return $this->jsonResponse($response, [
                'quality_score' => $qualityScore,
                'message' => 'Metadata updated successfully'
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse($response, [
                'error' => 'Failed to update metadata: ' . $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $results = [];

        try {
            if (isset($params['keyword'])) {
                $results = $this->metadata->getMetadataByKeyword($params['keyword']);
            } elseif (isset($params['contact'])) {
                $results = $this->metadata->getMetadataByContact($params['contact']);
            } elseif (isset($params['spatial_extent'])) {
                $results = $this->metadata->getMetadataBySpatialExtent($params['spatial_extent']);
            } elseif (isset($params['temporal_start']) && isset($params['temporal_end'])) {
                $start = new DateTime($params['temporal_start']);
                $end = new DateTime($params['temporal_end']);
                $results = $this->metadata->getMetadataByTemporalExtent($start, $end);
            } else {
                return $this->jsonResponse($response, [
                    'error' => 'No search criteria provided'
                ], 400);
            }

            return $this->jsonResponse($response, [
                'count' => count($results),
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse($response, [
                'error' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function templates(Request $request, Response $response): Response
    {
        try {
            error_log('Starting templates method');
            error_log('Template object: ' . get_class($this->template));
            error_log('Twig object: ' . get_class($this->twig));
            
            $templates = $this->template->getAll();
            error_log('Templates retrieved: ' . print_r($templates, true));
            
            error_log('About to render view: catalog/templates/index.twig');
            $result = $this->twig->render($response, 'catalog/templates/index.twig', [
                'templates' => $templates
            ]);
            error_log('View rendered successfully');
            return $result;
        } catch (\Exception $e) {
            error_log('Error in templates method: ' . $e->getMessage());
            error_log('Error class: ' . get_class($e));
            error_log('Stack trace: ' . $e->getTraceAsString());
            $this->flash->addMessage('error', 'Failed to load templates: ' . $e->getMessage());
            return $response->withHeader('Location', '/catalog')->withStatus(302);
        }
    }

    public function templateShow(Request $request, Response $response, array $args): Response
    {
        $templateId = (int) $args['id'];
        $template = $this->template->getById($templateId);
        
        if (!$template) {
            $this->flash->addMessage('error', 'Template not found');
            return $response->withHeader('Location', '/catalog/templates')->withStatus(302);
        }

        $fields = $this->template->getFields($templateId);
        
        return $this->twig->render($response, 'catalog/templates/show.twig', [
            'template' => $template,
            'fields' => $fields
        ]);
    }

    public function templateNew(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'catalog/templates/new.twig');
    }

    public function templateCreate(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        if (!isset($data['name']) || !isset($data['metadata_standard']) || !isset($data['metadata_version']) || !isset($data['template_xml'])) {
            $this->flash->addMessage('error', 'Missing required fields');
            return $response->withHeader('Location', '/catalog/templates/new')->withStatus(302);
        }

        try {
            // Start transaction
            $this->db->beginTransaction();

            // Create template
            $templateId = $this->template->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'metadata_standard' => $data['metadata_standard'],
                'metadata_version' => $data['metadata_version'],
                'template_xml' => $data['template_xml'],
                'is_default' => $data['is_default'] ?? false
            ]);

            // Create template fields
            if (isset($data['fields']) && is_array($data['fields'])) {
                foreach ($data['fields'] as $field) {
                    if (!empty($field['name']) && !empty($field['path']) && !empty($field['type'])) {
                        $this->template->createField([
                            'template_id' => $templateId,
                            'field_name' => $field['name'],
                            'field_path' => $field['path'],
                            'field_type' => $field['type'],
                            'is_required' => $field['required'] ?? false,
                            'description' => $field['description'] ?? null
                        ]);
                    }
                }
            }

            // Commit transaction
            $this->db->commit();

            $this->flash->addMessage('success', 'Template created successfully');
            return $response->withHeader('Location', '/catalog/templates/' . $templateId)->withStatus(302);
        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->db->rollBack();
            $this->flash->addMessage('error', 'Failed to create template: ' . $e->getMessage());
            return $response->withHeader('Location', '/catalog/templates/new')->withStatus(302);
        }
    }

    public function templateEdit(Request $request, Response $response, array $args): Response
    {
        $templateId = (int) $args['id'];
        $template = $this->template->getById($templateId);
        
        if (!$template) {
            $this->flash->addMessage('error', 'Template not found');
            return $response->withHeader('Location', '/catalog/templates')->withStatus(302);
        }

        $fields = $this->template->getFields($templateId);
        
        return $this->twig->render($response, 'catalog/templates/edit.twig', [
            'template' => $template,
            'fields' => $fields
        ]);
    }

    public function templateUpdate(Request $request, Response $response, array $args): Response
    {
        $templateId = (int) $args['id'];
        $data = $request->getParsedBody();
        
        // Debug logging
        error_log('Template Update - Received data: ' . print_r($data, true));
        error_log('Template Update - Request method: ' . $request->getMethod());
        error_log('Template Update - Request URI: ' . $request->getUri()->getPath());
        error_log('Template Update - is_default value: ' . (isset($data['is_default']) ? $data['is_default'] : 'not set'));
        
        if (!isset($data['name']) || !isset($data['metadata_standard']) || !isset($data['metadata_version'])) {
            $this->flash->addMessage('error', 'Missing required fields');
            return $response->withHeader('Location', '/catalog/templates/' . $templateId . '/edit')->withStatus(302);
        }

        try {
            // Prepare update data with is_default field
            $updateData = [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'metadata_standard' => $data['metadata_standard'],
                'metadata_version' => $data['metadata_version'],
                'template_xml' => $data['template_xml'] ?? '',
                'is_default' => (bool)($data['is_default'] ?? false)  // Use consistent boolean conversion
            ];
            
            // Debug logging
            error_log('Template Update - Update data: ' . print_r($updateData, true));
            error_log('Template Update - is_default type: ' . gettype($updateData['is_default']));
            
            // If this template is being set as default, first unset any existing default
            if ($updateData['is_default']) {
                $this->template->setDefaultTemplate($templateId);
            }
            
            // Update the template
            $success = $this->template->update($templateId, $updateData);

            if (!$success) {
                $this->flash->addMessage('error', 'Failed to update template');
                return $response->withHeader('Location', '/catalog/templates/' . $templateId . '/edit')->withStatus(302);
            }

            $this->flash->addMessage('success', 'Template updated successfully');
            return $response->withHeader('Location', '/catalog/templates/' . $templateId)->withStatus(302);
        } catch (\Exception $e) {
            error_log('Template Update - Error: ' . $e->getMessage());
            error_log('Template Update - Stack trace: ' . $e->getTraceAsString());
            $this->flash->addMessage('error', 'Failed to update template: ' . $e->getMessage());
            return $response->withHeader('Location', '/catalog/templates/' . $templateId . '/edit')->withStatus(302);
        }
    }

    public function templateDelete(Request $request, Response $response, array $args): Response
    {
        $templateId = (int) $args['id'];
        
        try {
            $success = $this->template->delete($templateId);
            
            if ($success) {
                $this->flash->addMessage('success', 'Template deleted successfully');
            } else {
                $this->flash->addMessage('error', 'Failed to delete template');
            }
        } catch (\Exception $e) {
            $this->flash->addMessage('error', 'Failed to delete template: ' . $e->getMessage());
        }
        
        return $response->withHeader('Location', '/catalog/templates')->withStatus(302);
    }

    public function templateSetDefault(Request $request, Response $response, array $args): Response
    {
        $templateId = (int) $args['id'];
        
        try {
            $success = $this->template->setDefaultTemplate($templateId);
            
            if ($success) {
                $this->flash->addMessage('success', 'Default template updated successfully');
            } else {
                $this->flash->addMessage('error', 'Failed to update default template');
            }
        } catch (\Exception $e) {
            $this->flash->addMessage('error', 'Failed to update default template: ' . $e->getMessage());
        }
        
        return $response->withHeader('Location', '/catalog/templates/' . $templateId)->withStatus(302);
    }

    public function create(Request $request, Response $response): Response
    {
        $templates = $this->template->getAll();
        $defaultTemplate = $this->template->getDefaultTemplate();
        
        return $this->twig->render($response, 'metadata/create.twig', [
            'templates' => $templates,
            'defaultTemplate' => $defaultTemplate
        ]);
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        if (!isset($data['template_id']) || !isset($data['dataset_id'])) {
            $this->flash->addMessage('error', 'Missing required fields');
            return $response->withHeader('Location', '/metadata/create')->withStatus(302);
        }

        try {
            $template = $this->template->getById((int) $data['template_id']);
            if (!$template) {
                throw new \Exception('Template not found');
            }

            $fields = $this->template->getFields((int) $data['template_id']);
            $metadataXml = $template['template_xml'];

            // Replace template variables with actual values
            foreach ($fields as $field) {
                $value = $data[$field['field_path']] ?? $field['default_value'] ?? '';
                $metadataXml = str_replace('{{' . $field['field_path'] . '}}', $value, $metadataXml);
            }

            // Create metadata record
            $metadataId = $this->metadata->createMetadata([
                'dataset_id' => (int) $data['dataset_id'],
                'metadata_xml' => $metadataXml,
                'metadata_standard' => $template['metadata_standard'],
                'metadata_version' => $template['metadata_version'],
                'quality_score' => $this->metadata->calculateQualityScore($metadataXml)
            ]);

            $this->flash->addMessage('success', 'Metadata created successfully');
            return $response->withHeader('Location', '/metadata/' . $metadataId)->withStatus(302);
        } catch (\Exception $e) {
            $this->flash->addMessage('error', 'Failed to create metadata: ' . $e->getMessage());
            return $response->withHeader('Location', '/metadata/create')->withStatus(302);
        }
    }

    public function searchPublic(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $page = (int)($queryParams['page'] ?? 1);
            $limit = (int)($queryParams['limit'] ?? 10);
            $search = $queryParams['q'] ?? null;
            $metadataStandard = $queryParams['metadata_standard'] ?? null;
            $harvestSource = $queryParams['harvest_source'] ?? null;
            $dateFrom = $queryParams['date_from'] ?? null;
            $dateTo = $queryParams['date_to'] ?? null;

            $result = $this->metadata->findAllPublic(
                $page,
                $limit,
                $search,
                $metadataStandard,
                $harvestSource,
                $dateFrom,
                $dateTo
            );

            return $this->jsonResponse($response, [
                'status' => 'success',
                'data' => [
                    'items' => $result['items'],
                    'total' => $result['total'],
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => ceil($result['total'] / $limit)
                ]
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse($response, [
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getPublicMetadata(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)$args['id'];
            $metadata = $this->metadata->findPublicById($id);

            if (!$metadata) {
                return $this->jsonResponse($response, [
                    'status' => 'error',
                    'message' => 'Metadata not found'
                ], 404);
            }

            return $this->jsonResponse($response, [
                'status' => 'success',
                'data' => $metadata
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse($response, [
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function jsonResponse(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
} 