<?php

declare(strict_types=1);

namespace GeoLibre\Controller;

use GeoLibre\Model\GisData;
use GeoLibre\Model\Dataset;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Doctrine\DBAL\Connection;

class ViewerController
{
    private Twig $twig;
    private GisData $gisData;
    private Dataset $dataset;
    private Connection $db;

    public function __construct(Twig $twig, GisData $gisData, Dataset $dataset, Connection $db)
    {
        $this->twig = $twig;
        $this->gisData = $gisData;
        $this->dataset = $dataset;
        $this->db = $db;
    }

    public function index(Request $request, Response $response): Response
    {
        // Get pagination and search parameters
        $page = (int)($request->getQueryParams()['page'] ?? 1);
        $limit = 1000; // Show all datasets initially
        $search = $request->getQueryParams()['search'] ?? null;

        // Get public datasets with pagination and search
        $result = $this->gisData->findAllPublic(
            page: $page,
            limit: $limit,
            search: $search,
            metadataStandard: null,
            harvestSource: null,
            dateFrom: null,
            dateTo: null,
            topics: null,
            updatedAt: null,
            mapExtent: null,
            spatialMode: 'any'
        );
        
        $datasets = $result['items'] ?? [];
        $totalItems = $result['total'] ?? 0;
        
        // Ensure we have an array of datasets
        if (!is_array($datasets)) {
            $datasets = [];
        }
        
        // Get all topics with their associated datasets
        $topics = $this->db->createQueryBuilder()
            ->select('t.*', 'dt.dataset_id')
            ->from('topics', 't')
            ->leftJoin('t', 'dataset_topics', 'dt', 't.id = dt.topic_id')
            ->orderBy('t.name', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        // Organize topics and their datasets
        $organizedTopics = [];
        foreach ($topics as $topic) {
            if (!isset($organizedTopics[$topic['id']])) {
                $organizedTopics[$topic['id']] = [
                    'id' => $topic['id'],
                    'name' => $topic['name'],
                    'description' => $topic['description'],
                    'datasets' => []
                ];
            }
            if ($topic['dataset_id']) {
                $organizedTopics[$topic['id']]['datasets'][] = $topic['dataset_id'];
            }
        }

        // Add all necessary details to each dataset
        foreach ($datasets as &$dataset) {
            // Get full dataset details including metadata
            $fullDataset = $this->dataset->getWithMetadata($dataset['id']);
            if ($fullDataset) {
                // Merge the full dataset details
                $dataset = array_merge($dataset, $fullDataset);
            }

            // Ensure spatial_extent is always GeoJSON
            if (!empty($dataset['spatial_extent']) && is_string($dataset['spatial_extent']) && !str_starts_with(trim($dataset['spatial_extent']), '{')) {
                $result = $this->db->executeQuery(
                    'SELECT ST_AsGeoJSON(?) as geojson',
                    [$dataset['spatial_extent']]
                )->fetchAssociative();
                if ($result && isset($result['geojson'])) {
                    $dataset['spatial_extent'] = $result['geojson'];
                }
            }

            // Get topics for this dataset
            $datasetTopics = $this->db->createQueryBuilder()
                ->select('t.id', 't.name')
                ->from('topics', 't')
                ->innerJoin('t', 'dataset_topics', 'dt', 't.id = dt.topic_id')
                ->where('dt.dataset_id = :dataset_id')
                ->setParameter('dataset_id', $dataset['id'])
                ->executeQuery()
                ->fetchAllAssociative();

            $dataset['topics'] = $datasetTopics;
        }

        return $this->twig->render($response, 'viewer.twig', [
            'datasets' => $datasets,
            'topics' => array_values($organizedTopics),
            'title' => 'GIS Data Viewer',
            'currentPage' => $page,
            'totalItems' => $totalItems,
            'search' => $search
        ]);
    }

    public function getDatasetData(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)$args['id'];
            error_log("Fetching dataset data for ID: " . $id);

            // Try to fetch all features for this dataset
            $features = $this->gisData->findFeaturesByDatasetId($id);
            error_log("Features found: " . ($features ? count($features) : 0));

            if ($features && count($features) > 0) {
                $featureCollection = [
                    'type' => 'FeatureCollection',
                    'features' => $features
                ];
                error_log("Returning feature collection with " . count($features) . " features");
                $response->getBody()->write(json_encode($featureCollection));
                return $response->withHeader('Content-Type', 'application/json');
            }

            // Fallback: Get the dataset with its geometry using GisData model
            error_log("No features found, trying to get dataset with geometry");
            $dataset = $this->gisData->findById($id);
            
            if (!$dataset) {
                error_log("Dataset not found for ID: " . $id);
                $response->getBody()->write(json_encode([
                    'error' => 'Dataset not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            error_log("Dataset found, checking spatial extent");
            // Format the geometry as proper GeoJSON with CRS
            if (isset($dataset['spatial_extent'])) {
                $geometry = is_string($dataset['spatial_extent']) ? 
                    json_decode($dataset['spatial_extent'], true) : 
                    $dataset['spatial_extent'];
                
                error_log("Spatial extent: " . json_encode($geometry));

                if ($geometry) {
                    $featureCollection = [
                        'type' => 'FeatureCollection',
                        'features' => [[
                            'type' => 'Feature',
                            'geometry' => $geometry,
                            'properties' => [
                                'title' => $dataset['title'],
                                'description' => $dataset['description'] ?? ''
                            ]
                        ]]
                    ];
                    error_log("Returning feature collection with geometry");
                    $response->getBody()->write(json_encode($featureCollection));
                    return $response->withHeader('Content-Type', 'application/json');
                } else {
                    error_log("Invalid geometry format");
                    $response->getBody()->write(json_encode([
                        'error' => 'Invalid geometry format'
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            } else {
                error_log("No spatial extent found for dataset");
                $response->getBody()->write(json_encode([
                    'error' => 'No spatial extent found for this dataset'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
        } catch (\Exception $e) {
            error_log("Error in getDatasetData: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $response->getBody()->write(json_encode([
                'error' => 'Internal server error: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
} 