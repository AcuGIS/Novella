<?php

declare(strict_types=1);

// Ensure environment variables are loaded
if (!isset($_ENV['DB_PASSWORD'])) {
    throw new RuntimeException('Database password not set in environment variables');
}

return [
    'driver' => 'pdo_pgsql',
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'port' => $_ENV['DB_PORT'] ?? '5432',
    'dbname' => $_ENV['DB_NAME'] ?? 'geolibre',
    'user' => $_ENV['DB_USER'] ?? 'postgres',
    'password' => $_ENV['DB_PASSWORD'],
    'charset' => 'utf8',
    'driverOptions' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ],
]; 