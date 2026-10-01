<?php

declare(strict_types=1);

use App\Support\Router;

$controllers = require __DIR__ . '/../bootstrap.php';

$router = new Router();

$router->get('/', [$controllers['homeController'], 'index']);

$router->get('/login', [$controllers['authController'], 'showLogin']);
$router->post('/login', [$controllers['authController'], 'login']);

$router->get('/cadastro', [$controllers['authController'], 'showCadastro']);
$router->post('/cadastro', [$controllers['authController'], 'cadastrar']);

$router->get('/logout', [$controllers['authController'], 'logout']);

$router->get('/culinarista/setup', [$controllers['culinaristaController'], 'setup']);
$router->get('/culinarista', [$controllers['culinaristaController'], 'dashboard']);

$router->get('/receitas/nova', [$controllers['receitaController'], 'create']);
$router->post('/receitas/nova', [$controllers['receitaController'], 'store']);

return $router;
