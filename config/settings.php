<?php

declare(strict_types=1);

return [
    'app' => [
        'name' => 'GeoLibre GIS Catalog',
        'env' => $_ENV['APP_ENV'] ?? 'production',
        'debug' => $_ENV['APP_DEBUG'] ?? false,
        'url' => $_ENV['APP_URL'] ?? 'https://your-domain.com',
        'secret' => $_ENV['APP_SECRET'] ?? 'your-secret-key-here',
    ],
    'oai' => [
        'repository_name' => $_ENV['OAI_REPOSITORY_NAME'] ?? 'GeoLibre GIS Catalog',
        'admin_email' => $_ENV['OAI_ADMIN_EMAIL'] ?? 'admin@example.com',
        'deleted_record' => $_ENV['OAI_DELETED_RECORD'] ?? 'no',
        'granularity' => $_ENV['OAI_GRANULARITY'] ?? 'YYYY-MM-DDThh:mm:ssZ',
    ],
    'logging' => [
        'level' => $_ENV['LOG_LEVEL'] ?? 'error',
        'path' => $_ENV['LOG_PATH'] ?? __DIR__ . '/../var/logs/app.log',
    ],
    'cache' => [
        'driver' => $_ENV['CACHE_DRIVER'] ?? 'file',
        'path' => $_ENV['CACHE_PATH'] ?? __DIR__ . '/../var/cache',
    ],
    'db' => [
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'port' => $_ENV['DB_PORT'] ?? 5432,
        'database' => $_ENV['DB_DATABASE'] ?? 'geolibre',
        'username' => $_ENV['DB_USERNAME'] ?? 'postgres',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
    ],
    'jwt' => [
        'secret' => $_ENV['JWT_SECRET'] ?? 'your-secret-key',
        'algorithm' => $_ENV['JWT_ALGORITHM'] ?? 'HS256',
        'expiry' => (int)($_ENV['JWT_EXPIRY'] ?? 3600),
    ],
    'displayErrorDetails' => false,
    'logErrors' => true,
    'logErrorDetails' => true,
]; 