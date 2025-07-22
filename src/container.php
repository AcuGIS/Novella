<?php

use DI\ContainerBuilder;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Psr\Container\ContainerInterface;
use PDO;
use Novella\Auth\Auth;
use Novella\Database\Database;
use Novella\Models\Metadata;

// Load environment variables if not already loaded
if (!isset($_ENV['DB_HOST'])) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// Default database settings for development
$defaultDbSettings = [
    'host' => 'localhost',
    'port' => '5432',
    'database' => 'novella',
    'username' => 'postgres',
    'password' => 'postgres' // Default development password
];

return [
    // Twig View Renderer
    Twig::class => function (ContainerInterface $c) {
        $settings = $c->get('settings');
        $twig = Twig::create($settings['twig']['template_path'], [
            'cache' => $settings['twig']['cache_path'],
            'auto_reload' => true,
            'debug' => true
        ]);
        
        // Add globals to Twig
        $twig->getEnvironment()->addGlobal('auth', $c->get('auth'));
        $twig->getEnvironment()->addGlobal('app', [
            'request' => [
                'uri' => [
                    'path' => $_SERVER['REQUEST_URI'] ?? '/'
                ]
            ]
        ]);
        
        return $twig;
    },

    // Auth service
    'auth' => function (ContainerInterface $c) {
        return Auth::getInstance($c->get(PDO::class));
    },

    // Metadata model
    Metadata::class => function (ContainerInterface $c) {
        return new Metadata($c->get(PDO::class), $c);
    },

    // Database connection
    PDO::class => function (ContainerInterface $c) {
        return Database::getInstance()->getConnection();
    },

    // Settings loader
    'settings' => function () {
        $settingsPath = __DIR__ . '/../config/settings.php';
        if (!file_exists($settingsPath)) {
            // Default settings if config file doesn't exist
            return [
                'twig' => [
                    'template_path' => __DIR__ . '/../templates',
                    'cache_path' => __DIR__ . '/../var/cache/twig',
                ],
            ];
        }
        return require $settingsPath;
    }
]; 