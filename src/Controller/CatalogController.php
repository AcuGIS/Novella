<?php

declare(strict_types=1);

namespace GeoLibre\Controller;

use GeoLibre\Model\GisData;
use GeoLibre\Model\Metadata;
use GeoLibre\Model\Topic;
use GeoLibre\Model\Document;
use GeoLibre\Validator\GisDataValidator;
use GeoLibre\Validator\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpNotFoundException;
use GeoLibre\Model\MetadataTemplate;

class CatalogController
{
    private GisData $gisData;
    private Metadata $metadata;
    private Topic $topic;
    private Document $document;
    private GisDataValidator $validator;
    private Twig $twig;
    private LoggerInterface $logger;
    private MetadataTemplate $template;

    public function __construct(
        GisData $gisData, 
        Metadata $metadata,
        Topic $topic,
        GisDataValidator $validator, 
        Twig $twig,
        LoggerInterface $logger,
        Document $document,
        MetadataTemplate $template
    ) {
        $this->gisData = $gisData;
        $this->metadata = $metadata;
        $this->topic = $topic;
        $this->validator = $validator;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->document = $document;
        $this->template = $template;
    }

    public function index(Request $request, Response $response): Response
    {
        error_log("=== Catalog Index Debug ===");
        try {
            $page = (int)($request->getQueryParams()['page'] ?? 1);
            $limit = (int)($request->getQueryParams()['limit'] ?? 10);
            $query = $request->getQueryParams()['q'] ?? '';
            $metadataStandard = $request->getQueryParams()['metadata_standard'] ?? null;
            $status = $request->getQueryParams()['status'] ?? null;
            
            error_log("Page: " . $page);
            error_log("Limit: " . $limit);
            error_log("Query: " . $query);
            error_log("Metadata Standard: " . $metadataStandard);
            error_log("Status: " . $status);
            
            $filters = [];
            if ($metadataStandard) {
                $filters['metadata_standard'] = $metadataStandard;
            }
            if ($status) {
                $filters['status'] = $status;
            }
            
            // If there's no search query, get all datasets
            error_log("Fetching datasets...");
            $datasets = empty($query) ? $this->gisData->getAll($filters) : $this->gisData->search($query, $filters);
            error_log("Found " . count($datasets) . " datasets");
            
            if (!empty($datasets)) {
                error_log("First dataset: " . print_r($datasets[0], true));
            } else {
                error_log("No datasets found");
            }
            
            // Process each dataset to extract keywords from metadata
            foreach ($datasets as &$dataset) {
                if (!empty($dataset['metadata_xml'])) {
                    try {
                        $xml = new \SimpleXMLElement($dataset['metadata_xml']);
                        $xml->registerXPathNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
                        $keywords = $xml->xpath('//gmd:keyword');
                        $dataset['keywords'] = array_map(function($keyword) {
                            return (string)$keyword;
                        }, $keywords);
                    } catch (\Exception $e) {
                        error_log("Error processing metadata XML for dataset {$dataset['id']}: " . $e->getMessage());
                        $dataset['keywords'] = [];
                    }
                } else {
                    $dataset['keywords'] = [];
                }
            }
            
            error_log("=== End Catalog Index Debug ===");
            
            // Render the template with datasets
            $response = $this->twig->render($response, 'catalog/index.twig', [
                'datasets' => $datasets,
                'request' => $request
            ]);
            
            error_log("Template rendered successfully");
            return $response;
            
        } catch (\Exception $e) {
            error_log("Error in catalog index: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        // Always use findById to show all datasets
        $dataset = $this->gisData->findById((int)$args['id']);
        
        if (!$dataset) {
            $this->flash('error', 'Dataset not found');
            return $response->withHeader('Location', '/catalog')->withStatus(302);
        }
        
        // Set is_harvested flag for template
        $dataset['is_harvested'] = $this->gisData->isHarvestedDataset($dataset['id']);
        $metadata = $this->metadata->findByDatasetId($dataset['id']);
        
        // Merge metadata with dataset data
        if ($metadata) {
            $dataset['metadata_xml'] = $metadata['metadata_xml'];
            $dataset['quality_score'] = $metadata['quality_score'];
            $dataset['metadata_standard'] = $metadata['metadata_standard'];
            $dataset['metadata_version'] = $metadata['metadata_version'];

            // Extract metadata from XML if it exists
            if (!empty($metadata['metadata_xml'])) {
                try {
                    $xml = new \SimpleXMLElement($metadata['metadata_xml']);
                    $xml->registerXPathNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
                    $xml->registerXPathNamespace('gco', 'http://www.isotc211.org/2005/gco');
                    $xml->registerXPathNamespace('gml', 'http://www.opengis.net/gml/3.2');

                    // Helper function to safely get text content
                    $getText = function($xpath) use ($xml) {
                        $result = $xml->xpath($xpath);
                        return $result ? (string)$result[0] : '';
                    };

                    // Extract keywords
                    $keywords = $xml->xpath('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:descriptiveKeywords/gmd:MD_Keywords/gmd:keyword/gco:CharacterString');
                    if ($keywords) {
                        $dataset['keywords'] = array_map('strval', $keywords);
                    }

                } catch (\Exception $e) {
                    $this->logger->error('Error parsing metadata XML: ' . $e->getMessage());
                }
            }
        }
        
        return $this->twig->render($response, 'catalog/dataset_details.twig', [
            'dataset' => $dataset,
            'is_public_view' => false
        ]);
    }

    public function editForm(Request $request, Response $response, array $args): Response
    {
        $dataset = $this->gisData->findById((int)$args['id']);
        
        if (!$dataset) {
            $this->flash('error', 'Dataset not found');
            return $response->withHeader('Location', '/catalog')->withStatus(302);
        }

        // Extract metadata from XML if it exists
        if (!empty($dataset['metadata_xml'])) {
            try {
                $xml = new \SimpleXMLElement($dataset['metadata_xml']);
                $xml->registerXPathNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
                $xml->registerXPathNamespace('gco', 'http://www.isotc211.org/2005/gco');
                $xml->registerXPathNamespace('gml', 'http://www.opengis.net/gml/3.2');

                // Helper function to safely get text content
                $getText = function($xpath) use ($xml) {
                    $result = $xml->xpath($xpath);
                    return $result ? (string)$result[0] : '';
                };

                $dataset['metadata'] = [
                    'abstract' => $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gco:CharacterString'),
                    'purpose' => $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:purpose/gco:CharacterString'),
                    'datasetLanguage' => $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:language/gmd:LanguageCode'),
                    'characterSet' => $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:characterSet/gmd:MD_CharacterSetCode'),
                    'topicCategory' => $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:topicCategory/gmd:MD_TopicCategoryCode'),
                    'temporalStart' => $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:temporalElement/gmd:EX_TemporalExtent/gmd:extent/gml:TimePeriod/gml:beginPosition'),
                    'temporalEnd' => $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:temporalElement/gmd:EX_TemporalExtent/gmd:extent/gml:TimePeriod/gml:endPosition'),
                    'pointOfContactName' => $getText('//gmd:contact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString'),
                    'pointOfContactOrg' => $getText('//gmd:contact/gmd:CI_ResponsibleParty/gmd:organisationName/gco:CharacterString'),
                    'pointOfContactEmail' => $getText('//gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString'),
                    'metadataPointOfContactName' => $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString'),
                    'metadataPointOfContactOrg' => $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:organisationName/gco:CharacterString'),
                    'metadataPointOfContactEmail' => $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString'),
                    'metadataPointOfContactRole' => $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:role/gmd:CI_RoleCode'),
                    'publisherName' => $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:citedResponsibleParty/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString'),
                    'publisherOrg' => $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:citedResponsibleParty/gmd:CI_ResponsibleParty/gmd:organisationName/gco:CharacterString'),
                    'publisherRole' => $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:citedResponsibleParty/gmd:CI_ResponsibleParty/gmd:role/gmd:CI_RoleCode'),
                    'lineage' => $getText('//gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:lineage/gmd:LI_Lineage/gmd:statement/gco:CharacterString'),
                    'scope' => $getText('//gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:scope/gmd:DQ_Scope/gmd:level/gmd:MD_ScopeCode'),
                    'completeness' => $getText('//gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:report/gmd:DQ_Completeness/gmd:result/gmd:DQ_QuantitativeResult/gmd:value/gco:Record'),
                    'logicalConsistency' => $getText('//gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:report/gmd:DQ_LogicalConsistency/gmd:result/gmd:DQ_QuantitativeResult/gmd:value/gco:Record'),
                    'positionalAccuracy' => $getText('//gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:report/gmd:DQ_PositionalAccuracy/gmd:result/gmd:DQ_QuantitativeResult/gmd:value/gco:Record'),
                    'temporalAccuracy' => $getText('//gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:report/gmd:DQ_TemporalAccuracy/gmd:result/gmd:DQ_QuantitativeResult/gmd:value/gco:Record'),
                    'thematicAccuracy' => $getText('//gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:report/gmd:DQ_ThematicAccuracy/gmd:result/gmd:DQ_QuantitativeResult/gmd:value/gco:Record'),
                    'distributionFormat' => $getText('//gmd:distributionInfo/gmd:MD_Distribution/gmd:distributionFormat/gmd:MD_Format/gmd:name/gco:CharacterString'),
                    'distributionFormatVersion' => $getText('//gmd:distributionInfo/gmd:MD_Distribution/gmd:distributionFormat/gmd:MD_Format/gmd:version/gco:CharacterString'),
                    'distributionUrl' => $getText('//gmd:distributionInfo/gmd:MD_Distribution/gmd:transferOptions/gmd:MD_DigitalTransferOptions/gmd:onLine/gmd:CI_OnlineResource/gmd:linkage/gmd:URL'),
                    'distributionTransferOptions' => $getText('//gmd:distributionInfo/gmd:MD_Distribution/gmd:transferOptions/gmd:MD_DigitalTransferOptions/gmd:onLine/gmd:CI_OnlineResource/gmd:description/gco:CharacterString'),
                    'distributionSize' => $getText('//gmd:distributionInfo/gmd:MD_Distribution/gmd:distributionFormat/gmd:MD_Format/gmd:fileSize/gco:Real'),
                    'distributionUnits' => $getText('//gmd:distributionInfo/gmd:MD_Distribution/gmd:distributionFormat/gmd:MD_Format/gmd:fileSize/gco:Real/@uom'),
                    'maintenanceFrequency' => $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceAndUpdateFrequency/gmd:MD_MaintenanceFrequencyCode'),
                    'maintenanceNote' => $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceNote/gco:CharacterString'),
                    'maintenanceDate' => $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:dateOfNextUpdate/gco:Date'),
                    'maintenanceScope' => $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:scope/gmd:MD_ScopeCode')
                ];

                // Extract spatial extent
                $west = $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:westBoundLongitude/gco:Decimal');
                $east = $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:eastBoundLongitude/gco:Decimal');
                $south = $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:southBoundLatitude/gco:Decimal');
                $north = $getText('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:northBoundLatitude/gco:Decimal');

                if ($west !== '' && $east !== '' && $south !== '' && $north !== '') {
                    $dataset['spatial_extent'] = [
                        'westBoundLongitude' => $west,
                        'eastBoundLongitude' => $east,
                        'southBoundLatitude' => $south,
                        'northBoundLatitude' => $north
                    ];
                }

                // Extract keywords
                $keywords = $xml->xpath('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:descriptiveKeywords/gmd:MD_Keywords/gmd:keyword/gco:CharacterString');
                if ($keywords) {
                    $dataset['keywords'] = array_map('strval', $keywords);
                }

            } catch (\Exception $e) {
                $this->logger->error('Error parsing metadata XML: ' . $e->getMessage());
            }
        }
        
        return $this->twig->render($response, 'catalog/edit.twig', [
            'dataset' => $dataset,
            'request' => $request
        ]);
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $dataset = $this->gisData->findById($id);

        if (!$dataset) {
            throw new HttpNotFoundException($request, 'Dataset not found');
        }

        if ($request->getMethod() === 'POST') {
            $input = $request->getParsedBody();
            
            // Enhanced debug logging
            error_log("=== Edit Form Submission Debug ===");
            error_log("Dataset ID: " . $id);
            error_log("Raw input: " . print_r($input, true));
            error_log("Current dataset state: " . print_r($dataset, true));
            
            // Validate input
            if (!$this->validator->validate($input)) {
                $errors = $this->validator->getErrors();
                error_log("Validation failed. Errors: " . print_r($errors, true));
                return $this->twig->render($response, 'catalog/edit.twig', [
                    'dataset' => $dataset,
                    'errors' => $errors,
                    'input' => $input  // Pass the input back to the form
                ]);
            }

            try {
                // Get spatial extent from the hidden input if available
                $spatialExtent = null;
                if (!empty($input['spatial_extent'])) {
                    $spatialExtent = json_decode($input['spatial_extent'], true);
                    error_log("Using spatial extent from hidden input: " . print_r($spatialExtent, true));
                } else {
                    // Fallback to individual coordinate fields
                    $west = floatval($input['westBoundLongitude'] ?? $input['westBound'] ?? 0);
                    $east = floatval($input['eastBoundLongitude'] ?? $input['eastBound'] ?? 0);
                    $south = floatval($input['southBoundLatitude'] ?? $input['southBound'] ?? 0);
                    $north = floatval($input['northBoundLatitude'] ?? $input['northBound'] ?? 0);

                    error_log("Using individual coordinate fields - West: $west, East: $east, South: $south, North: $north");

                    // Create GeoJSON polygon for spatial extent
                    $spatialExtent = [
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

                // Handle thumbnail upload
                $uploadedFiles = $request->getUploadedFiles();
                $thumbnailFilename = $dataset['thumbnail_path'] ?? null;
                if (isset($uploadedFiles['thumbnail']) && $uploadedFiles['thumbnail']->getError() === UPLOAD_ERR_OK) {
                    $thumbnailFile = $uploadedFiles['thumbnail'];
                    $extension = pathinfo($thumbnailFile->getClientFilename(), PATHINFO_EXTENSION);
                    $safeName = 'thumb_' . $id . '_' . uniqid() . '.' . $extension;
                    $uploadPath = __DIR__ . '/../../public/assets/thumbnails/' . $safeName;
                    $thumbnailFile->moveTo($uploadPath);
                    $thumbnailFilename = $safeName;
                }

                // Prepare dataset data
                $isPublic = isset($input['is_public']) && ($input['is_public'] === '1' || $input['is_public'] === 'on' || $input['is_public'] === 1 || $input['is_public'] === true);
                $datasetData = [
                    'title' => $input['title'],
                    'description' => $input['description'],
                    'spatial_extent' => json_encode($spatialExtent),
                    'is_public' => $isPublic,
                    'status' => $input['status'] ?? 'draft',
                    'thumbnail_path' => $thumbnailFilename
                ];

                error_log("Dataset data to update: " . print_r($datasetData, true));

                // Update dataset record
                $success = $this->gisData->updateGisData($id, $datasetData);
                
                if (!$success) {
                    error_log("Failed to update dataset in database");
                    throw new \Exception('Failed to update dataset');
                }

                error_log("Dataset updated successfully in database");

                // Extract coordinates from spatial extent for metadata
                $coordinates = $spatialExtent['coordinates'][0];
                $west = $coordinates[0][0];
                $south = $coordinates[0][1];
                $east = $coordinates[2][0];
                $north = $coordinates[2][1];

                // Prepare metadata fields
                $metadataFields = [
                    'title' => $input['title'] ?? '',
                    'abstract' => $input['abstract'] ?? '',
                    'purpose' => $input['purpose'] ?? '',
                    'datasetLanguage' => $input['datasetLanguage'] ?? 'eng',
                    'characterSet' => $input['characterSet'] ?? 'utf8',
                    'topicCategory' => $input['topicCategory'] ?? '',
                    'westBound' => $west,
                    'eastBound' => $east,
                    'southBound' => $south,
                    'northBound' => $north,
                    'crs' => $input['crs'] ?? 'EPSG:4326',
                    'crsType' => $input['crsType'] ?? 'geographic',
                    'spatialResolution' => $input['spatialResolution'] ?? '',
                    'spatialRepresentationType' => $input['spatialRepresentationType'] ?? '',
                    'spatialResolutionUnits' => $input['spatialResolutionUnits'] ?? '',
                    'spatialResolutionDistance' => $input['spatialResolutionDistance'] ?? '',
                    'spatialResolutionVertical' => $input['spatialResolutionVertical'] ?? '',
                    'spatialResolutionVerticalUnits' => $input['spatialResolutionVerticalUnits'] ?? '',
                    'temporalStart' => $input['temporalStart'] ?? '',
                    'temporalEnd' => $input['temporalEnd'] ?? '',
                    'pointOfContactName' => $input['pointOfContactName'] ?? '',
                    'pointOfContactOrg' => $input['pointOfContactOrg'] ?? '',
                    'pointOfContactEmail' => $input['pointOfContactEmail'] ?? '',
                    'metadataPointOfContactName' => $input['metadataPointOfContactName'] ?? '',
                    'metadataPointOfContactOrg' => $input['metadataPointOfContactOrg'] ?? '',
                    'metadataPointOfContactEmail' => $input['metadataPointOfContactEmail'] ?? '',
                    'metadataPointOfContactRole' => $input['metadataPointOfContactRole'] ?? '',
                    'publisherName' => $input['publisherName'] ?? '',
                    'publisherOrg' => $input['publisherOrg'] ?? '',
                    'publisherRole' => $input['publisherRole'] ?? '',
                    'scope' => $input['scope'] ?? '',
                    'lineage' => $input['lineage'] ?? '',
                    'completeness' => $input['completeness'] ?? '',
                    'logicalConsistency' => $input['logicalConsistency'] ?? '',
                    'positionalAccuracy' => $input['positionalAccuracy'] ?? '',
                    'temporalAccuracy' => $input['temporalAccuracy'] ?? '',
                    'thematicAccuracy' => $input['thematicAccuracy'] ?? '',
                    'distributionFormat' => $input['distributionFormat'] ?? '',
                    'distributionFormatVersion' => $input['distributionFormatVersion'] ?? '',
                    'distributionUrl' => $input['distributionUrl'] ?? '',
                    'distributionTransferOptions' => $input['distributionTransferOptions'] ?? '',
                    'distributionSize' => $input['distributionSize'] ?? '',
                    'distributionUnits' => $input['distributionUnits'] ?? '',
                    'accessConstraints' => $input['distributionAccessConstraints'] ?? 'otherRestrictions',
                    'useConstraints' => $input['distributionUseConstraints'] ?? 'otherRestrictions',
                    'useLimitation' => $input['distributionUseLimitation'] ?? '',
                    'maintenanceFrequency' => $input['maintenanceFrequency'] ?? '',
                    'maintenanceNote' => $input['maintenanceNote'] ?? '',
                    'maintenanceDate' => $input['maintenanceDate'] ?? '',
                    'maintenanceScope' => $input['maintenanceScope'] ?? '',
                    'metadata_standard' => $input['metadata_standard'] ?? 'ISO 19115',
                    'metadata_version' => $input['metadata_version'] ?? '2018'
                ];

                // Add keywords if present
                if (!empty($input['keywords'])) {
                    $metadataFields['keywords'] = is_array($input['keywords']) ? $input['keywords'] : explode(',', $input['keywords']);
                }

                error_log("Metadata fields to update: " . print_r($metadataFields, true));

                // Generate metadata XML
                $metadataXml = $this->generateMetadataXml($metadataFields);
                error_log("Generated metadata XML: " . $metadataXml);
                
                // Calculate quality score
                $qualityScore = $this->calculateQualityScore($metadataXml);
                error_log("Calculated quality score: " . $qualityScore);

                // Update metadata
                $metadataData = [
                    'metadata_xml' => $metadataXml,
                    'quality_score' => $qualityScore,
                    'metadata_standard' => $input['metadata_standard'] ?? 'ISO 19115',
                    'metadata_version' => $input['metadata_version'] ?? '2018'
                ];

                error_log("Final metadata data to update: " . print_r($metadataData, true));

                $metadataSuccess = $this->metadata->updateMetadata($id, $metadataData);
                if (!$metadataSuccess) {
                    error_log("Failed to update metadata in database");
                    throw new \Exception('Failed to update metadata');
                }

                error_log("Metadata updated successfully in database");
                error_log("=== End Edit Form Submission Debug ===");

                $this->flash('success', 'Dataset updated successfully');
                return $response->withHeader('Location', '/catalog/' . $id)->withStatus(302);
            } catch (\Exception $e) {
                error_log("Error updating dataset: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                $this->flash('error', 'Failed to update dataset: ' . $e->getMessage());
                return $this->twig->render($response, 'catalog/edit.twig', [
                    'dataset' => $dataset,
                    'errors' => ['general' => 'Failed to update dataset: ' . $e->getMessage()],
                    'input' => $input  // Pass the input back to the form
                ]);
            }
        }

        return $this->twig->render($response, 'catalog/edit.twig', [
            'dataset' => $dataset
        ]);
    }

    private function generateMetadataXml(array $metadata): string
    {
        $standard = $metadata['metadata_standard'] ?? 'ISO 19115';
        $version = $metadata['metadata_version'] ?? '2018';
        
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
            <gmd:MD_Metadata xmlns:gmd="http://www.isotc211.org/2005/gmd"
                            xmlns:gco="http://www.isotc211.org/2005/gco"
                            xmlns:gml="http://www.opengis.net/gml/3.2"
                            xmlns:xlink="http://www.w3.org/1999/xlink">
                <gmd:fileIdentifier>
                    <gco:CharacterString>' . uniqid('dataset_') . '</gco:CharacterString>
                </gmd:fileIdentifier>
                <gmd:language>
                    <gco:CharacterString>eng</gco:CharacterString>
                </gmd:language>
                <gmd:characterSet>
                    <gmd:MD_CharacterSetCode>' . ($metadata['characterSet'] ?? 'utf8') . '</gmd:MD_CharacterSetCode>
                </gmd:characterSet>
                <gmd:hierarchyLevel>
                    <gmd:MD_ScopeCode>dataset</gmd:MD_ScopeCode>
                </gmd:hierarchyLevel>
                <gmd:contact>
                    <gmd:CI_ResponsibleParty>
                        <gmd:individualName>
                            <gco:CharacterString>' . htmlspecialchars($metadata['pointOfContactName'] ?? '') . '</gco:CharacterString>
                        </gmd:individualName>
                        <gmd:organisationName>
                            <gco:CharacterString>' . htmlspecialchars($metadata['pointOfContactOrg'] ?? '') . '</gco:CharacterString>
                        </gmd:organisationName>
                        <gmd:contactInfo>
                            <gmd:CI_Contact>
                                <gmd:address>
                                    <gmd:CI_Address>
                                        <gmd:electronicMailAddress>
                                            <gco:CharacterString>' . ($metadata['pointOfContactEmail'] ?? '') . '</gco:CharacterString>
                                        </gmd:electronicMailAddress>
                                    </gmd:CI_Address>
                                </gmd:address>
                            </gmd:CI_Contact>
                        </gmd:contactInfo>
                        <gmd:role>
                            <gmd:CI_RoleCode>pointOfContact</gmd:CI_RoleCode>
                        </gmd:role>
                    </gmd:CI_ResponsibleParty>
                </gmd:contact>
                <gmd:dateStamp>
                    <gco:DateTime>' . date('Y-m-d\TH:i:s') . '</gco:DateTime>
                </gmd:dateStamp>
                <gmd:metadataStandardName>
                    <gco:CharacterString>' . $standard . '</gco:CharacterString>
                </gmd:metadataStandardName>
                <gmd:metadataStandardVersion>
                    <gco:CharacterString>' . $version . '</gco:CharacterString>
                </gmd:metadataStandardVersion>
                <gmd:identificationInfo>
                    <gmd:MD_DataIdentification>
                        <gmd:citation>
                            <gmd:CI_Citation>
                                <gmd:title>
                                    <gco:CharacterString>' . htmlspecialchars($metadata['title'] ?? '') . '</gco:CharacterString>
                                </gmd:title>
                                <gmd:date>
                                    <gmd:CI_Date>
                                        <gmd:date>
                                            <gco:Date>' . date('Y-m-d') . '</gco:Date>
                                        </gmd:date>
                                        <gmd:dateType>
                                            <gmd:CI_DateTypeCode>publication</gmd:CI_DateTypeCode>
                                        </gmd:dateType>
                                    </gmd:CI_Date>
                                </gmd:date>
                                <gmd:citedResponsibleParty>
                                    <gmd:CI_ResponsibleParty>
                                        <gmd:individualName>
                                            <gco:CharacterString>' . htmlspecialchars($metadata['publisherName'] ?? '') . '</gco:CharacterString>
                                        </gmd:individualName>
                                        <gmd:organisationName>
                                            <gco:CharacterString>' . htmlspecialchars($metadata['publisherOrg'] ?? '') . '</gco:CharacterString>
                                        </gmd:organisationName>
                                        <gmd:role>
                                            <gmd:CI_RoleCode>' . htmlspecialchars($metadata['publisherRole'] ?? 'publisher') . '</gmd:CI_RoleCode>
                                        </gmd:role>
                                    </gmd:CI_ResponsibleParty>
                                </gmd:citedResponsibleParty>
                            </gmd:CI_Citation>
                        </gmd:citation>
                        <gmd:pointOfContact>
                            <gmd:CI_ResponsibleParty>
                                <gmd:individualName>
                                    <gco:CharacterString>' . htmlspecialchars($metadata['metadataPointOfContactName'] ?? '') . '</gco:CharacterString>
                                </gmd:individualName>
                                <gmd:organisationName>
                                    <gco:CharacterString>' . htmlspecialchars($metadata['metadataPointOfContactOrg'] ?? '') . '</gco:CharacterString>
                                </gmd:organisationName>
                                <gmd:contactInfo>
                                    <gmd:CI_Contact>
                                        <gmd:address>
                                            <gmd:CI_Address>
                                                <gmd:electronicMailAddress>
                                                    <gco:CharacterString>' . ($metadata['metadataPointOfContactEmail'] ?? '') . '</gco:CharacterString>
                                                </gmd:electronicMailAddress>
                                            </gmd:CI_Address>
                                        </gmd:address>
                                    </gmd:CI_Contact>
                                </gmd:contactInfo>
                                <gmd:role>
                                    <gmd:CI_RoleCode>' . htmlspecialchars($metadata['metadataPointOfContactRole'] ?? 'metadataPointOfContact') . '</gmd:CI_RoleCode>
                                </gmd:role>
                            </gmd:CI_ResponsibleParty>
                        </gmd:pointOfContact>
                        <gmd:abstract>
                            <gco:CharacterString>' . htmlspecialchars($metadata['abstract'] ?? '') . '</gco:CharacterString>
                        </gmd:abstract>
                        <gmd:purpose>
                            <gco:CharacterString>' . htmlspecialchars($metadata['purpose'] ?? '') . '</gco:CharacterString>
                        </gmd:purpose>
                        <gmd:language>
                            <gmd:LanguageCode>' . ($metadata['datasetLanguage'] ?? 'eng') . '</gmd:LanguageCode>
                        </gmd:language>
                        <gmd:characterSet>
                            <gmd:MD_CharacterSetCode>' . ($metadata['characterSet'] ?? 'utf8') . '</gmd:MD_CharacterSetCode>
                        </gmd:characterSet>
                        <gmd:topicCategory>
                            <gmd:MD_TopicCategoryCode>' . ($metadata['topicCategory'] ?? '') . '</gmd:MD_TopicCategoryCode>
                        </gmd:topicCategory>
                        <gmd:descriptiveKeywords>
                            <gmd:MD_Keywords>
                                ' . (!empty($metadata['keywords']) ? implode('', array_map(function($keyword) {
                                    return '<gmd:keyword><gco:CharacterString>' . htmlspecialchars(is_array($keyword) ? implode(', ', $keyword) : $keyword) . '</gco:CharacterString></gmd:keyword>';
                                }, (array)$metadata['keywords'])) : '') . '
                            </gmd:MD_Keywords>
                        </gmd:descriptiveKeywords>
                        <gmd:resourceConstraints>
                            <gmd:MD_LegalConstraints>
                                <gmd:accessConstraints>
                                    <gmd:MD_RestrictionCode>' . ($metadata['accessConstraints'] ?? 'otherRestrictions') . '</gmd:MD_RestrictionCode>
                                </gmd:accessConstraints>
                                <gmd:useConstraints>
                                    <gmd:MD_RestrictionCode>' . ($metadata['useConstraints'] ?? 'otherRestrictions') . '</gmd:MD_RestrictionCode>
                                </gmd:useConstraints>
                            </gmd:MD_LegalConstraints>
                        </gmd:resourceConstraints>
                        <gmd:resourceConstraints>
                            <gmd:MD_Constraints>
                                <gmd:useLimitation>
                                    <gco:CharacterString>' . htmlspecialchars($metadata['useLimitation'] ?? '') . '</gco:CharacterString>
                                </gmd:useLimitation>
                            </gmd:MD_Constraints>
                        </gmd:resourceConstraints>
                        <gmd:extent>
                            <gmd:EX_Extent>
                                <gmd:geographicElement>
                                    <gmd:EX_GeographicBoundingBox>
                                        <gmd:westBoundLongitude>
                                            <gco:Decimal>' . ($metadata['westBound'] ?? '') . '</gco:Decimal>
                                        </gmd:westBoundLongitude>
                                        <gmd:eastBoundLongitude>
                                            <gco:Decimal>' . ($metadata['eastBound'] ?? '') . '</gco:Decimal>
                                        </gmd:eastBoundLongitude>
                                        <gmd:southBoundLatitude>
                                            <gco:Decimal>' . ($metadata['southBound'] ?? '') . '</gco:Decimal>
                                        </gmd:southBoundLatitude>
                                        <gmd:northBoundLatitude>
                                            <gco:Decimal>' . ($metadata['northBound'] ?? '') . '</gco:Decimal>
                                        </gmd:northBoundLatitude>
                                    </gmd:EX_GeographicBoundingBox>
                                </gmd:geographicElement>
                            </gmd:EX_Extent>
                        </gmd:extent>
                        <gmd:spatialRepresentationType>
                            <gmd:MD_SpatialRepresentationTypeCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml#MD_SpatialRepresentationTypeCode" codeListValue="' . ($metadata['spatialRepresentationType'] ?? 'vector') . '">' . ($metadata['spatialRepresentationType'] ?? 'vector') . '</gmd:MD_SpatialRepresentationTypeCode>
                        </gmd:spatialRepresentationType>
                        <gmd:spatialResolution>
                            <gmd:MD_Resolution>
                                <gmd:distance>
                                    <gco:Distance uom="' . ($metadata['spatialResolutionUnits'] ?? 'meters') . '">' . ($metadata['spatialResolutionDistance'] ?? '') . '</gco:Distance>
                                </gmd:distance>
                            </gmd:MD_Resolution>
                        </gmd:spatialResolution>
                        <gmd:spatialResolution>
                            <gmd:MD_Resolution>
                                <gmd:vertical>
                                    <gco:Distance uom="' . ($metadata['spatialResolutionVerticalUnits'] ?? 'meters') . '">' . ($metadata['spatialResolutionVertical'] ?? '') . '</gco:Distance>
                                </gmd:vertical>
                            </gmd:MD_Resolution>
                        </gmd:spatialResolution>
                        <gmd:referenceSystemInfo>
                            <gmd:MD_ReferenceSystem>
                                <gmd:referenceSystemIdentifier>
                                    <gmd:RS_Identifier>
                                        <gmd:code>
                                            <gco:CharacterString>' . ($metadata['crs'] ?? 'EPSG:4326') . '</gco:CharacterString>
                                        </gmd:code>
                                        <gmd:codeSpace>
                                            <gco:CharacterString>EPSG</gco:CharacterString>
                                        </gmd:codeSpace>
                                    </gmd:RS_Identifier>
                                </gmd:referenceSystemIdentifier>
                                <gmd:referenceSystemType>
                                    <gmd:MD_ReferenceSystemTypeCode codeList="http://www.isotc211.org/2005/resources/Codelist/gmxCodelists.xml#MD_ReferenceSystemTypeCode" codeListValue="' . ($metadata['crsType'] ?? 'geographic') . '">' . ($metadata['crsType'] ?? 'geographic') . '</gmd:MD_ReferenceSystemTypeCode>
                                </gmd:referenceSystemType>
                            </gmd:MD_ReferenceSystem>
                        </gmd:referenceSystemInfo>
                        <gmd:temporalElement>
                            <gmd:EX_TemporalExtent>
                                <gmd:extent>
                                    <gml:TimePeriod>
                                        <gml:beginPosition>' . ($metadata['temporalStart'] ?? '') . '</gml:beginPosition>
                                        <gml:endPosition>' . ($metadata['temporalEnd'] ?? '') . '</gml:endPosition>
                                    </gml:TimePeriod>
                                </gmd:extent>
                            </gmd:EX_TemporalExtent>
                        </gmd:temporalElement>
                        <gmd:resourceMaintenance>
                            <gmd:MD_MaintenanceInformation>
                                <gmd:maintenanceAndUpdateFrequency>
                                    <gmd:MD_MaintenanceFrequencyCode>' . ($metadata['maintenanceFrequency'] ?? '') . '</gmd:MD_MaintenanceFrequencyCode>
                                </gmd:maintenanceAndUpdateFrequency>
                                <gmd:maintenanceNote>
                                    <gco:CharacterString>' . htmlspecialchars($metadata['maintenanceNote'] ?? '') . '</gco:CharacterString>
                                </gmd:maintenanceNote>
                                <gmd:dateOfNextUpdate>
                                    <gco:Date>' . ($metadata['maintenanceDate'] ?? '') . '</gco:Date>
                                </gmd:dateOfNextUpdate>
                                <gmd:scope>
                                    <gmd:MD_ScopeCode>' . ($metadata['maintenanceScope'] ?? '') . '</gmd:MD_ScopeCode>
                                </gmd:scope>
                            </gmd:MD_MaintenanceInformation>
                        </gmd:resourceMaintenance>
                    </gmd:MD_DataIdentification>
                </gmd:identificationInfo>
                <gmd:dataQualityInfo>
                    <gmd:DQ_DataQuality>
                        <gmd:scope>
                            <gmd:DQ_Scope>
                                <gmd:level>
                                    <gmd:MD_ScopeCode>' . ($metadata['scope'] ?? '') . '</gmd:MD_ScopeCode>
                                </gmd:level>
                            </gmd:DQ_Scope>
                        </gmd:scope>
                        <gmd:lineage>
                            <gmd:LI_Lineage>
                                <gmd:statement>
                                    <gco:CharacterString>' . htmlspecialchars($metadata['lineage'] ?? '') . '</gco:CharacterString>
                                </gmd:statement>
                            </gmd:LI_Lineage>
                        </gmd:lineage>
                        <gmd:report>
                            <gmd:DQ_Completeness>
                                <gmd:result>
                                    <gmd:DQ_QuantitativeResult>
                                        <gmd:value>
                                            <gco:Record>' . htmlspecialchars($metadata['completeness'] ?? '') . '</gco:Record>
                                        </gmd:value>
                                    </gmd:DQ_QuantitativeResult>
                                </gmd:result>
                            </gmd:DQ_Completeness>
                        </gmd:report>
                        <gmd:report>
                            <gmd:DQ_LogicalConsistency>
                                <gmd:result>
                                    <gmd:DQ_QuantitativeResult>
                                        <gmd:value>
                                            <gco:Record>' . htmlspecialchars($metadata['logicalConsistency'] ?? '') . '</gco:Record>
                                        </gmd:value>
                                    </gmd:DQ_QuantitativeResult>
                                </gmd:result>
                            </gmd:DQ_LogicalConsistency>
                        </gmd:report>
                        <gmd:report>
                            <gmd:DQ_PositionalAccuracy>
                                <gmd:result>
                                    <gmd:DQ_QuantitativeResult>
                                        <gmd:value>
                                            <gco:Record>' . htmlspecialchars($metadata['positionalAccuracy'] ?? '') . '</gco:Record>
                                        </gmd:value>
                                    </gmd:DQ_QuantitativeResult>
                                </gmd:result>
                            </gmd:DQ_PositionalAccuracy>
                        </gmd:report>
                        <gmd:report>
                            <gmd:DQ_TemporalAccuracy>
                                <gmd:result>
                                    <gmd:DQ_QuantitativeResult>
                                        <gmd:value>
                                            <gco:Record>' . htmlspecialchars($metadata['temporalAccuracy'] ?? '') . '</gco:Record>
                                        </gmd:value>
                                    </gmd:DQ_QuantitativeResult>
                                </gmd:result>
                            </gmd:DQ_TemporalAccuracy>
                        </gmd:report>
                        <gmd:report>
                            <gmd:DQ_ThematicAccuracy>
                                <gmd:result>
                                    <gmd:DQ_QuantitativeResult>
                                        <gmd:value>
                                            <gco:Record>' . htmlspecialchars($metadata['thematicAccuracy'] ?? '') . '</gco:Record>
                                        </gmd:value>
                                    </gmd:DQ_QuantitativeResult>
                                </gmd:result>
                            </gmd:DQ_ThematicAccuracy>
                        </gmd:report>
                    </gmd:DQ_DataQuality>
                </gmd:dataQualityInfo>
                <gmd:distributionInfo>
                    <gmd:MD_Distribution>
                        <gmd:distributor>
                            <gmd:MD_Distributor>
                                <gmd:distributorContact>
                                    <gmd:CI_ResponsibleParty>
                                        <gmd:individualName>
                                            <gco:CharacterString>' . htmlspecialchars($metadata['distributorName'] ?? '') . '</gco:CharacterString>
                                        </gmd:individualName>
                                        <gmd:organisationName>
                                            <gco:CharacterString>' . htmlspecialchars($metadata['distributorOrg'] ?? '') . '</gco:CharacterString>
                                        </gmd:organisationName>
                                        <gmd:contactInfo>
                                            <gmd:CI_Contact>
                                                <gmd:address>
                                                    <gmd:CI_Address>
                                                        <gmd:electronicMailAddress>
                                                            <gco:CharacterString>' . htmlspecialchars($metadata['distributorEmail'] ?? '') . '</gco:CharacterString>
                                                        </gmd:electronicMailAddress>
                                                    </gmd:CI_Address>
                                                </gmd:address>
                                            </gmd:CI_Contact>
                                        </gmd:contactInfo>
                                        <gmd:role>
                                            <gmd:CI_RoleCode>distributor</gmd:CI_RoleCode>
                                        </gmd:role>
                                    </gmd:CI_ResponsibleParty>
                                </gmd:distributorContact>
                            </gmd:MD_Distributor>
                        </gmd:distributor>
                        <gmd:distributionFormat>
                            <gmd:MD_Format>
                                <gmd:name>
                                    <gco:CharacterString>' . htmlspecialchars($metadata['distributionFormat'] ?? '') . '</gco:CharacterString>
                                </gmd:name>
                                <gmd:version>
                                    <gco:CharacterString>' . htmlspecialchars($metadata['distributionFormatVersion'] ?? '') . '</gco:CharacterString>
                                </gmd:version>
                                <gmd:fileSize>
                                    <gco:Real uom="' . htmlspecialchars($metadata['distributionUnits'] ?? 'bytes') . '">' . htmlspecialchars($metadata['distributionSize'] ?? '') . '</gco:Real>
                                </gmd:fileSize>
                            </gmd:MD_Format>
                        </gmd:distributionFormat>
                        <gmd:transferOptions>
                            <gmd:MD_DigitalTransferOptions>
                                <gmd:transferSize>
                                    <gco:Real uom="' . htmlspecialchars($metadata['distributionUnits'] ?? 'bytes') . '">' . htmlspecialchars($metadata['distributionSize'] ?? '') . '</gco:Real>
                                </gmd:transferSize>
                                <gmd:onLine>
                                    <gmd:CI_OnlineResource>
                                        <gmd:linkage>
                                            <gmd:URL>' . ($metadata['distributionUrl'] ?? '') . '</gmd:URL>
                                        </gmd:linkage>
                                        <gmd:description>
                                            <gco:CharacterString>' . htmlspecialchars($metadata['distributionTransferOptions'] ?? '') . '</gco:CharacterString>
                                        </gmd:description>
                                    </gmd:CI_OnlineResource>
                                </gmd:onLine>
                            </gmd:MD_DigitalTransferOptions>
                        </gmd:transferOptions>
                    </gmd:MD_Distribution>
                </gmd:distributionInfo>
            </gmd:MD_Metadata>');
        
        return $xml->asXML();
    }

    private function calculateQualityScore(string $metadataXml): float
    {
        $xml = new \SimpleXMLElement($metadataXml);
        $score = 0;
        $maxScore = 0;

        // Define weights for each metadata element
        $weights = [
            'identification' => [
                'abstract' => 10,
                'purpose' => 5
            ],
            'spatialReference' => [
                'crs' => 10,
                'crsType' => 5
            ],
            'temporal' => [
                'startDate' => 5,
                'endDate' => 5
            ],
            'responsibleParties' => [
                'pointOfContact' => [
                    'name' => 5,
                    'organization' => 5,
                    'email' => 5
                ],
                'publisher' => [
                    'name' => 5,
                    'organization' => 5
                ]
            ],
            'dataQuality' => [
                'lineageStatement' => 10,
                'conformity' => [
                    'standard' => 5,
                    'degree' => 5
                ]
            ],
            'constraints' => [
                'useConstraints' => 5,
                'accessConstraints' => 5,
                'license' => 5
            ],
            'distribution' => [
                'url' => 5,
                'format' => 5,
                'distributor' => [
                    'name' => 5,
                    'organization' => 5
                ]
            ],
            'maintenance' => [
                'updateFrequency' => 5,
                'maintenanceNotes' => 5
            ]
        ];

        // Calculate maximum possible score
        foreach ($weights as $category => $elements) {
            if (is_array($elements)) {
                foreach ($elements as $element => $weight) {
                    if (is_array($weight)) {
                        foreach ($weight as $subElement => $subWeight) {
                            $maxScore += $subWeight;
                        }
                    } else {
                        $maxScore += $weight;
                    }
                }
            }
        }

        // Calculate actual score
        foreach ($weights as $category => $elements) {
            if (isset($xml->$category)) {
                foreach ($elements as $element => $weight) {
                    if (is_array($weight)) {
                        foreach ($weight as $subElement => $subWeight) {
                            if (isset($xml->$category->$element->$subElement) && !empty($xml->$category->$element->$subElement)) {
                                $score += $subWeight;
                            }
                        }
                    } else {
                        if (isset($xml->$category->$element) && !empty($xml->$category->$element)) {
                            $score += $weight;
                        }
                    }
                }
            }
        }

        return ($maxScore > 0) ? ($score / $maxScore) * 100 : 0;
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    // API Methods
    public function getDatasets(Request $request, Response $response): Response
    {
        try {
            $page = (int)($request->getQueryParams()['page'] ?? 1);
            $limit = (int)($request->getQueryParams()['limit'] ?? 10);
            $query = $request->getQueryParams()['q'] ?? '';
            $metadataStandard = $request->getQueryParams()['metadata_standard'] ?? null;
            
            $filters = [];
            if ($metadataStandard) {
                $filters['metadata_standard'] = $metadataStandard;
            }
            
            $datasets = empty($query) ? $this->gisData->getAll() : $this->gisData->search($query, $filters);
            
            // Process each dataset to extract keywords from metadata
            foreach ($datasets as &$dataset) {
                if (!empty($dataset['metadata_xml'])) {
                    try {
                        $xml = new \SimpleXMLElement($dataset['metadata_xml']);
                        $xml->registerXPathNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
                        $keywords = $xml->xpath('//gmd:keyword');
                        $dataset['keywords'] = array_map(function($keyword) {
                            return (string)$keyword;
                        }, $keywords);
                    } catch (\Exception $e) {
                        error_log("Error processing metadata XML for dataset {$dataset['id']}: " . $e->getMessage());
                        $dataset['keywords'] = [];
                    }
                } else {
                    $dataset['keywords'] = [];
                }
            }
            
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $datasets
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            error_log("Error in getDatasets: " . $e->getMessage());
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Failed to fetch datasets'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getDataset(Request $request, Response $response, array $args): Response
    {
        try {
            $dataset = $this->gisData->findById((int)$args['id']);
            
            if (!$dataset) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Dataset not found'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            
            $metadata = $this->metadata->findByDatasetId($dataset['id']);
            $dataset['metadata'] = $metadata;
            
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $dataset
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            error_log("Error in getDataset: " . $e->getMessage());
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Failed to fetch dataset'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function createDataset(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            
            // Validate input
            $errors = $this->validator->validate($data, []);
            
            if (!empty($errors)) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $errors
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            // Format coordinates for PostGIS
            $west = floatval($data['westBound']);
            $east = floatval($data['eastBound']);
            $south = floatval($data['southBound']);
            $north = floatval($data['northBound']);

            // Create GeoJSON polygon
            $geometry = [
                'type' => 'Polygon',
                'coordinates' => [[
                    [$west, $south],
                    [$east, $south],
                    [$east, $north],
                    [$west, $north],
                    [$west, $south]
                ]]
            ];

            // Prepare dataset data
            $datasetData = [
                'title' => $data['title'],
                'description' => $data['description'],
                'spatial_extent' => $geometry,
                'is_public' => isset($data['is_public']),
                'status' => $data['status'] ?? 'draft',
                'created_by' => $request->getAttribute('user_id')
            ];

            // Create dataset record
            $datasetId = $this->gisData->createGisData($datasetData);

            // Process keywords
            $keywords = array_map('trim', explode(',', $data['keywords'] ?? ''));

            // Create metadata record
            $metadata = [
                'dataset_id' => $datasetId,
                'metadata_xml' => json_encode([
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'abstract' => $data['abstract'] ?? '',
                    'purpose' => $data['purpose'] ?? '',
                    'keywords' => $keywords,
                    'topic_category' => $data['topic_category'],
                    'spatial_extent' => [
                        'westBoundLongitude' => $west,
                        'eastBoundLongitude' => $east,
                        'southBoundLatitude' => $south,
                        'northBoundLatitude' => $north
                    ],
                    'url' => $data['url'],
                    'distribution_info' => [
                        'url' => $data['url'],
                        'protocol' => 'HTTP',
                        'name' => $data['title'],
                        'description' => $data['description']
                    ]
                ]),
                'metadata_standard' => $data['metadata_standard'],
                'metadata_version' => $data['metadata_version']
            ];

            $this->metadata->createMetadata($metadata);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'Dataset created successfully',
                'data' => ['id' => $datasetId]
            ]));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            error_log("Error in createDataset: " . $e->getMessage());
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Failed to create dataset'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function updateDataset(Request $request, Response $response, array $args): Response
    {
        try {
            $dataset = $this->gisData->findById((int)$args['id']);
            
            if (!$dataset) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Dataset not found'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $data = $request->getParsedBody();
            
            // Validate input
            $errors = $this->validator->validate($data, []);
            
            if (!empty($errors)) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $errors
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            // Format coordinates for PostGIS
            $west = floatval($data['westBound']);
            $east = floatval($data['eastBound']);
            $south = floatval($data['southBound']);
            $north = floatval($data['northBound']);

            // Create GeoJSON polygon
            $geometry = [
                'type' => 'Polygon',
                'coordinates' => [[
                    [$west, $south],
                    [$east, $south],
                    [$east, $north],
                    [$west, $north],
                    [$west, $south]
                ]]
            ];

            // Prepare dataset data
            $datasetData = [
                'title' => $data['title'],
                'description' => $data['description'],
                'spatial_extent' => $geometry,
                'is_public' => isset($data['is_public']),
                'status' => $data['status'] ?? 'draft'
            ];

            // Update dataset record
            $success = $this->gisData->updateGisData((int)$args['id'], $datasetData);

            if (!$success) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Failed to update dataset'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'Dataset updated successfully'
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            error_log("Error in updateDataset: " . $e->getMessage());
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Failed to update dataset'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)$args['id'];
            $dataset = $this->gisData->findById($id);
            
            if (!$dataset) {
                $this->flash('error', 'Dataset not found');
                return $response->withHeader('Location', '/catalog')->withStatus(302);
            }

            // Check if this is a harvested dataset
            $isHarvested = $this->gisData->isHarvestedDataset($id);

            if ($isHarvested) {
                $this->flash('error', 'Cannot delete harvested datasets');
                return $response->withHeader('Location', '/catalog')->withStatus(302);
            }

            // Delete the dataset (this will cascade delete metadata)
            $success = $this->gisData->deleteDataset($id);

            if (!$success) {
                throw new \Exception('Failed to delete dataset');
            }

            $this->flash('success', 'Dataset deleted successfully');
            return $response->withHeader('Location', '/catalog')->withStatus(302);
        } catch (\Exception $e) {
            error_log("Error deleting dataset: " . $e->getMessage());
            $this->flash('error', 'Failed to delete dataset: ' . $e->getMessage());
            return $response->withHeader('Location', '/catalog')->withStatus(302);
        }
    }

    public function getPublicDatasets(Request $request, Response $response): Response
    {
        try {
            $params = $request->getQueryParams();
            $page = (int)($params['page'] ?? 1);
            $limit = (int)($params['limit'] ?? 10);
            $search = $params['q'] ?? '';
            $metadataStandard = $params['metadata_standard'] ?? null;
            $harvestSource = $params['harvest_source'] ?? null;
            $dateFrom = $params['date_from'] ?? null;
            $dateTo = $params['date_to'] ?? null;
            $mapExtent = $params['map_extent'] ?? null;
            $spatialMode = $params['spatial_mode'] ?? 'any';
            $updatedAt = $params['updated_at'] ?? null;
            $topics = $params['topics'] ?? null;
            if ($topics && !is_array($topics)) {
                $topics = [$topics];
            }
            error_log('DEBUG: map_extent=' . var_export($mapExtent, true));
            error_log('DEBUG: spatial_mode=' . var_export($spatialMode, true));

            $result = $this->gisData->findAllPublic(
                $page,
                $limit,
                $search,
                $metadataStandard,
                $harvestSource,
                $dateFrom,
                $dateTo,
                $topics,
                $updatedAt,
                $mapExtent,
                $spatialMode
            );
            $datasets = $result['items'] ?? [];

            // Get all topics for the filter
            $topics = $this->topic->all();

            $publicDocuments = $this->document->getPublicDocuments();
            return $this->twig->render($response, 'catalog/public.twig', [
                'datasets' => $datasets,
                'documents' => $publicDocuments,
                'request' => $request,
                'topics' => $topics,
                'pagination' => [
                    'page' => $result['page'] ?? 1,
                    'pages' => $result['pages'] ?? 1,
                    'total' => $result['total'] ?? 0
                ],
                'map_center' => $request->getQueryParams()['map_center'] ?? '',
                'map_zoom' => $request->getQueryParams()['map_zoom'] ?? ''
            ]);
        } catch (\Exception $e) {
            error_log("Error getting public datasets: " . $e->getMessage());
            return $this->jsonResponse($response, ['error' => 'Failed to get public datasets'], 500);
        }
    }

    public function updatePublicStatus(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)$args['id'];
            
            // Get form data using getParsedBody
            $data = $request->getParsedBody();
            error_log("=== Update Public Status Debug ===");
            error_log("Dataset ID: " . $id);
            error_log("Form data: " . print_r($data, true));
            
            // Convert string '1'/'0' to boolean
            $isPublic = isset($data['is_public']) ? filter_var($data['is_public'], FILTER_VALIDATE_BOOLEAN) : null;
            error_log("Converted is_public value: " . ($isPublic ? 'true' : 'false'));
            
            if ($isPublic === null) {
                error_log("Error: Missing is_public parameter");
                return $this->jsonResponse($response, ['error' => 'Missing is_public parameter'], 400);
            }

            $dataset = $this->gisData->findById($id);
            if (!$dataset) {
                error_log("Error: Dataset not found");
                return $this->jsonResponse($response, ['error' => 'Dataset not found'], 404);
            }

            error_log("Updating public status for dataset: " . $dataset['title']);
            $success = $this->gisData->updatePublicStatus($id, $isPublic);
            if (!$success) {
                error_log("Error: Failed to update public status");
                throw new \Exception('Failed to update public status');
            }

            error_log("Public status updated successfully");
            error_log("=== End Update Public Status Debug ===");
            return $this->jsonResponse($response, ['message' => 'Public status updated successfully']);
        } catch (\Exception $e) {
            error_log("Error updating public status: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return $this->jsonResponse($response, ['error' => 'Failed to update public status'], 500);
        }
    }

    public function updateStatus(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)$args['id'];
            
            // Get form data using getParsedBody
            $data = $request->getParsedBody();
            error_log("=== Update Status Debug ===");
            error_log("Dataset ID: " . $id);
            error_log("Form data: " . print_r($data, true));
            
            // Validate status value
            $status = $data['status'] ?? null;
            if (!in_array($status, ['draft', 'published', 'archived'])) {
                error_log("Error: Invalid status value");
                return $this->jsonResponse($response, ['error' => 'Invalid status value'], 400);
            }

            $dataset = $this->gisData->findById($id);
            if (!$dataset) {
                error_log("Error: Dataset not found");
                return $this->jsonResponse($response, ['error' => 'Dataset not found'], 404);
            }

            error_log("Updating status for dataset: " . $dataset['title']);
            $success = $this->gisData->updateGisData($id, ['status' => $status]);
            if (!$success) {
                error_log("Error: Failed to update status");
                throw new \Exception('Failed to update status');
            }

            error_log("Status updated successfully");
            error_log("=== End Update Status Debug ===");
            return $this->jsonResponse($response, ['message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            error_log("Error updating status: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return $this->jsonResponse($response, ['error' => 'Failed to update status'], 500);
        }
    }

    public function getPublicDataset(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)$args['id'];
            $dataset = $this->gisData->findPublicById($id);

            if (!$dataset) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Dataset not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $dataset['is_harvested'] = $this->gisData->isHarvestedDataset($id);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $dataset
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function add(Request $request, Response $response): Response
    {
        // Check if the request is an AJAX (fetch) request.
        $isAjax = ( $request->getHeaderLine( "X-Requested-With" ) === "XMLHttpRequest" );

        try {
            $input = $request->getParsedBody();
            $files = $request->getUploadedFiles();

            // Debug logging
            error_log("=== Add Dataset Debug ===");
            error_log("Input data: " . print_r($input, true));
            error_log("Raw spatial_extent: " . (isset($input['spatial_extent']) ? $input['spatial_extent'] : 'not set'));

            // Validate input using injected validator
            if (!$this->validator->validate($input)) {
                error_log("Validation failed: " . print_r($this->validator->getErrors(), true));
                if ($isAjax) {
                    return $this->jsonResponse($response, [ "success" => false, "errors" => $this->validator->getErrors() ], 400);
                } else {
                    return $this->twig->render($response, 'catalog/add.twig', [ "errors" => $this->validator->getErrors(), "input" => $input ]);
                }
            }

            // Handle spatial extent
            $spatialExtent = null;
            if (isset($input['spatial_extent'])) {
                try {
                    $spatialExtent = json_decode($input['spatial_extent'], true);
                    error_log("Decoded spatial extent: " . print_r($spatialExtent, true));
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        error_log("JSON decode error: " . json_last_error_msg());
                        throw new \Exception("Invalid spatial extent JSON");
                    }
                } catch (\Exception $e) {
                    error_log("Error processing spatial extent: " . $e->getMessage());
                    if ($isAjax) {
                        return $this->jsonResponse($response, [ "success" => false, "errors" => [ "spatial_extent" => "Error processing spatial extent: " . $e->getMessage() ] ], 400);
                    } else {
                        throw new \Exception("Error processing spatial extent: " . $e->getMessage());
                    }
                }
            } else {
                error_log("No spatial extent provided in input");
            }

            // Format coordinates for PostGIS
            $west = isset($spatialExtent['coordinates'][0][0][0]) ? floatval($spatialExtent['coordinates'][0][0][0]) : 0;
            $east = isset($spatialExtent['coordinates'][0][2][0]) ? floatval($spatialExtent['coordinates'][0][2][0]) : 0;
            $south = isset($spatialExtent['coordinates'][0][0][1]) ? floatval($spatialExtent['coordinates'][0][0][1]) : 0;
            $north = isset($spatialExtent['coordinates'][0][2][1]) ? floatval($spatialExtent['coordinates'][0][2][1]) : 0;

            error_log("Processed coordinates: west=$west, east=$east, south=$south, north=$north");

            // Create GeoJSON polygon for spatial extent
            $spatialExtent = [
                'type' => 'Polygon',
                'coordinates' => [[
                    [$west, $south],
                    [$west, $north],
                    [$east, $north],
                    [$east, $south],
                    [$west, $south]
                ]]
            ];

            error_log("Final spatial extent: " . json_encode($spatialExtent));

            // Prepare dataset data
            $isPublic = isset($input['is_public']) && ($input['is_public'] === '1' || $input['is_public'] === 'on' || $input['is_public'] === 1 || $input['is_public'] === true);
            $datasetData = [
                'title' => $input['title'],
                'description' => $input['description'],
                'spatial_extent' => json_encode($spatialExtent),
                'is_public' => $isPublic,
                'status' => $input['status'] ?? 'draft',
                'created_by' => $request->getAttribute('user_id') ?? $_SESSION['user_id'] ?? null,
                'wms_url' => $input['wms_url'] ?? null,
                'wms_layer' => $input['wms_layer'] ?? null
            ];

            // Ensure created_by is set
            if (empty($datasetData['created_by'])) {
                error_log("Warning: created_by is empty, using session user_id");
                $datasetData['created_by'] = $_SESSION['user_id'] ?? null;
            }

            error_log("Dataset data to create: " . print_r($datasetData, true));

            // Create dataset first
            $datasetId = $this->gisData->createGisData($datasetData);
            if (!$datasetId) {
                error_log("Failed to create dataset");
                if ($isAjax) {
                    return $this->jsonResponse($response, [ "success" => false, "errors" => [ "general" => "Failed to create dataset" ] ], 500);
                } else {
                    throw new \Exception('Failed to create dataset');
                }
            }

            error_log("Dataset created with ID: " . $datasetId);

            // Prepare metadata fields (as before)
            $metadataFields = [
                'title' => $input['title'] ?? '',
                'abstract' => $input['abstract'] ?? '',
                'purpose' => $input['purpose'] ?? '',
                'datasetLanguage' => $input['datasetLanguage'] ?? 'eng',
                'characterSet' => $input['characterSet'] ?? 'utf8',
                'topicCategory' => $input['topicCategory'] ?? '',
                'westBound' => $west,
                'eastBound' => $east,
                'southBound' => $south,
                'northBound' => $north,
                'crs' => $input['crs'] ?? 'EPSG:4326',
                'crsType' => $input['crsType'] ?? 'geographic',
                'spatialResolution' => $input['spatialResolution'] ?? '',
                'spatialRepresentationType' => $input['spatialRepresentationType'] ?? '',
                'spatialResolutionUnits' => $input['spatialResolutionUnits'] ?? '',
                'spatialResolutionDistance' => $input['spatialResolutionDistance'] ?? '',
                'spatialResolutionVertical' => $input['spatialResolutionVertical'] ?? '',
                'spatialResolutionVerticalUnits' => $input['spatialResolutionVerticalUnits'] ?? '',
                'temporalStart' => $input['temporalStart'] ?? '',
                'temporalEnd' => $input['temporalEnd'] ?? '',
                'pointOfContactName' => $input['pointOfContactName'] ?? '',
                'pointOfContactOrg' => $input['pointOfContactOrg'] ?? '',
                'pointOfContactEmail' => $input['pointOfContactEmail'] ?? '',
                'metadataPointOfContactName' => $input['metadataPointOfContactName'] ?? '',
                'metadataPointOfContactOrg' => $input['metadataPointOfContactOrg'] ?? '',
                'metadataPointOfContactEmail' => $input['metadataPointOfContactEmail'] ?? '',
                'metadataPointOfContactRole' => $input['metadataPointOfContactRole'] ?? '',
                'publisherName' => $input['publisherName'] ?? '',
                'publisherOrg' => $input['publisherOrg'] ?? '',
                'publisherRole' => $input['publisherRole'] ?? '',
                'scope' => $input['scope'] ?? '',
                'lineage' => $input['lineage'] ?? '',
                'completeness' => $input['completeness'] ?? '',
                'logicalConsistency' => $input['logicalConsistency'] ?? '',
                'positionalAccuracy' => $input['positionalAccuracy'] ?? '',
                'temporalAccuracy' => $input['temporalAccuracy'] ?? '',
                'thematicAccuracy' => $input['thematicAccuracy'] ?? '',
                'distributionFormat' => $input['distributionFormat'] ?? '',
                'distributionFormatVersion' => $input['distributionFormatVersion'] ?? '',
                'distributionUrl' => $input['distributionUrl'] ?? '',
                'distributionTransferOptions' => $input['distributionTransferOptions'] ?? '',
                'distributionSize' => $input['distributionSize'] ?? '',
                'distributionUnits' => $input['distributionUnits'] ?? '',
                'accessConstraints' => $input['distributionAccessConstraints'] ?? 'otherRestrictions',
                'useConstraints' => $input['distributionUseConstraints'] ?? 'otherRestrictions',
                'useLimitation' => $input['distributionUseLimitation'] ?? '',
                'maintenanceFrequency' => $input['maintenanceFrequency'] ?? '',
                'maintenanceNote' => $input['maintenanceNote'] ?? '',
                'maintenanceDate' => $input['maintenanceDate'] ?? '',
                'maintenanceScope' => $input['maintenanceScope'] ?? '',
                'metadata_standard' => $input['metadata_standard'] ?? 'ISO 19115',
                'metadata_version' => $input['metadata_version'] ?? '2018'
            ];

            // Add keywords if present
            if (!empty($input['keywords'])) {
                $metadataFields['keywords'] = is_array($input['keywords']) ? $input['keywords'] : explode(',', $input['keywords']);
            }

            error_log("Metadata fields: " . print_r($metadataFields, true));

            // Generate metadata XML
            $metadataXml = $this->generateMetadataXml($metadataFields);
            error_log("Generated metadata XML: " . $metadataXml);

            // Calculate quality score
            $qualityScore = $this->calculateQualityScore($metadataXml);
            error_log("Calculated quality score: " . $qualityScore);

            // Create metadata record
            $metadataData = [
                'dataset_id' => $datasetId,
                'metadata_xml' => $metadataXml,
                'quality_score' => $qualityScore,
                'metadata_standard' => $input['metadata_standard'] ?? 'ISO 19115',
                'metadata_version' => $input['metadata_version'] ?? '2018'
            ];

            error_log("Metadata data to create: " . print_r($metadataData, true));

            $metadataId = $this->metadata->createMetadata($metadataData);
            if (!$metadataId) {
                error_log("Failed to create metadata");
                // Rollback dataset creation
                $this->gisData->deleteDataset($datasetId);
                if ($isAjax) {
                    return $this->jsonResponse($response, [ "success" => false, "errors" => [ "general" => "Failed to create metadata" ] ], 500);
                } else {
                    throw new \Exception('Failed to create metadata');
                }
            }

            error_log("Metadata created with ID: " . $metadataId);

            // Handle WMS metadata if provided (as before)
            if (!empty($input['wms_url']) && !empty($input['wms_layer'])) {
                try {
                    // Format WMS URL to ensure it ends with /wms
                    $wmsUrl = $input['wms_url'];
                    if (!str_ends_with($wmsUrl, '/wms')) {
                        $wmsUrl = str_ends_with($wmsUrl, '/') ? 
                            $wmsUrl . 'wms' : 
                            $wmsUrl . '/wms';
                    }

                    // Update dataset with formatted WMS URL
                    $this->gisData->updateGisData($datasetId, [
                        'wms_url' => $wmsUrl,
                        'wms_layer' => $input['wms_layer']
                    ]);

                    error_log("Updated dataset with WMS details – URL: " . $wmsUrl . ", Layer: " . $input['wms_layer']);
                } catch (\Exception $e) {
                    error_log("Error updating WMS details: " . $e->getMessage());
                    // Continue processing even if WMS update fails
                }
            }

            error_log("=== End Add Dataset Debug ===");

            $this->flash('success', 'Dataset created successfully');
            if ($isAjax) {
                // Return a JSON success response with a redirect URL.
                return $this->jsonResponse($response, [ "success" => true, "redirect" => "/catalog/" . $datasetId ]);
            } else {
                return $response->withHeader("Location", "/catalog/" . $datasetId)->withStatus(302);
            }
        } catch (\Exception $e) {
            error_log('Error creating dataset: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            if ($isAjax) {
                return $this->jsonResponse($response, [ "success" => false, "errors" => [ "general" => "An error occurred while creating the dataset: " . $e->getMessage() ] ], 500);
            } else {
                return $this->twig->render($response, 'catalog/add.twig', [ "errors" => [ "general" => "An error occurred while creating the dataset: " . $e->getMessage() ], "input" => ($input ?? null) ]);
            }
        }
    }

    public function addForm(Request $request, Response $response): Response
    {
        // Fetch available templates
        $templates = $this->template->getAll();
        
        // Sort templates to show default template first
        usort($templates, function($a, $b) {
            if ($a['is_default'] && !$b['is_default']) return -1;
            if (!$a['is_default'] && $b['is_default']) return 1;
            return strcmp($a['name'], $b['name']);
        });

        return $this->twig->render($response, 'catalog/add.twig', [
            'templates' => $templates
        ]);
    }

    // Update getTemplateFields to use the template model
    public function getTemplateFields(Request $request, Response $response, array $args): Response
    {
        try {
            $templateId = (int) $args['id'];
            $template = $this->template->getById($templateId);
            
            if (!$template) {
                return $this->jsonResponse($response, [
                    'status' => 'error',
                    'error' => 'Template not found'
                ], 404);
            }

            $fields = $this->template->getFields($templateId);
            
            // Transform fields to include label and type
            $transformedFields = array_map(function($field) {
                return [
                    'name' => $field['field_name'],
                    'label' => ucwords(str_replace('_', ' ', $field['field_name'])),
                    'type' => $field['field_type'],
                    'required' => (bool)$field['is_required'],
                    'description' => $field['description'] ?? '',
                    'section' => $field['section'] ?? 'Additional Information',
                    'options' => $field['options'] ? json_decode($field['options'], true) : null
                ];
            }, $fields);

            return $this->jsonResponse($response, [
                'status' => 'success',
                'id' => $template['id'],
                'name' => $template['name'],
                'metadata_standard' => $template['metadata_standard'],
                'metadata_version' => $template['metadata_version'],
                'fields' => $transformedFields
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Error getting template fields: ' . $e->getMessage());
            return $this->jsonResponse($response, [
                'status' => 'error',
                'error' => 'Error getting template fields: ' . $e->getMessage()
            ], 500);
        }
    }

    private function jsonResponse(Response $response, $data, $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public function publicDatasetsPage(Request $request, Response $response): Response
    {
        $page = (int)($request->getQueryParams()['page'] ?? 1);
        $limit = (int)($request->getQueryParams()['limit'] ?? 10);
        $search = $request->getQueryParams()['q'] ?? '';
        $metadataStandard = $request->getQueryParams()['metadata_standard'] ?? null;
        $harvestSource = $request->getQueryParams()['harvest_source'] ?? null;
        $dateFrom = $request->getQueryParams()['date_from'] ?? null;
        $dateTo = $request->getQueryParams()['date_to'] ?? null;
        $updatedAt = $request->getQueryParams()['updated_at'] ?? null;
        $topics = $request->getQueryParams()['topics'] ?? null;
        if ($topics && !is_array($topics)) {
            $topics = [$topics];
        }
        $mapExtent = $request->getQueryParams()['map_extent'] ?? null;
        $spatialMode = $request->getQueryParams()['spatial_mode'] ?? 'any';
        error_log('DEBUG: [publicDatasetsPage] map_extent=' . var_export($mapExtent, true));
        error_log('DEBUG: [publicDatasetsPage] spatial_mode=' . var_export($spatialMode, true));

        $result = $this->gisData->findAllPublic(
            $page,
            $limit,
            $search,
            $metadataStandard,
            $harvestSource,
            $dateFrom,
            $dateTo,
            $topics,
            $updatedAt,
            $mapExtent,
            $spatialMode
        );
        $datasets = $result['items'] ?? [];

        // Get all topics for the filter
        $topics = $this->topic->all();

        $publicDocuments = $this->document->getPublicDocuments();
        return $this->twig->render($response, 'catalog/public.twig', [
            'datasets' => $datasets,
            'documents' => $publicDocuments,
            'request' => $request,
            'topics' => $topics,
            'pagination' => [
                'page' => $result['page'] ?? 1,
                'pages' => $result['pages'] ?? 1,
                'total' => $result['total'] ?? 0
            ],
            'map_center' => $request->getQueryParams()['map_center'] ?? '',
            'map_zoom' => $request->getQueryParams()['map_zoom'] ?? ''
        ]);
    }

    public function publicDatasetDetails(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $dataset = $this->gisData->findPublicById($id);
        if (!$dataset) {
            $response->getBody()->write('Dataset not found');
            return $response->withStatus(404);
        }
        $dataset['is_harvested'] = $this->gisData->isHarvestedDataset($id);
        
        // Get metadata for the dataset
        $metadata = $this->metadata->findByDatasetId($dataset['id']);
        if ($metadata) {
            $dataset['metadata_xml'] = $metadata['metadata_xml'];
            $dataset['quality_score'] = $metadata['quality_score'];
            $dataset['metadata_standard'] = $metadata['metadata_standard'];
            $dataset['metadata_version'] = $metadata['metadata_version'];
        }
        
        return $this->twig->render($response, 'catalog/dataset_details.twig', [
            'dataset' => $dataset,
            'is_public_view' => true,
            'request' => $request
        ]);
    }

    /**
     * Fetch metadata from a GIS data URL
     */
    public function fetchMetadataFromUrl(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            if (!isset($data['url'])) {
                return $this->jsonResponse($response, [
                    'status' => 'error',
                    'error' => 'URL is required'
                ], 400);
            }

            $url = $data['url'];
            if (empty($url)) {
                return $this->jsonResponse($response, [
                    'status' => 'error',
                    'error' => 'URL cannot be empty'
                ], 400);
            }

            // Validate URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return $this->jsonResponse($response, [
                    'status' => 'error',
                    'error' => 'Invalid URL format'
                ], 400);
            }

            // Initialize metadata array
            $metadata = [
                'title' => '',
                'description' => '',
                'abstract' => '',
                'crs' => 'EPSG:4326',
                'spatial_extent' => null,
                'temporal_extent' => null,
                'format' => '',
                'keywords' => [],
                'service_type' => null,
                'service_url' => $url,
                'layer_name' => ''
            ];

            // Parse URL to determine service type
            $parsedUrl = parse_url($url);
            $queryParams = [];
            if (isset($parsedUrl['query'])) {
                parse_str($parsedUrl['query'], $queryParams);
            }

            // Determine service type from URL or query parameters
            $serviceType = strtolower($queryParams['service'] ?? '');
            $isWfs = strpos($url, 'wfs') !== false || strpos($url, 'WFS') !== false || $serviceType === 'wfs';
            $isWms = strpos($url, 'wms') !== false || strpos($url, 'WMS') !== false || $serviceType === 'wms';
            $isGeoJson = strpos($url, '.geojson') !== false || strpos($url, 'application/json') !== false;

            if (!$isWfs && !$isWms && !$isGeoJson) {
                return $this->jsonResponse($response, [
                    'status' => 'error',
                    'error' => 'Unsupported service type. Only WMS, WFS, and GeoJSON are supported.'
                ], 400);
            }

            if ($isWfs) {
                // Handle WFS
                try {
                    // First try to get feature directly to get layer-specific metadata
                    $json = @file_get_contents($url);
                    if ($json !== false) {
                        $data = json_decode($json, true);
                        if ($data !== null && isset($data['type']) && $data['type'] === 'FeatureCollection') {
                            $metadata['format'] = 'WFS';
                            
                            // Extract layer name from typeName parameter
                            $layerName = $queryParams['typeName'] ?? '';
                            if (strpos($layerName, ':') !== false) {
                                $layerName = explode(':', $layerName)[1];
                            }
                            $metadata['title'] = $layerName ?: 'WFS Layer';
                            
                            // Try to extract description from properties of first feature
                            if (!empty($data['features'])) {
                                $firstFeature = $data['features'][0];
                                if (isset($firstFeature['properties'])) {
                                    $props = $firstFeature['properties'];
                                    $metadata['description'] = $props['description'] ?? $props['abstract'] ?? '';
                                    $metadata['abstract'] = $props['abstract'] ?? $props['description'] ?? '';
                                    
                                    // Extract keywords if available
                                    if (isset($props['keywords'])) {
                                        $metadata['keywords'] = is_array($props['keywords']) 
                                            ? $props['keywords'] 
                                            : explode(',', $props['keywords']);
                                    }
                                }
                            }

                            // Extract spatial extent from features
                            if (!empty($data['features'])) {
                                $bounds = [PHP_FLOAT_MAX, PHP_FLOAT_MAX, PHP_FLOAT_MIN, PHP_FLOAT_MIN];
                                
                                // Define the processCoordinates function first
                                $processCoordinates = function($coords) use (&$bounds, &$processCoordinates) {
                                    if (is_array($coords)) {
                                        if (isset($coords[0]) && isset($coords[1]) && is_numeric($coords[0]) && is_numeric($coords[1])) {
                                            // This is a point coordinate
                                            $bounds[0] = min($bounds[0], $coords[0]); // min lon
                                            $bounds[1] = min($bounds[1], $coords[1]); // min lat
                                            $bounds[2] = max($bounds[2], $coords[0]); // max lon
                                            $bounds[3] = max($bounds[3], $coords[1]); // max lat
                                        } else {
                                            // This is an array of coordinates, process each one
                                            foreach ($coords as $coord) {
                                                $processCoordinates($coord);
                                            }
                                        }
                                    }
                                };

                                // Process each feature's geometry
                                foreach ($data['features'] as $feature) {
                                    if (isset($feature['geometry']) && isset($feature['geometry']['coordinates'])) {
                                        $processCoordinates($feature['geometry']['coordinates']);
                                    }
                                }
                                
                                if ($bounds[0] !== PHP_FLOAT_MAX) {
                                    $metadata['spatial_extent'] = [
                                        'type' => 'Polygon',
                                        'coordinates' => [[
                                            [$bounds[0], $bounds[1]], // southwest
                                            [$bounds[2], $bounds[1]], // southeast
                                            [$bounds[2], $bounds[3]], // northeast
                                            [$bounds[0], $bounds[3]], // northwest
                                            [$bounds[0], $bounds[1]]  // back to southwest
                                        ]]
                                    ];
                                }
                            }

                            // Extract CRS if available
                            if (isset($data['crs']['properties']['name'])) {
                                $metadata['crs'] = $data['crs']['properties']['name'];
                            } else {
                                // Default to EPSG:4326 for WFS if no CRS specified
                                $metadata['crs'] = 'EPSG:4326';
                            }
                            
                            // If we got valid metadata, return it
                            if (!empty($metadata['title']) && $metadata['spatial_extent'] !== null) {
                                return $this->jsonResponse($response, [
                                    'status' => 'success',
                                    'metadata' => $metadata
                                ]);
                            }
                        }
                    }
                    
                    // If direct feature access failed or didn't provide enough metadata,
                    // try to get capabilities
                    $capabilitiesUrl = $url . (strpos($url, '?') === false ? '?' : '&') . 'SERVICE=WFS&REQUEST=GetCapabilities';
                    $xml = @simplexml_load_file($capabilitiesUrl);
                    
                    if ($xml !== false) {
                        // Extract metadata from capabilities
                        $metadata['format'] = 'WFS';
                        
                        // Try to find the specific layer in capabilities
                        $layerName = $queryParams['typeName'] ?? '';
                        if (!empty($layerName)) {
                            $xml->registerXPathNamespace('wfs', 'http://www.opengis.net/wfs');
                            $xml->registerXPathNamespace('gml', 'http://www.opengis.net/gml');
                            
                            // Try to find the layer in FeatureTypeList
                            $featureTypes = $xml->xpath('//wfs:FeatureType');
                            foreach ($featureTypes as $featureType) {
                                $typeName = (string)$featureType->Name;
                                if ($typeName === $layerName || strpos($typeName, $layerName) !== false) {
                                    $metadata['title'] = (string)$featureType->Title;
                                    $metadata['abstract'] = (string)$featureType->Abstract;
                                    break;
                                }
                            }
                        }
                        
                        // If we still don't have a title, use the service title
                        if (empty($metadata['title'])) {
                            $metadata['title'] = (string)$xml->Service->Title;
                            $metadata['abstract'] = (string)$xml->Service->Abstract;
                        }
                        
                        // Extract keywords
                        if (isset($xml->Service->KeywordList)) {
                            $metadata['keywords'] = array_map('trim', explode(',', (string)$xml->Service->KeywordList));
                        }
                    } else {
                        throw new \Exception('Could not access WFS service');
                    }
                } catch (\Exception $e) {
                    throw new \Exception('Error processing WFS service: ' . $e->getMessage());
                }
            } elseif ($isWms) {
                // Handle WMS
                try {
                    $capabilitiesUrl = $url . (strpos($url, '?') === false ? '?' : '&') . 'SERVICE=WMS&REQUEST=GetCapabilities';
                    $xml = @simplexml_load_file($capabilitiesUrl);
                    
                    if ($xml === false) {
                        throw new \Exception('Could not access WMS service');
                    }
                    
                    $metadata['format'] = 'WMS';
                    $metadata['title'] = (string)$xml->Service->Title;
                    $metadata['abstract'] = (string)$xml->Service->Abstract;
                    
                    // Extract keywords
                    if (isset($xml->Service->KeywordList)) {
                        $metadata['keywords'] = array_map('trim', explode(',', (string)$xml->Service->KeywordList));
                    }
                } catch (\Exception $e) {
                    throw new \Exception('Error processing WMS service: ' . $e->getMessage());
                }
            } elseif ($isGeoJson) {
                // Handle GeoJSON
                try {
                    $json = @file_get_contents($url);
                    if ($json === false) {
                        throw new \Exception('Could not access GeoJSON file');
                    }
                    
                    $data = json_decode($json, true);
                    if ($data === null) {
                        throw new \Exception('Invalid GeoJSON format');
                    }
                    
                    $metadata['format'] = 'GeoJSON';
                    $metadata['title'] = $data['properties']['title'] ?? basename($url);
                    $metadata['description'] = $data['properties']['description'] ?? '';
                    $metadata['abstract'] = $data['properties']['abstract'] ?? '';
                    
                    // Extract spatial extent
                    if (isset($data['bbox'])) {
                        $metadata['spatial_extent'] = [
                            'type' => 'Polygon',
                            'coordinates' => [[
                                [$data['bbox'][0], $data['bbox'][1]],
                                [$data['bbox'][2], $data['bbox'][1]],
                                [$data['bbox'][2], $data['bbox'][3]],
                                [$data['bbox'][0], $data['bbox'][3]],
                                [$data['bbox'][0], $data['bbox'][1]]
                            ]]
                        ];
                    }
                    
                    // Extract CRS if available
                    if (isset($data['crs']['properties']['name'])) {
                        $metadata['crs'] = $data['crs']['properties']['name'];
                    }
                } catch (\Exception $e) {
                    throw new \Exception('Error processing GeoJSON: ' . $e->getMessage());
                }
            } else {
                throw new \Exception('Unsupported GIS data format. Please provide a WMS, WFS, or GeoJSON URL.');
            }

            // Ensure we have at least some basic metadata
            if (empty($metadata['title'])) {
                $metadata['title'] = basename($url);
            }

            return $this->jsonResponse($response, [
                'status' => 'success',
                'metadata' => $metadata
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Error fetching metadata: ' . $e->getMessage());
            return $this->jsonResponse($response, [
                'status' => 'error',
                'error' => 'Error fetching metadata: ' . $e->getMessage()
            ], 400);
        }
    }
} 