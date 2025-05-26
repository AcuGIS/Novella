<?php

use DI\ContainerBuilder;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Doctrine\DBAL\DriverManager;
use Twig\Loader\FilesystemLoader;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        // Twig Loader
        \Twig\Loader\LoaderInterface::class => function () {
            return new FilesystemLoader(__DIR__ . '/View');
        },
        
        // Twig View
        Twig::class => function ($container) {
            $twig = new Twig(
                $container->get(\Twig\Loader\LoaderInterface::class),
                [
                    'cache' => false,
                    'auto_reload' => true,
                ]
            );
            
            // Add any global variables to the view
            $twig->getEnvironment()->addGlobal('user', $_SESSION['user'] ?? null);
            
            return $twig;
        },
        
        // Database connection
        \Doctrine\DBAL\Connection::class => function () {
            $connectionParams = [
                'dbname' => $_ENV['DB_NAME'] ?? 'geolibre',
                'user' => $_ENV['DB_USER'] ?? 'postgres',
                'password' => $_ENV['DB_PASSWORD'] ?? 'postgres',
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'driver' => 'pdo_pgsql',
            ];
            
            return DriverManager::getConnection($connectionParams);
        },
        
        // Models
        \GeoLibre\Model\Metadata::class => function ($container) {
            return new \GeoLibre\Model\Metadata($container->get(\Doctrine\DBAL\Connection::class));
        },
        
        \GeoLibre\Model\Dataset::class => function ($container) {
            return new \GeoLibre\Model\Dataset($container->get(\Doctrine\DBAL\Connection::class));
        },
        
        \GeoLibre\Model\GisData::class => function ($container) {
            return new \GeoLibre\Model\GisData($container->get(\Doctrine\DBAL\Connection::class));
        },
        
        \GeoLibre\Model\MetadataTemplate::class => function ($container) {
            return new \GeoLibre\Model\MetadataTemplate($container->get(\Doctrine\DBAL\Connection::class));
        },
        
        \GeoLibre\Model\User::class => function ($container) {
            return new \GeoLibre\Model\User($container->get(\Doctrine\DBAL\Connection::class));
        },

        \GeoLibre\Model\Topic::class => function ($container) {
            return new \GeoLibre\Model\Topic($container->get(\Doctrine\DBAL\Connection::class));
        },
        
        // Controllers
        \GeoLibre\Controller\MetadataController::class => function ($container) {
            return new \GeoLibre\Controller\MetadataController(
                $container->get(\GeoLibre\Model\Metadata::class),
                $container->get(\GeoLibre\Model\Dataset::class),
                $container->get(\GeoLibre\Model\MetadataTemplate::class),
                $container->get(Twig::class)
            );
        },
        
        \GeoLibre\Controller\HarvestController::class => function ($container) {
            return new \GeoLibre\Controller\HarvestController(
                new \GeoLibre\Model\HarvestSource($container->get(\Doctrine\DBAL\Connection::class)),
                new \GeoLibre\Model\OaiPmh($container->get(\Doctrine\DBAL\Connection::class)),
                $container->get(Twig::class),
                $container->get(\Doctrine\DBAL\Connection::class),
                $container->get(\GeoLibre\Model\Metadata::class)
            );
        },

        \GeoLibre\Controller\TopicsController::class => function ($container) {
            return new \GeoLibre\Controller\TopicsController(
                $container->get(\GeoLibre\Model\Topic::class),
                $container->get(\GeoLibre\Model\Dataset::class),
                $container->get(Twig::class)
            );
        },

        \GeoLibre\Controller\ViewerController::class => function (ContainerInterface $c) {
            return new \GeoLibre\Controller\ViewerController(
                $c->get(Twig::class),
                $c->get(\GeoLibre\Model\GisData::class),
                $c->get(\GeoLibre\Model\Dataset::class)
            );
        },

        \GeoLibre\Controller\AboutController::class => function (ContainerInterface $c) {
            return new \GeoLibre\Controller\AboutController(
                $c->get(Twig::class)
            );
        },
    ]);
}; 