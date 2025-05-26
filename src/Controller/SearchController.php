<?php

namespace App\Controller;

use App\Service\DatasetService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    private DatasetService $datasetService;

    public function __construct(DatasetService $datasetService)
    {
        $this->datasetService = $datasetService;
    }

    #[Route('/api/search/datasets', name: 'search_datasets', methods: ['GET'])]
    public function searchDatasets(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        
        // Get search parameters
        $params = [
            'keywords' => $request->query->get('keywords', []),
            'spatial_extent' => $request->query->get('spatial_extent'),
            'temporal_start' => $request->query->get('temporal_start'),
            'temporal_end' => $request->query->get('temporal_end'),
            'metadata_standard' => $request->query->get('metadata_standard'),
            'sort_by' => $request->query->get('sort_by', 'created_at'),
            'sort_order' => $request->query->get('sort_order', 'desc')
        ];

        try {
            $results = $this->datasetService->search($query, $params, $page, $limit);
            
            return $this->json([
                'status' => 'success',
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/search/suggest', name: 'search_suggest', methods: ['GET'])]
    public function suggest(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');
        $limit = $request->query->getInt('limit', 5);
        
        try {
            $suggestions = $this->datasetService->suggest($query, $limit);
            
            return $this->json([
                'status' => 'success',
                'data' => $suggestions
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/search/facets', name: 'search_facets', methods: ['GET'])]
    public function getFacets(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');
        
        try {
            $facets = $this->datasetService->getFacets($query);
            
            return $this->json([
                'status' => 'success',
                'data' => $facets
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
} 