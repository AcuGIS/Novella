<?php

require __DIR__ . '/../vendor/autoload.php';

use Novella\Database\Database;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
    
    // Reset admin password
    $password = 'Novella';
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("
        UPDATE users 
        SET password_hash = ?, 
            updated_at = CURRENT_TIMESTAMP
        WHERE username = 'admin'
        RETURNING id
    ");
    
    $stmt->execute([$passwordHash]);
    $adminId = $stmt->fetchColumn();
    
    if ($adminId) {
        // Verify the update
        $stmt = $db->prepare("
            SELECT u.*, array_agg(r.name) as roles
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            WHERE u.id = ?
            GROUP BY u.id
        ");
        
        $stmt->execute([$adminId]);
        $admin = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // Clean up the roles array
        $roles = str_replace(['{', '}', '"'], '', $admin['roles']);
        $admin['roles'] = array_filter(explode(',', $roles));
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Admin password has been reset',
            'user' => [
                'id' => $admin['id'],
                'username' => $admin['username'],
                'email' => $admin['email'],
                'is_active' => $admin['is_active'],
                'roles' => $admin['roles'],
                'password_hash' => $admin['password_hash'] // Including for verification
            ]
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