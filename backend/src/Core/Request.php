<?php

declare(strict_types=1);

namespace App\Core;

// Datos de la petición HTTP que llega a la app.
class Request
{
    // Método HTTP: GET, POST, PATCH...
    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    // Ruta pedida sin los parámetros de la URL.
    public function path(): string
    {
        return (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
}