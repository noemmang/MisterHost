<?php

declare(strict_types=1);

namespace App\Service;

use App\Core\HttpException;
use App\Repository\ReservaRepository;
use DateTime;

// Reglas de negocio de las reservas.
class ReservaService
{
    const ESTADOS = ['pendiente', 'confirmada', 'cancelada'];

    public function __construct(private ReservaRepository $repository)
    {
    }

    // Lista las reservas aplicando los filtros, una vez comprobado que son válidos.
    public function listar(array $filtros): array
    {
        $this->comprobarFiltros($filtros);

        return $this->repository->listar($filtros);
    }

    // Devuelve una reserva con su historial. Lanza 404 si no existe.
    public function obtener(int $id): array
    {
        $reserva = $this->repository->buscar($id);

        if ($reserva === null) {
            throw new HttpException(404, 'no_encontrado');
        }

        return $reserva;
    }

    // Lanza 400 si el estado no es uno de los permitidos o si una fecha no tiene formato AAAA-MM-DD.
    private function comprobarFiltros(array $filtros): void
    {
        if ($filtros['estado'] !== null && !in_array($filtros['estado'], self::ESTADOS, true)) {
            throw new HttpException(400, 'filtro_no_valido');
        }

        foreach (['desde', 'hasta'] as $campo) {
            if ($filtros[$campo] !== null && !$this->esFecha($filtros[$campo])) {
                throw new HttpException(400, 'filtro_no_valido');
            }
        }
    }

    // Comprueba que el texto sea una fecha real con formato AAAA-MM-DD (rechaza por ejemplo 2026-02-31).
    private function esFecha(string $valor): bool
    {
        $fecha = DateTime::createFromFormat('Y-m-d', $valor);

        return $fecha !== false && $fecha->format('Y-m-d') === $valor;
    }
}