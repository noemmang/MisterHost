<?php

declare(strict_types=1);

use App\Core\ErrorHandler;
use App\Core\JsonResponse;
use App\Core\Request;
use App\Core\Router;
use App\Core\Translator;

require __DIR__ . '/../backend/vendor/autoload.php';

// Los errores de PHP se guardan en el log, nunca se muestran al cliente.
ini_set('display_errors', '0');

// Cabeceras de seguridad para todas las respuestas.
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

$translator = new Translator('es');
$response = new JsonResponse();
$errorHandler = new ErrorHandler($translator, $response);

$router = new Router();

// Ruta provisional: devuelve una lista vacía hasta conectar con la base de datos.
$router->add('GET', '/api/reservas', function () use ($response) {
    $response->send([]);
});

try {
    $router->dispatch(new Request());
} catch (Throwable $e) {
    $errorHandler->handle($e);
}