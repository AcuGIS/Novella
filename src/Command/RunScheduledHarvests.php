<?php

declare(strict_types=1);

namespace GeoLibre\Command;

use Doctrine\DBAL\Connection;
use GeoLibre\Model\HarvestSource;
use GeoLibre\Controller\HarvestController;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

class RunScheduledHarvests
{
    private Connection $db;
    private HarvestSource $harvestSource;
    private HarvestController $harvestController;

    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->harvestSource = new HarvestSource($db);
        $this->harvestController = new HarvestController($db);
    }

    public function run(): void
    {
        error_log("[SCHEDULER] Starting scheduled harvest check");
        
        // Get all sources that are due for harvest
        $dueSources = $this->harvestSource->getSourcesDueForHarvest();
        error_log("[SCHEDULER] Found " . count($dueSources) . " sources due for harvest");

        foreach ($dueSources as $source) {
            try {
                error_log("[SCHEDULER] Processing harvest source: " . $source['name']);
                
                // Create request and response objects for the harvest controller
                $requestFactory = new ServerRequestFactory();
                $responseFactory = new ResponseFactory();
                
                // Create a request to run the harvest
                $request = $requestFactory->createServerRequest('POST', "/oai/harvest/{$source['id']}/run");
                
                // Create a response object
                $response = $responseFactory->createResponse();
                
                // Run the harvest
                $this->harvestController->run($request, $response, ['id' => $source['id']]);
                
                error_log("[SCHEDULER] Successfully triggered harvest for source: " . $source['name']);
            } catch (\Exception $e) {
                error_log("[SCHEDULER] Error processing harvest source {$source['name']}: " . $e->getMessage());
                // Continue with next source even if one fails
                continue;
            }
        }
        
        error_log("[SCHEDULER] Completed scheduled harvest check");
    }
} 