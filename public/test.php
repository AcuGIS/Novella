<?php
header('Content-Type: application/json');

// Test database connection
try {
    $db = new PDO(
        "pgsql:host=" . $_ENV['DB_HOST'] . 
        ";port=" . $_ENV['DB_PORT'] . 
        ";dbname=" . $_ENV['DB_NAME'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbStatus = "connected";
} catch (PDOException $e) {
    $dbStatus = "error: " . $e->getMessage();
}

// Return system information
echo json_encode([
    'status' => 'success',
    'php_version' => PHP_VERSION,
    'extensions' => get_loaded_extensions(),
    'database' => $dbStatus,
    'environment' => [
        'DB_HOST' => $_ENV['DB_HOST'] ?? 'not set',
        'DB_NAME' => $_ENV['DB_NAME'] ?? 'not set',
        'DB_USER' => $_ENV['DB_USER'] ?? 'not set',
        'APP_ENV' => $_ENV['APP_ENV'] ?? 'not set'
    ],
    'server' => [
        'software' => $_SERVER['SERVER_SOFTWARE'],
        'document_root' => $_SERVER['DOCUMENT_ROOT'],
        'script_filename' => $_SERVER['SCRIPT_FILENAME']
    ]
]); 