<?php

declare(strict_types=1);

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use GeoLibre\Controller\HomeController;
use GeoLibre\Controller\CatalogController;
use GeoLibre\Controller\OaiPmhController;
use GeoLibre\Controller\MetadataController;
use GeoLibre\Controller\HarvestController;
use GeoLibre\Controller\SecurityController;
use GeoLibre\Middleware\AuthMiddleware;
use GeoLibre\Middleware\AdminMiddleware;
use GeoLibre\Controller\ApiAuthController;
use GeoLibre\Controller\StatsController;
use GeoLibre\Middleware\JwtAuthMiddleware;
use Twig\Environment as Twig;
use GeoLibre\Controller\TopicsController;
use GeoLibre\Controller\DocumentController;
use GeoLibre\Controller\ViewerController;
use GeoLibre\Middleware\HarvesterMiddleware;
use GeoLibre\Controller\AboutController;

// Debug logging
error_log("Loading routes file: " . __FILE__);
file_put_contents('/tmp/geolibre_routes.log', "Routes file loaded at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

return function (App $app) {
    error_log("Registering routes in " . __FILE__);
    file_put_contents('/tmp/geolibre_routes.log', "Starting route registration at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    
    // Public routes (no authentication required)
    $app->group('', function (RouteCollectorProxy $group) {
        error_log("Registering public routes");
        // About page (public)
        $group->get('/about', [AboutController::class, 'index'])->setName('about.index');

        // Authentication routes
        $group->group('/auth', function (RouteCollectorProxy $authGroup) {
            $authGroup->get('/login', [SecurityController::class, 'login'])->setName('login');
            $authGroup->post('/login', [SecurityController::class, 'login'])->setName('login.post');
            $authGroup->get('/logout', [SecurityController::class, 'logout'])->setName('logout');
        });

        // Public datasets page (HTML)
        $group->get('/public', [CatalogController::class, 'publicDatasetsPage'])->setName('public.datasets.page');

        // Viewer routes (public)
        $group->group('/viewer', function (RouteCollectorProxy $viewerGroup) {
            $viewerGroup->get('', [ViewerController::class, 'index'])->setName('viewer.index');
            $viewerGroup->get('/dataset/{id}', [ViewerController::class, 'getDatasetData'])->setName('viewer.dataset');
        });

        // Public API routes
        $group->group('/api/public', function (RouteCollectorProxy $publicGroup) {
            $publicGroup->get('/stats', [StatsController::class, 'getStats'])->setName('api.public.stats');
            
            // Public datasets
            $publicGroup->get('/datasets', [CatalogController::class, 'getPublicDatasets'])->setName('api.public.datasets.list');
            $publicGroup->get('/datasets/{id}', [CatalogController::class, 'getPublicDataset'])->setName('api.public.datasets.get');
            
            // Public metadata
            $publicGroup->get('/metadata', [MetadataController::class, 'searchPublic'])->setName('api.public.metadata.list');
            $publicGroup->get('/metadata/{id}', [MetadataController::class, 'getPublicMetadata'])->setName('api.public.metadata.get');
            
            // Authentication API routes
            $publicGroup->post('/auth/login', [ApiAuthController::class, 'login'])->setName('api.auth.login');
            $publicGroup->post('/auth/logout', [ApiAuthController::class, 'logout'])->setName('api.auth.logout');
            $publicGroup->post('/auth/register', [ApiAuthController::class, 'register'])->setName('api.auth.register');
        });

        // OAI-PMH base route
        error_log("Registering OAI-PMH base routes");
        $group->get('/oai', [OaiPmhController::class, 'handle'])->setName('oai.handle');
        $group->post('/oai', [OaiPmhController::class, 'handle'])->setName('oai.handle');

        // Public dataset details page (HTML)
        $group->get('/public/dataset/{id}', [CatalogController::class, 'publicDatasetDetails'])->setName('public.dataset.details');

        // Add WMS layers API endpoint
        $group->post('/api/wms-layers', [HarvestController::class, 'getWmsLayers'])->setName('api.wms.layers');
    });

    // Public document download route (no authentication required)
    $app->get('/documents/download/{filename}', [DocumentController::class, 'download'])->setName('documents.download');

    // Protected routes (require authentication)
    $app->group('', function (RouteCollectorProxy $group) {
        error_log("Registering protected routes");
        // Home page
        $group->get('/', [HomeController::class, 'index'])->setName('home');

        // API routes
        $group->group('/api', function (RouteCollectorProxy $apiGroup) {
            // Protected API routes
            $apiGroup->group('/private', function (RouteCollectorProxy $group) {
                $group->get('/profile', [SecurityController::class, 'getProfile'])->setName('api.private.profile');
            })->add(JwtAuthMiddleware::class);

            // Dataset routes (protected)
            $apiGroup->group('/datasets', function (RouteCollectorProxy $datasetGroup) {
                $datasetGroup->get('', [CatalogController::class, 'getDatasets'])->setName('api.datasets.list');
                $datasetGroup->get('/{id}', [CatalogController::class, 'getDataset'])->setName('api.datasets.get');
                $datasetGroup->post('', [CatalogController::class, 'createDataset'])->setName('api.datasets.create');
                $datasetGroup->put('/{id}', [CatalogController::class, 'updateDataset'])->setName('api.datasets.update');
                $datasetGroup->delete('/{id}', [CatalogController::class, 'deleteDataset'])->setName('api.datasets.delete');
            })->add(JwtAuthMiddleware::class);

            // Admin API routes
            $apiGroup->group('/admin', function (RouteCollectorProxy $group) {
                $group->get('/users', [SecurityController::class, 'getUsers'])->setName('api.admin.users');
            })->add(JwtAuthMiddleware::class)->add(AdminMiddleware::class);

            // Protected metadata routes
            $apiGroup->group('/metadata', function (RouteCollectorProxy $metadataGroup) {
                $metadataGroup->get('', [MetadataController::class, 'search'])->setName('api.metadata.list');
                $metadataGroup->get('/{id}', [MetadataController::class, 'getIso19115'])->setName('api.metadata.get');
                $metadataGroup->post('/iso19115', [MetadataController::class, 'createIso19115'])->setName('api.metadata.create');
                $metadataGroup->put('/iso19115/{id}', [MetadataController::class, 'updateIso19115'])->setName('api.metadata.update');
            })->add(JwtAuthMiddleware::class);
        });

        // Metadata routes
        $group->group('/metadata', function (RouteCollectorProxy $metadataGroup) {
            // Remove all template routes from metadata group since they're in catalog group
            $metadataGroup->get('', [MetadataController::class, 'index'])->setName('metadata.index');
        })->add(new AuthMiddleware());

        // Keep the catalog template routes as well for direct access
        $group->group('/catalog', function (RouteCollectorProxy $catalogGroup) {
            // Static routes first
            $catalogGroup->get('', [CatalogController::class, 'index'])->setName('catalog.index');
            $catalogGroup->get('/add', [CatalogController::class, 'addForm'])->setName('catalog.add');
            $catalogGroup->post('/add', [CatalogController::class, 'add'])->setName('catalog.add.post');
            $catalogGroup->post('/fetch-metadata', [CatalogController::class, 'fetchMetadataFromUrl'])->setName('catalog.fetch-metadata');

            // Metadata Templates routes (static routes)
            $catalogGroup->get('/templates', [MetadataController::class, 'templates'])->setName('catalog.templates.index');
            $catalogGroup->get('/templates/new', [MetadataController::class, 'templateNew'])->setName('catalog.templates.new');
            $catalogGroup->post('/templates/new', [MetadataController::class, 'templateCreate'])->setName('catalog.templates.create');
            $catalogGroup->get('/templates/{id}', [MetadataController::class, 'templateShow'])->setName('catalog.templates.show');
            $catalogGroup->get('/templates/{id}/edit', [MetadataController::class, 'templateEdit'])->setName('catalog.templates.edit');
            $catalogGroup->post('/templates/{id}/edit', [MetadataController::class, 'templateUpdate'])->setName('catalog.templates.update');
            $catalogGroup->post('/templates/{id}/delete', [MetadataController::class, 'templateDelete'])->setName('catalog.templates.delete');
            $catalogGroup->post('/templates/{id}/set-default', [MetadataController::class, 'templateSetDefault'])->setName('catalog.templates.set-default');
            $catalogGroup->get('/templates/{id}/fields', [CatalogController::class, 'getTemplateFields'])->setName('catalog.templates.fields');

            // Variable routes last
            $catalogGroup->get('/{id}', [CatalogController::class, 'show'])->setName('catalog.show');
            $catalogGroup->get('/{id}/edit', [CatalogController::class, 'editForm'])->setName('catalog.edit');
            $catalogGroup->post('/{id}/edit', [CatalogController::class, 'edit'])->setName('catalog.edit.post');
            $catalogGroup->post('/{id}/delete', [CatalogController::class, 'delete'])->setName('catalog.delete');
            $catalogGroup->post('/{id}/public-status', [CatalogController::class, 'updatePublicStatus'])->setName('catalog.public-status');
            $catalogGroup->post('/{id}/status', [CatalogController::class, 'updateStatus'])->setName('catalog.status');
        })->add(new AuthMiddleware());

        // Harvest routes
        error_log("Registering harvest routes");
        $group->group('/oai/harvest', function (RouteCollectorProxy $harvestGroup) {
            $harvestGroup->get('', [HarvestController::class, 'index'])->setName('harvest.index');
            $harvestGroup->post('', [HarvestController::class, 'add'])->setName('harvest.add');
            $harvestGroup->get('/{id}/edit', [HarvestController::class, 'edit'])->setName('harvest.edit');
            $harvestGroup->post('/{id}/edit', [HarvestController::class, 'edit'])->setName('harvest.edit');
            $harvestGroup->post('/{id}/delete', [HarvestController::class, 'delete'])->setName('harvest.delete');
            $harvestGroup->post('/{id}/run', [HarvestController::class, 'run'])->setName('harvest.run');
            $harvestGroup->get('/{id}/progress', [HarvestController::class, 'progress'])->setName('harvest.progress');
            $harvestGroup->get('/{id}/progress/view', [HarvestController::class, 'progressView'])->setName('harvest.progress.view');
            $harvestGroup->get('/{id}/layers', [HarvestController::class, 'layersSelectPage'])->setName('harvest.layers.select');
            $harvestGroup->post('/wms-import', [HarvestController::class, 'wmsImport'])->setName('harvest.wms.import');
            $harvestGroup->post('/{id}/layers', [HarvestController::class, 'saveLayers'])->setName('harvest.layers.save');
        })->add(new AuthMiddleware())->add(new HarvesterMiddleware());

        // Topics routes
        $group->group('/topics', function (RouteCollectorProxy $topicsGroup) {
            $topicsGroup->get('', [TopicsController::class, 'index'])->setName('topics.index');
            $topicsGroup->get('/create', [TopicsController::class, 'create'])->setName('topics.create');
            $topicsGroup->post('', [TopicsController::class, 'store'])->setName('topics.store');
            $topicsGroup->get('/{id}/edit', [TopicsController::class, 'edit'])->setName('topics.edit');
            $topicsGroup->post('/{id}', [TopicsController::class, 'update'])->setName('topics.update');
            $topicsGroup->post('/{id}/delete', [TopicsController::class, 'delete'])->setName('topics.delete');
        });

        // Document routes
        $group->group('/documents', function (RouteCollectorProxy $group) {
            $group->get('', [DocumentController::class, 'index'])->setName('documents.index');
            $group->get('/create', [DocumentController::class, 'create'])->setName('documents.create');
            $group->post('', [DocumentController::class, 'store'])->setName('documents.store');
            $group->get('/{id}/edit', [DocumentController::class, 'edit'])->setName('documents.edit');
            $group->post('/{id}', [DocumentController::class, 'update'])->setName('documents.update');
            $group->post('/{id}/delete', [DocumentController::class, 'delete'])->setName('documents.delete');
            $group->post('/{id}/toggle-public', [DocumentController::class, 'togglePublic'])->setName('documents.toggle-public');
        });
    })->add(new AuthMiddleware());

    // Admin routes (require both authentication and admin role)
    $app->group('', function (RouteCollectorProxy $group) {
        error_log("Registering admin routes");
        // Admin user management routes
        $group->group('/admin/users', function (RouteCollectorProxy $userGroup) {
            $userGroup->get('', [SecurityController::class, 'usersPage'])->setName('admin.users');
            $userGroup->get('/create', [SecurityController::class, 'createUser'])->setName('admin.users.create');
            $userGroup->post('/create', [SecurityController::class, 'createUser'])->setName('admin.users.create');
            $userGroup->get('/{id}/edit', [SecurityController::class, 'editUser'])->setName('admin.users.edit');
            $userGroup->post('/{id}/edit', [SecurityController::class, 'editUser'])->setName('admin.users.edit');
            $userGroup->post('/{id}/delete', [SecurityController::class, 'deleteUser'])->setName('admin.users.delete');
        });
    })->add(new AuthMiddleware())->add(new AdminMiddleware());

    // Debug route
    $app->get('/debug-routes', function ($request, $response) use ($app) {
        $routeCollector = $app->getRouteCollector();
        $routes = $routeCollector->getRoutes();
        $routeInfo = [];
        foreach ($routes as $route) {
            $routeInfo[] = [
                'methods' => $route->getMethods(),
                'pattern' => $route->getPattern(),
                'name' => $route->getName()
            ];
        }
        $response->getBody()->write(json_encode($routeInfo));
        return $response->withHeader('Content-Type', 'application/json');
    });

    error_log("Routes registration completed in " . __FILE__);
    file_put_contents('/tmp/geolibre_routes.log', "Route registration completed at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
}; 