<?php

declare(strict_types=1);

namespace GeoLibre\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DatabaseMigrateCommand extends Command
{
    protected static $defaultName = 'db:migrate';
    private array $dbConfig;

    public function __construct()
    {
        parent::__construct();
        
        // Debug: Print environment variables (excluding password)
        echo "Checking environment variables:\n";
        echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'not set') . "\n";
        echo "DB_PORT: " . ($_ENV['DB_PORT'] ?? 'not set') . "\n";
        echo "DB_NAME: " . ($_ENV['DB_NAME'] ?? 'not set') . "\n";
        echo "DB_USER: " . ($_ENV['DB_USER'] ?? 'not set') . "\n";
        echo "DB_PASSWORD is " . (isset($_ENV['DB_PASSWORD']) ? 'set' : 'not set') . "\n";
        echo "APP_ENV: " . ($_ENV['APP_ENV'] ?? 'not set') . "\n";
        
        // Try to load .env file manually if not loaded
        if (!isset($_ENV['DB_PASSWORD'])) {
            $envFile = __DIR__ . '/../../.env';
            if (file_exists($envFile)) {
                echo "Attempting to load .env file manually...\n";
                $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                        list($key, $value) = explode('=', $line, 2);
                        $key = trim($key);
                        $value = trim($value);
                        $_ENV[$key] = $value;
                        putenv("$key=$value");
                    }
                }
                echo "Manual .env load completed\n";
            } else {
                throw new \RuntimeException(".env file not found at: $envFile");
            }
        }
        
        $this->dbConfig = require __DIR__ . '/../../config/database.php';
    }

    protected function configure(): void
    {
        $this->setDescription('Run database migrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Running database migrations');

        try {
            $connection = DriverManager::getConnection($this->dbConfig);
            $this->createMigrationsTable($connection);
            $this->runMigrations($connection, $io);
            
            $io->success('Database migrations completed successfully');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Migration failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function createMigrationsTable(Connection $connection): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id SERIAL PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            executed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        )";
        $connection->executeStatement($sql);
    }

    private function runMigrations(Connection $connection, SymfonyStyle $io): void
    {
        $migrationsDir = __DIR__ . '/../../migrations';
        if (!is_dir($migrationsDir)) {
            mkdir($migrationsDir, 0777, true);
        }

        // Get executed migrations
        $executedMigrations = $connection->fetchFirstColumn(
            'SELECT migration FROM migrations ORDER BY id'
        );

        // Get all migration files
        $migrationFiles = glob($migrationsDir . '/*.sql');
        sort($migrationFiles);

        foreach ($migrationFiles as $file) {
            $migrationName = basename($file);
            if (!in_array($migrationName, $executedMigrations)) {
                $io->text("Executing migration: {$migrationName}");
                
                $sql = file_get_contents($file);
                $connection->executeStatement($sql);
                
                $connection->insert('migrations', ['migration' => $migrationName]);
                $io->text("✓ Completed: {$migrationName}");
            }
        }
    }
} 