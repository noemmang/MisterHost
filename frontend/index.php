<?php

declare(strict_types=1);

use App\Controller\ReservaApiController;
use App\Core\Database;
use App\Core\ErrorHandler;
use App\Core\JsonResponse;
use App\Core\Request;
use App\Core\Router;
use App\Core\Translator;
use App\Repository\ReservaRepository;
use App\Service\ReservaService;
use App\Service\ReservaValidator;

require __DIR__ . '/../backend/vendor/autoload.php';

// Los errores de PHP se guardan en el log, nunca se muestran al cliente.
ini_set('display_errors', '0');

// Cabeceras de seguridad para todas las respuestas.
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

$translator = new Translator('es');
$response = new JsonResponse();
$errorHandler = new ErrorHandler($translator, $response);
$request = new Request();

try {
    // El .env vive en backend/, fuera de la carpeta pública, para que el navegador nunca lo alcance.
    $config = parse_ini_file(__DIR__ . '/../backend/.env', false, INI_SCANNER_RAW);

    $repository = new ReservaRepository((new Database($config))->connect());
    $service = new ReservaService($repository, new ReservaValidator(), $translator);
    $controller = new ReservaApiController($service, $request, $response);

    $router = new Router();
    $router->add('GET', '/api/reservas', [$controller, 'listar']);
    $router->add('GET', '/api/reservas/{id}', [$controller, 'detalle']);
    $router->add('POST', '/api/reservas', [$controller, 'crear']);

    $router->dispatch($request);
} catch (Throwable $e) {
    $errorHandler->handle($e);
}