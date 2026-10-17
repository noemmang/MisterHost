<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;
use Twig\Environment;

// Convierte cualquier error en una respuesta: JSON en la API y una página de error en el resto.
class ErrorHandler
{
    public function __construct(
        private Translator $translator,
        private JsonResponse $response,
        private Request $request,
        private Environment $twig
    ) {
    }

    // Los errores esperados (HttpException) se devuelven tal cual, con los errores por campo si los hay.
    // El resto se guarda en el log y el cliente solo ve un mensaje genérico.
    // Las rutas que empiezan por /api responden JSON y las demás muestran una página de error.
    public function handle(Throwable $e): void
    {
        $campos = [];

        if ($e instanceof HttpException) {
            $status = $e->getStatus();
            $codigo = $e->getCodigo();
            $campos = $e->getCampos();
        } else {
            error_log((string) $e);
            $status = 500;
            $codigo = 'interno';
        }

        $mensaje = $this->translator->t('error.' . $codigo);

        if (!str_starts_with($this->request->path(), '/api/')) {
            http_response_code($status);
            echo $this->twig->render('error.html.twig', ['status' => $status, 'mensaje' => $mensaje]);
            return;
        }

        $error = ['codigo' => $codigo, 'mensaje' => $mensaje];

        // Cada campo trae un código de error que se traduce a su texto.
        if ($campos !== []) {
            $error['campos'] = [];
            foreach ($campos as $campo => $codigoCampo) {
                $error['campos'][$campo] = $this->translator->t('validacion.' . $codigoCampo);
            }
        }

        $this->response->send(['error' => $error], $status);
    }
}