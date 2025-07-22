<?php

require __DIR__ . '/../vendor/autoload.php';

use Novella\Database\Database;
use Novella\Auth\Auth;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
    $auth = Auth::getInstance($db);
    
    // Verify admin status
    if (!$auth->isLoggedIn() || !$auth->getCurrentUser()->hasPermission('edit_dataset')) {
        throw new Exception('Admin permissions required');
    }
    
    // List of dataset titles to make public (these are general/example datasets)
    $publicDatasets = [
        'World Map',
        'Countries',
        'Coastlines',
        'Boundary Lines',
        'Populated Places',
        'Tasmania',
        'Spearfish',
        'Manhattan (NY) landmarks',
        'Manhattan (NY) roads',
        'USA Population'
    ];
    
    // Update the datasets to be public
    $stmt = $db->prepare("
        UPDATE metadata_records 
        SET is_public = true 
        WHERE title = ANY(:titles)
        RETURNING id, title, is_public
    ");
    
    $stmt->execute(['titles' => $publicDatasets]);
    $updatedDatasets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get the count of public vs private datasets
    $countStmt = $db->query("
        SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN is_public = true THEN 1 END) as public_count,
            COUNT(CASE WHEN is_public = false THEN 1 END) as private_count
        FROM metadata_records
    ");
    $counts = $countStmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Updated ' . count($updatedDatasets) . ' datasets to public',
        'updated_datasets' => $updatedDatasets,
        'current_counts' => $counts
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 