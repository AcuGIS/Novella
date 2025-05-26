<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use GeoLibre\Service\OaiPmhService;
use GeoLibre\Middleware\CsrfMiddleware;
use Doctrine\DBAL\Connection;
use Slim\Views\Twig;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        OaiPmhService::class => function (Connection $db) {
            return new OaiPmhService(
                $db,
                $_ENV['OAI_REPOSITORY_NAME'] ?? 'GeoLibre GIS Catalog',
                $_ENV['OAI_BASE_URL'] ?? 'http://localhost:8080/oai',
                $_ENV['OAI_ADMIN_EMAIL'] ?? 'admin@example.com'
            );
        },
        CsrfMiddleware::class => function (Twig $twig) {
            return new CsrfMiddleware($twig);
        },
    ]);
}; 