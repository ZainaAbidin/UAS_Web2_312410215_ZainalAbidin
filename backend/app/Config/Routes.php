<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->options('api/(:any)', static function() {
    return service('response')
        ->setStatusCode(204);
});

$routes->post('api/auth/login', 'Api\AuthController::login');
$routes->get('api/public/stats', 'Api\DashboardController::publicStats');

$routes->group('api/auth', ['filter' => 'auth'], static function ($routes) {
    $routes->post('logout', 'Api\AuthController::logout');
    $routes->get('me', 'Api\AuthController::me');
});

$routes->get('api/dashboard', 'Api\DashboardController::index', ['filter' => 'auth']);

$routes->group('api/categories', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Api\CategoryController::index');
    $routes->get('(:num)', 'Api\CategoryController::show/$1');
    $routes->post('/', 'Api\CategoryController::create');
    $routes->put('(:num)', 'Api\CategoryController::update/$1');
    $routes->delete('(:num)', 'Api\CategoryController::delete/$1');
});

$routes->group('api/authors', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Api\AuthorController::index');
    $routes->get('(:num)', 'Api\AuthorController::show/$1');
    $routes->post('/', 'Api\AuthorController::create');
    $routes->put('(:num)', 'Api\AuthorController::update/$1');
    $routes->delete('(:num)', 'Api\AuthorController::delete/$1');
});

$routes->group('api/books', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Api\BookController::index');
    $routes->get('(:num)', 'Api\BookController::show/$1');
    $routes->post('/', 'Api\BookController::create');
    $routes->put('(:num)', 'Api\BookController::update/$1');
    $routes->delete('(:num)', 'Api\BookController::delete/$1');
});

$routes->group('api/members', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Api\MemberController::index');
    $routes->get('(:num)', 'Api\MemberController::show/$1');
    $routes->post('/', 'Api\MemberController::create');
    $routes->put('(:num)', 'Api\MemberController::update/$1');
    $routes->delete('(:num)', 'Api\MemberController::delete/$1');
});

$routes->group('api/rentals', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Api\RentalController::index');
    $routes->get('(:num)', 'Api\RentalController::show/$1');
    $routes->post('/', 'Api\RentalController::create');
    $routes->put('(:num)', 'Api\RentalController::update/$1');
    $routes->put('(:num)/status', 'Api\RentalController::updateStatus/$1');
    $routes->delete('(:num)', 'Api\RentalController::delete/$1');
});
