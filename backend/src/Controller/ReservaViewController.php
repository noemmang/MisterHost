<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Request;
use App\Service\ReservaService;
use Twig\Environment;

// Páginas HTML de las reservas: pide los datos al servicio y los pinta con Twig.
class ReservaViewController
{
    public function __construct(
        private ReservaService $service,
        private Request $request,
        private Environment $twig
    ) {
    }

    // Pinta el listado de reservas con los mismos filtros que la API, recibidos por la URL.
    // GET /?estado=&desde=&hasta=&q=
    public function listar(): void
    {
        $filtros = [
            'estado' => $this->request->query('estado'),
            'desde' => $this->request->query('desde'),
            'hasta' => $this->request->query('hasta'),
            'q' => $this->request->query('q'),
        ];

        echo $this->twig->render('listado.html.twig', [
            'reservas' => $this->service->listar($filtros),
            'filtros' => $filtros,
            'estados' => ReservaService::ESTADOS,
        ]);
    }

    // Pinta los datos de una reserva y su historial de eventos.
    // GET /reservas/{id}
    public function detalle(int $id): void
    {
        echo $this->twig->render('detalle.html.twig', [
            'reserva' => $this->service->obtener($id),
        ]);
    }
}