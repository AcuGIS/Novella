<?php

require __DIR__ . '/../vendor/autoload.php';

use Novella\Models\HarvestSettings;
use Novella\Controllers\HarvestController;

// Get container
$container = require __DIR__ . '/../src/container.php';

// Get harvest settings model
$harvestSettings = $container->get(HarvestSettings::class);

// Get harvest controller
$harvestController = $container->get(HarvestController::class);

// Get due harvests
$result = $harvestSettings->getDueHarvests();
if (!$result['success']) {
    echo "Error getting due harvests: " . $result['message'] . "\n";
    exit(1);
}

$dueHarvests = $result['data'];
if (empty($dueHarvests)) {
    echo "No harvests due at this time.\n";
    exit(0);
}

echo "Found " . count($dueHarvests) . " harvest(s) due.\n";

// Process each due harvest
foreach ($dueHarvests as $harvest) {
    echo "\nProcessing harvest: {$harvest['name']}\n";
    echo "WMS URL: {$harvest['wms_url']}\n";
    echo "Layers: " . implode(', ', $harvest['layers']) . "\n";
    
    try {
        // Start the harvest
        $result = $harvestController->startHarvest([
            'wms_url' => $harvest['wms_url'],
            'layers' => $harvest['layers']
        ]);
        
        if ($result['status'] === 'success') {
            echo "Harvest started successfully.\n";
            
            // Update last run time
            $updateResult = $harvestSettings->updateLastRun($harvest['id']);
            if (!$updateResult['success']) {
                echo "Warning: Failed to update last run time: " . $updateResult['message'] . "\n";
            }
        } else {
            echo "Error starting harvest: " . ($result['message'] ?? 'Unknown error') . "\n";
        }
    } catch (Exception $e) {
        echo "Exception during harvest: " . $e->getMessage() . "\n";
    }
}

echo "\nFinished processing harvests.\n";
exit(0); 