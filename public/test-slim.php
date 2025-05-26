<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->get('/test', function ($request, $response) {
    $response->getBody()->write('Slim is working!');
    return $response;
});

$app->run(); 