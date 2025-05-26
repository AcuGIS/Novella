<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Create database connection
$connection = DriverManager::getConnection([
    'driver' => 'pdo_pgsql',
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'port' => $_ENV['DB_PORT'] ?? '5432',
    'dbname' => $_ENV['DB_NAME'] ?? 'geolibre',
    'user' => $_ENV['DB_USER'] ?? 'postgres',
    'password' => $_ENV['DB_PASSWORD'],
    'charset' => 'utf8'
]);

// Create migrations table if it doesn't exist
$connection->executeStatement('
    CREATE TABLE IF NOT EXISTS migrations (
        id SERIAL PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        executed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    )
');

// Get all migration files
$migrationsDir = __DIR__ . '/../migrations';
$migrationFiles = glob($migrationsDir . '/*.sql');
sort($migrationFiles); // Ensure migrations run in order

// Get already executed migrations
$executedMigrations = $connection->executeQuery('SELECT migration FROM migrations')
    ->fetchFirstColumn();

// Execute pending migrations
foreach ($migrationFiles as $migrationFile) {
    $migrationName = basename($migrationFile);
    
    if (in_array($migrationName, $executedMigrations)) {
        echo "Skipping {$migrationName} - already executed\n";
        continue;
    }

    echo "Executing {$migrationName}...\n";
    
    try {
        // Read and execute migration
        $sql = file_get_contents($migrationFile);
        $connection->executeStatement($sql);
        
        // Record migration
        $connection->insert('migrations', ['migration' => $migrationName]);
        
        echo "Successfully executed {$migrationName}\n";
    } catch (\Exception $e) {
        echo "Error executing {$migrationName}: {$e->getMessage()}\n";
        exit(1);
    }
}

echo "All migrations completed successfully!\n"; 