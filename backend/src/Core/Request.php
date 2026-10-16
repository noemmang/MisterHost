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

    // Valor de un parámetro de la URL (?estado=pendiente).
    // Devuelve null si no viene, está vacío o no es texto.
    public function query(string $nombre): ?string
    {
        $valor = $_GET[$nombre] ?? null;

        if (!is_string($valor) || trim($valor) === '') {
            return null;
        }

        return trim($valor);
    }
}