<?php

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => getenv('DB_PORT') ?: '5432',
        'database' => getenv('DB_NAME') ?: 'novella',
        'username' => getenv('DB_USER') ?: 'postgres',
        'password' => getenv('DB_PASS') ?: '',
    ],
    'twig' => [
        'template_path' => __DIR__ . '/../templates',
        'cache_path' => __DIR__ . '/../var/cache/twig',
    ],
    'displayErrorDetails' => true,
    'logErrors' => true,
    'logErrorDetails' => true,
]; 