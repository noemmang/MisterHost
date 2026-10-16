<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

// Crea la conexión a la base de datos con los datos del .env.
class Database
{
    // Recibe los datos del .env: DB_HOST, DB_PORT, DB_NAME, DB_USER y DB_PASS.
    public function __construct(private array $config)
    {
    }

    // Devuelve la conexión PDO. Usa consultas preparadas reales (no emuladas)
    // para que un valor enviado por el usuario nunca se ejecute como SQL.
    public function connect(): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $this->config['DB_HOST'],
            $this->config['DB_PORT'],
            $this->config['DB_NAME']
        );

        return new PDO($dsn, $this->config['DB_USER'], $this->config['DB_PASS'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}