<?php

declare(strict_types=1);

use App\Controller\ReservaApiController;
use App\Controller\ReservaViewController;
use App\Core\Database;
use App\Core\ErrorHandler;
use App\Core\JsonResponse;
use App\Core\Request;
use App\Core\Router;
use App\Core\Translator;
use App\Repository\ReservaRepository;
use App\Service\ReservaService;
use App\Service\ReservaValidator;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

require __DIR__ . '/../backend/vendor/autoload.php';

// Los errores de PHP se guardan en el log, nunca se muestran al cliente.
ini_set('display_errors', '0');

// Cabeceras de seguridad para todas las respuestas.
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header("Content-Security-Policy: default-src 'self'");

$idioma = 'es';
$translator = new Translator($idioma);
$request = new Request();
$response = new JsonResponse();

// Twig con el autoescape de HTML activado y la función t() para los textos.
$twig = new Environment(new FilesystemLoader(__DIR__ . '/../backend/templates'), ['autoescape' => 'html']);
$twig->addFunction(new TwigFunction('t', [$translator, 't']));
$twig->addGlobal('idioma', $idioma);

$errorHandler = new ErrorHandler($translator, $response, $request, $twig);

try {
    // El .env vive en backend/, fuera de la carpeta pública, para que el navegador nunca lo alcance.
    $config = parse_ini_file(__DIR__ . '/../backend/.env', false, INI_SCANNER_RAW);

    $repository = new ReservaRepository((new Database($config))->connect());
    $service = new ReservaService($repository, new ReservaValidator(), $translator);
    $apiController = new ReservaApiController($service, $request, $response);
    $viewController = new ReservaViewController($service, $request, $twig);

    $router = new Router();
    $router->add('GET', '/', [$viewController, 'listar']);
    $router->add('GET', '/reservas/nueva', [$viewController, 'formulario']);
    $router->add('GET', '/reservas/{id}', [$viewController, 'detalle']);
    $router->add('GET', '/api/reservas', [$apiController, 'listar']);
    $router->add('GET', '/api/reservas/{id}', [$apiController, 'detalle']);
    $router->add('POST', '/api/reservas', [$apiController, 'crear']);
    $router->add('PATCH', '/api/reservas/{id}/estado', [$apiController, 'cambiarEstado']);

    $router->dispatch($request);
} catch (Throwable $e) {
    $errorHandler->handle($e);
}