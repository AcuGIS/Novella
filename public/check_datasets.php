<?php

require __DIR__ . '/../vendor/autoload.php';

use Novella\Database\Database;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
    
    // Get all datasets with their public status
    $stmt = $db->query("
        SELECT 
            id, 
            title, 
            is_public,
            created_at,
            updated_at
        FROM metadata_records 
        ORDER BY created_at DESC
    ");
    
    $datasets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Count public vs private datasets
    $publicCount = 0;
    $privateCount = 0;
    foreach ($datasets as $dataset) {
        if ($dataset['is_public']) {
            $publicCount++;
        } else {
            $privateCount++;
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'total_datasets' => count($datasets),
        'public_datasets' => $publicCount,
        'private_datasets' => $privateCount,
        'datasets' => $datasets
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 