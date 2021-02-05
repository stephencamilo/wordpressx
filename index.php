<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/vendor/autoload.php';

$WPX_routes = new Core\Routes\Routes;

$WPX_routes->init_router();