<?php
namespace App\Controllers;

use Slim\Routing\RouteCollectorProxy;

$app->group('/api', function (RouteCollectorProxy $api) {

    $api->group('/biblioteca_libros', function (RouteCollectorProxy $endpoint) {
        $endpoint->get('[/{id}]', Libro::class . ':read');
        $endpoint->post('', Libro::class . ':create');
        $endpoint->put('/{id}', Libro::class . ':update');
        $endpoint->delete('/{id}', Libro::class . ':delete');
    });
});
