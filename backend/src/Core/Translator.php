<?php

declare(strict_types=1);

namespace App\Core;

// Devuelve los textos de la app en el idioma indicado, leídos de backend/lang.
class Translator
{
    private array $textos;

    // El idioma lo decide la app, nunca la petición, porque forma parte de la ruta del fichero que se carga.
    public function __construct(string $idioma)
    {
        $this->textos = require __DIR__ . '/../../lang/' . $idioma . '.php';
    }

    // Devuelve el texto de una clave. Si no existe, devuelve la propia clave.
    public function t(string $clave): string
    {
        return $this->textos[$clave] ?? $clave;
    }
}