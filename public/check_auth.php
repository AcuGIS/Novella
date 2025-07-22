<?php

require __DIR__ . '/../vendor/autoload.php';

use Novella\Auth\Auth;
use Novella\Database\Database;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
    $auth = Auth::getInstance($db);
    
    $response = [
        'is_logged_in' => $auth->isLoggedIn(),
        'current_user' => null,
        'permissions' => []
    ];
    
    if ($auth->isLoggedIn()) {
        $user = $auth->getCurrentUser();
        $response['current_user'] = [
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'is_active' => $user->isActive()
        ];
        
        // Check specific permissions
        $response['permissions'] = [
            'edit_dataset' => $user->hasPermission('edit_dataset'),
            'publish_dataset' => $user->hasPermission('publish_dataset'),
            'manage_topics' => $user->hasPermission('manage_topics'),
            'manage_keywords' => $user->hasPermission('manage_keywords'),
            'manage_harvest' => $user->hasPermission('manage_harvest')
        ];
    }
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 