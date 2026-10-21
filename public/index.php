<?php

define('LARAVEL_START', microtime(true));

$candidatePaths = [
    dirname(__DIR__), // Standar Laravel lokal (public di dalam root project)
    dirname(__DIR__) . '/siswamart', // Hosting DirectAdmin/cPanel
    dirname(__DIR__) . '/siswamartid',
    dirname(dirname(__DIR__)) . '/siswamart',
    dirname(dirname(__DIR__)) . '/siswamartid',
    dirname(__DIR__) . '/core',
    dirname(__DIR__) . '/laravel',
];

$applicationPath = dirname(__DIR__);
foreach ($candidatePaths as $candidate) {
    if (is_file($candidate . '/vendor/autoload.php') && file_exists($candidate . '/bootstrap/app.php')) {
        $applicationPath = $candidate;
        break;
    }
}

if (file_exists($maintenance = $applicationPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $applicationPath.'/vendor/autoload.php';

$app = require_once $applicationPath.'/bootstrap/app.php';

$app->usePublicPath(__DIR__);

$app->handleRequest(Illuminate\Http\Request::capture());