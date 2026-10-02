<?php

define('LARAVEL_START', microtime(true));

$applicationPath = dirname(__DIR__);

if (! is_file($applicationPath.'/vendor/autoload.php')) {
    $applicationPath .= '/siswamartid';
}

if (file_exists($maintenance = $applicationPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $applicationPath.'/vendor/autoload.php';

$app = require_once $applicationPath.'/bootstrap/app.php';

$app->usePublicPath(__DIR__);

$app->handleRequest(Illuminate\Http\Request::capture());