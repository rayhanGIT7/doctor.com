<?php

namespace App\Core;

class Router
{
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, array $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    public function post(string $path, array $action): void
    {
        $this->routes['POST'][$path] = $action;
    }

    public function dispatch(string $method): void
    {
        // Every POST form must send a valid CSRF token
        if ($method === 'POST') {
            Csrf::verify();
        }

        $path = current_path();

        foreach ($this->routes[$method] ?? [] as $route => $action) {
            // "/doctors/{id}" becomes the regex "#^/doctors/(\d+)$#"
            $pattern = '#^' . preg_replace('#\{[a-z_]+\}#', '(\d+)', $route) . '$#';

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);
                [$class, $function] = $action;

                $controller = new $class();
                $controller->$function(...array_map('intval', $matches));
                return;
            }
        }

        abort(404);
    }
}
