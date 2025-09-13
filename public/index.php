<?php

require_once __DIR__ . '/../vendor/autoload.php';

$method = $_SERVER['REQUEST_METHOD']; 
$cleanedURI = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

App\Core\Route::dispatch($method, $cleanedURI);

