<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$config = require_once __DIR__ . '/config/config.php';
define( 'BASE_PATH', $config['app']['base_path'] );

require_once __DIR__ . '/app/core/Router.php';
require_once __DIR__ . '/app/controllers/MapController.php';
require_once __DIR__ . '/app/controllers/AuthController.php';

$router = new Router();
$controller = new MapController( $config );
$authController = new AuthController( $config );

$router->post( 'login', fn() => $authController->login() );
$router->get( 'logout', fn() => $authController->logout() );

$router->get( 'search', fn() => $controller->search() );
$router->get( 'date', fn() => $controller->date() );
$router->get( 'near', fn() => $controller->near() );
$router->get( ':id', fn() => $controller->one() );
$router->get( '', fn() => $controller->all() );

$router->post( 'create', fn() => $controller->create() );
$router->post( 'delete/:id', fn() => $controller->delete() );

$uri = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$router->dispatch( $uri, $_SERVER['REQUEST_METHOD'] );

?>