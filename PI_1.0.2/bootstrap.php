<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\CulinaristaController;
use App\Controllers\HomeController;
use App\Controllers\ReceitaController;
use App\Database\Connection;
use App\Repositories\ReceitaRepository;
use App\Repositories\UsuarioRepository;
use App\Services\ReceitaService;
use App\Services\UsuarioService;

$pdo = Connection::get();

$usuarioRepository = new UsuarioRepository($pdo);
$usuarioService = new UsuarioService($usuarioRepository, $pdo);

$receitaRepository = new ReceitaRepository($pdo);
$receitaService = new ReceitaService($receitaRepository);

$homeController = new HomeController();
$authController = new AuthController($usuarioService);
$culinaristaController = new CulinaristaController($receitaService);
$receitaController = new ReceitaController($receitaService);

return compact(
    'homeController',
    'authController',
    'culinaristaController',
    'receitaController'
);
