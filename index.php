<?php

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ( empty( $_SESSION['csrf_token'] ) ) {
    $_SESSION['csrf_token'] = bin2hex( random_bytes(32) );
}

$config = require_once __DIR__ . '/config/config.php';
define( 'BASE_PATH', $config['app']['base_path'] );
define( 'AUTH_URL', $config['backend']['url'] . $config['backend']['auth'] );
define( 'LOCATIONS_URL', $config['backend']['url'] . $config['backend']['locations'] );
define( 'MAPS_KEY', $config['maps']['api_key'] );
define( 'BACKEND_KEY', $config['backend']['api_key'] );

require_once __DIR__ . '/app/core/Security.php';
require_once __DIR__ . '/app/core/Router.php';
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/MapController.php';

$router = new Router();
$authController = new AuthController();
$mapController = new MapController();

$router->get( 'logout', fn() => $authController->logout() );

$router->get( 'search', fn() => $mapController->search() );
$router->addMiddleware( 'GET', 'search', [
    [Security::class, 'requireAuth']
] );

$router->get( 'date', fn() => $mapController->date() );
$router->addMiddleware( 'GET', 'date', [
    [Security::class, 'requireAuth']
] );

$router->get( 'near', fn() => $mapController->near() );
$router->addMiddleware( 'GET', 'near', [
    [Security::class, 'requireAuth']
] );

$router->get( ':id', fn() => $mapController->one() );
$router->addMiddleware( 'GET', ':id', [
    [Security::class, 'requireAuth']
] );

$router->get( '', fn() => $mapController->all() );

$router->post( 'create', fn() => $mapController->create() );
$router->addMiddleware( 'POST', 'create', [
    [Security::class, 'requireAuth'],
    [Security::class, 'requireCsrf']
] );

$router->post( 'delete/:id', fn() => $mapController->delete() );
$router->addMiddleware( 'POST', 'delete/:id', [
    [Security::class, 'requireAuth'],
    [Security::class, 'requireCsrf']
] );

$router->post( 'login', fn() => $authController->login() );
$router->addMiddleware( 'POST', 'login', [
    [Security::class, 'requireCsrf']
] );

$uri = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$router->dispatch( $uri, $_SERVER['REQUEST_METHOD'] );

?>