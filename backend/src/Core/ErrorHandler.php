<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

// Convierte cualquier error en una respuesta JSON.
class ErrorHandler
{
    public function __construct(private Translator $translator, private JsonResponse $response)
    {
    }

    // Los errores esperados (HttpException) se devuelven tal cual, con los errores por campo si los hay.
    // El resto se guarda en el log y el cliente solo ve un mensaje genérico.
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

        $error = [
            'codigo' => $codigo,
            'mensaje' => $this->translator->t('error.' . $codigo),
        ];

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