<?php

declare(strict_types=1);

namespace App\Core;

// Asocia cada método y ruta con la función que debe atenderla.
class Router
{
    private array $routes = [];

    // Registra una ruta. Si lleva {id}, en ese hueco solo se aceptan números.
    public function add(string $method, string $path, callable $handler): void
    {
        $this->routes[] = ['method' => $method, 'path' => $path, 'handler' => $handler];
    }

    // Busca la ruta de la petición y ejecuta su función, pasándole el id si lo hay.
    // Lanza 404 si la ruta no existe y 405 si existe pero con otro método.
    public function dispatch(Request $request): void
    {
        $pathExists = false;

        foreach ($this->routes as $route) {
            $pattern = '#^' . str_replace('{id}', '(\d+)', $route['path']) . '$#';

            if (!preg_match($pattern, $request->path(), $matches)) {
                continue;
            }

            $pathExists = true;

            if ($route['method'] === $request->method()) {
                $id = isset($matches[1]) ? (int) $matches[1] : null;
                $route['handler']($id);
                return;
            }
        }

        if ($pathExists) {
            throw new HttpException(405, 'metodo_no_permitido');
        }

        throw new HttpException(404, 'no_encontrado');
    }
}