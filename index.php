<?php

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

$config = require_once __DIR__ . '/config/config.php';

define('BASE_PATH', $config['app']['base_path']);

require_once __DIR__ . '/app/controllers/MapController.php';

$controller = new MapController($config);
$controller->index();

?>