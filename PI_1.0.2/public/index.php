<?php

declare(strict_types=1);

use App\Support\Session;

require dirname(__DIR__) . '/vendor/autoload.php';

Session::start();

$router = require dirname(__DIR__) . '/routes/web.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rtrim($path, '/') ?: '/';

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

$router->dispatch($method, $path);
