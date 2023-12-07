<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\AuthController;
use App\Controllers\UserController;
/**
 * @var RouteCollection $routes
 */

//$routes->resource('api/users',['filter' => 'authFilter']);

$routes->group("api", function ($routes) {
    // Ruta para iniciar sesión
    $routes->post('login', 'AuthController::index');

    // Ruta para obtener la lista de usuarios (protegida por el filtro de autenticación)
    $routes->get('index', 'UserController::index', ['filter' => 'authFilter']);

    // Ruta para crear un nuevo usuario (protegida por el filtro de autenticación)
    $routes->post('create', 'UserController::create', ['filter' => 'authFilter']);

    // Ruta para actualizar un usuario por ID (protegida por el filtro de autenticación)
    $routes->put('update/(:num)', 'UserController::update/$1', ['filter' => 'authFilter']);

    // Ruta para eliminar un usuario por ID (protegida por el filtro de autenticación)
    $routes->delete('delete/(:num)', 'UserController::delete/$1', ['filter' => 'authFilter']);

    // Ruta para descargar un PDF con la lista de usuarios (protegida por el filtro de autenticación)
    $routes->get('download-pdf', 'UserController::downloadPDF', ['filter' => 'authFilter']);
});

