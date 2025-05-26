<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use GeoLibre\Model\Metadata;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

// Database configuration
$dbParams = [
    'driver'   => 'pdo_pgsql',
    'host'     => 'localhost',
    'port'     => '5432',
    'dbname'   => 'geolibre',
    'user'     => 'postgres',
    'password' => 'postgres'
];

try {
    // Create database connection
    $connection = DriverManager::getConnection($dbParams);
    
    // Create Metadata model instance
    $metadata = new Metadata($connection);
    
    // Get all metadata records
    $qb = $connection->createQueryBuilder();
    $records = $qb->select('id', 'metadata_xml')
        ->from('metadata')
        ->executeQuery()
        ->fetchAllAssociative();
    
    echo "Found " . count($records) . " metadata records to update\n";
    
    // Update each record
    foreach ($records as $record) {
        if (empty($record['metadata_xml'])) {
            echo "Skipping record {$record['id']} - no metadata XML\n";
            continue;
        }
        
        try {
            // Calculate quality score
            $qualityScore = $metadata->calculateQualityScore($record['metadata_xml']);
            
            // Update the record
            $qb = $connection->createQueryBuilder();
            $qb->update('metadata')
                ->set('quality_score', ':score')
                ->where('id = :id')
                ->setParameter('score', $qualityScore)
                ->setParameter('id', $record['id'])
                ->executeQuery();
            
            echo "Updated record {$record['id']} with score: {$qualityScore}\n";
        } catch (\Exception $e) {
            echo "Error processing record {$record['id']}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "Quality score update completed\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
} 