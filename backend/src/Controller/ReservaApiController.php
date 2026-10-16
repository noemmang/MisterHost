<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\JsonResponse;
use App\Core\Request;
use App\Service\ReservaService;

// Endpoints JSON de las reservas: recibe la petición y devuelve la respuesta.
class ReservaApiController
{
    public function __construct( private ReservaService $service, private Request $request, private JsonResponse $response) {
    }

    // Obtiene el listado de reservas aplicando los filtros recibidos por los parámetros de la URL
    // GET /api/reservas?estado=&desde=&hasta=&q=
    public function listar(): void
    {
        $filtros = [
            'estado' => $this->request->query('estado'),
            'desde' => $this->request->query('desde'),
            'hasta' => $this->request->query('hasta'),
            'q' => $this->request->query('q'),
        ];

        $this->response->send($this->service->listar($filtros));
    }

    // Obtiene los datos de una reserva concreta a partir de su ID y devuelve el resultado en formato JSON.
    // GET /api/reservas/{id}
    public function detalle(int $id): void
    {
        $this->response->send($this->service->obtener($id));
    }
}