<?php

require __DIR__ . '/../vendor/autoload.php';

use Novella\Database\Database;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
    
    // Get admin user details
    $stmt = $db->prepare("
        SELECT u.*, array_agg(r.name) as roles
        FROM users u
        LEFT JOIN user_roles ur ON u.id = ur.user_id
        LEFT JOIN roles r ON ur.role_id = r.id
        WHERE u.username = 'admin'
        GROUP BY u.id
    ");
    
    $stmt->execute();
    $admin = $stmt->fetch(\PDO::FETCH_ASSOC);
    
    if ($admin) {
        // Clean up the roles array - remove curly braces and quotes
        $roles = str_replace(['{', '}', '"'], '', $admin['roles']);
        $admin['roles'] = array_filter(explode(',', $roles));
        
        // Include password hash for debugging
        $admin['password_hash'] = $admin['password_hash'];
        
        echo json_encode([
            'status' => 'success',
            'user' => $admin
        ], JSON_PRETTY_PRINT);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Admin user not found'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 