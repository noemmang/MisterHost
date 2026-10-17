<?php

declare(strict_types=1);

namespace App\Service;

use App\Core\HttpException;
use App\Core\Translator;
use App\Repository\ReservaRepository;

// Reglas de negocio de las reservas.
class ReservaService
{
    const ESTADOS = ['pendiente', 'confirmada', 'cancelada'];

    public function __construct(
        private ReservaRepository $repository,
        private ReservaValidator $validator,
        private Translator $translator
    ) {
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

    // Crea una reserva en estado pendiente con su evento inicial y la devuelve con su historial.
    // Lanza 422 con los errores por campo si los datos no son válidos.
    public function crear(array $datos): array
    {
        $errores = $this->validator->validarNueva($datos);

        if ($errores !== []) {
            throw new HttpException(422, 'validacion', $errores);
        }

        // Solo se guardan los campos conocidos. Las notas vacías se guardan como null.
        $notas = trim($datos['notas'] ?? '');
        $reserva = [
            'nombre_huesped' => trim($datos['nombre_huesped']),
            'email_huesped' => trim($datos['email_huesped']),
            'nombre_alojamiento' => trim($datos['nombre_alojamiento']),
            'fecha_entrada' => $datos['fecha_entrada'],
            'fecha_salida' => $datos['fecha_salida'],
            'estado' => 'pendiente',
            'importe' => $datos['importe'],
            'notas' => $notas === '' ? null : $notas,
        ];
        $evento = ['tipo' => 'creada', 'descripcion' => $this->translator->t('evento.creada')];

        $id = $this->repository->crear($reserva, $evento);

        return $this->obtener($id);
    }

    // Lanza 400 si el estado no es uno de los permitidos o si una fecha no tiene formato AAAA-MM-DD.
    private function comprobarFiltros(array $filtros): void
    {
        if ($filtros['estado'] !== null && !in_array($filtros['estado'], self::ESTADOS, true)) {
            throw new HttpException(400, 'filtro_no_valido');
        }

        foreach (['desde', 'hasta'] as $campo) {
            if ($filtros[$campo] !== null && !$this->validator->esFecha($filtros[$campo])) {
                throw new HttpException(400, 'filtro_no_valido');
            }
        }
    }
}