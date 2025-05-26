<?php

use GeoLibre\Controller\CatalogController;
use GeoLibre\Controller\TopicsController;
use GeoLibre\Controller\DocumentController;
use GeoLibre\Middleware\AuthMiddleware;

// Catalog routes
$app->get('/catalog', [CatalogController::class, 'index']);
$app->get('/catalog/{id}', [CatalogController::class, 'show']);
$app->get('/catalog/{id}/edit', [CatalogController::class, 'editForm']);
$app->post('/catalog/{id}/edit', [CatalogController::class, 'update']);
$app->post('/catalog/{id}/delete', [CatalogController::class, 'delete']);
$app->get('/api/catalog/public', [CatalogController::class, 'getPublicDatasets']);
$app->post('/api/catalog/{id}/public-status', [CatalogController::class, 'updatePublicStatus']);
$app->get('/catalog/add', [CatalogController::class, 'addForm']);
$app->post('/catalog/add', [CatalogController::class, 'add']);
$app->post('/catalog/fetch-metadata', [CatalogController::class, 'fetchMetadataFromUrl']);

// Topics routes
$app->get('/topics', [TopicsController::class, 'index']);
$app->get('/topics/create', [TopicsController::class, 'create']);
$app->post('/topics', [TopicsController::class, 'store']);
$app->get('/topics/{id}/edit', [TopicsController::class, 'edit']);
$app->post('/topics/{id}', [TopicsController::class, 'update']);
$app->post('/topics/{id}/delete', [TopicsController::class, 'delete']); 

// Document routes
$app->group('/documents', function (RouteCollectorProxy $group) {
    $group->get('', [DocumentController::class, 'index'])->setName('documents.index');
    $group->get('/create', [DocumentController::class, 'create'])->setName('documents.create');
    $group->post('', [DocumentController::class, 'store'])->setName('documents.store');
    $group->get('/{id}/edit', [DocumentController::class, 'edit'])->setName('documents.edit');
    $group->post('/{id}', [DocumentController::class, 'update'])->setName('documents.update');
    $group->post('/{id}/delete', [DocumentController::class, 'delete'])->setName('documents.delete');
    $group->post('/{id}/toggle-public', [DocumentController::class, 'togglePublic'])->setName('documents.toggle-public');
})->add(new AuthMiddleware()); 