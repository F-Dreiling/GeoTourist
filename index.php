<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$config = require_once __DIR__ . '/config/config.php';

define( 'BASE_PATH', $config['app']['base_path'] );

require_once __DIR__ . '/app/controllers/MapController.php';

$uri = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$uri = trim( $uri, '/' );

$controller = new MapController( $config );

switch ( $uri ) {
    case '':
        $controller->all();
        break;

    case 'search':
        $controller->search();
        break;

    case 'near':
        $controller->near();
        break;

    case 'create':
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $controller->create();
        } else {
            http_response_code( 405 );
            echo "Method not allowed";
        }
        break;

    default:
        if ( preg_match( '/^[a-f0-9]{24}$/i', $uri ) ) {
            $_GET['id'] = $uri;
            $controller->one();
        } else {
            http_response_code( 404 );
            echo "Not found";
        }
}

?>