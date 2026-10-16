<?php

declare(strict_types=1);

use App\Core\Database;

require __DIR__ . '/../backend/vendor/autoload.php';

// El .env vive en backend/, fuera de la carpeta pública, para que el navegador nunca lo alcance.
$config = parse_ini_file(__DIR__ . '/../backend/.env', false, INI_SCANNER_RAW);

try {
    (new Database($config))->connect();
    echo 'MisterHost: conexión con la base de datos correcta';
} catch (PDOException $e) {
    // El detalle va al log del servidor; al cliente solo un mensaje genérico.
    error_log($e->getMessage());
    http_response_code(500);
    echo 'No se pudo conectar con la base de datos';
}