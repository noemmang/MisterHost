<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

// Convierte cualquier error en una respuesta JSON.
class ErrorHandler
{
    public function __construct( private Translator $translator, private JsonResponse $response) {
    }

    // Los errores esperados (HttpException) se devuelven tal cual.
    // El resto se guarda en el log y el cliente solo ve un mensaje genérico.
    public function handle(Throwable $e): void
    {
        if ($e instanceof HttpException) {
            $status = $e->getStatus();
            $codigo = $e->getCodigo();
        } else {
            error_log((string) $e);
            $status = 500;
            $codigo = 'interno';
        }

        $this->response->send([
            'error' => [
                'codigo' => $codigo,
                'mensaje' => $this->translator->t('error.' . $codigo),
            ],
        ], $status);
    }
}