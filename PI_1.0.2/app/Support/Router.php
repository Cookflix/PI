<?php

declare(strict_types=1);

namespace App\Support;

use RuntimeException;

final class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $path): mixed
    {
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo 'Página não encontrada.';
            return null;
        }

        return $this->invoke($handler);
    }

    private function invoke(callable|array $handler): mixed
    {
        if (is_callable($handler)) {
            return $handler();
        }

        if (count($handler) !== 2) {
            throw new RuntimeException('Handler inválido.');
        }

        [$class, $method] = $handler;

        return $class->$method();
    }
}
