<?php

require __DIR__ . '/../vendor/autoload.php';

use Novella\Database\Database;
use Novella\Models\User;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if admin user exists
    $stmt = $db->prepare("SELECT id FROM users WHERE username = 'admin'");
    $stmt->execute();
    $adminId = $stmt->fetchColumn();
    
    if (!$adminId) {
        // Create admin user
        $adminId = User::create($db, 'admin', 'admin@novella.local', 'Novella');
        
        // Get admin role ID
        $stmt = $db->prepare("SELECT id FROM roles WHERE name = 'admin'");
        $stmt->execute();
        $adminRoleId = $stmt->fetchColumn();
        
        if (!$adminRoleId) {
            // Create admin role if it doesn't exist
            $stmt = $db->prepare("INSERT INTO roles (name) VALUES ('admin') RETURNING id");
            $stmt->execute();
            $adminRoleId = $stmt->fetchColumn();
        }
        
        // Assign admin role to user
        $stmt = $db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
        $stmt->execute([$adminId, $adminRoleId]);
        
        echo "Admin user created successfully\n";
    } else {
        echo "Admin user already exists\n";
    }
    
    // Verify admin user
    $admin = User::findByUsername($db, 'admin');
    if ($admin) {
        echo "Admin user details:\n";
        echo "Username: " . $admin->getUsername() . "\n";
        echo "Email: " . $admin->getEmail() . "\n";
        echo "Roles: " . implode(', ', $admin->getRoles()) . "\n";
        echo "Is Active: " . ($admin->isActive() ? 'Yes' : 'No') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
} 