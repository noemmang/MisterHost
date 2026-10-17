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

    // Estados a los que se puede pasar desde cada estado. Una reserva cancelada no cambia más.
    const TRANSICIONES = [
        'pendiente' => ['confirmada', 'cancelada'],
        'confirmada' => ['cancelada'],
        'cancelada' => [],
    ];

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

    // Cambia el estado de una reserva y la devuelve actualizada.
    // Lanza 422 si el estado no es válido, 404 si la reserva no existe y 409 si la transición no está permitida.
    public function cambiarEstado(int $id, array $datos): array
    {
        $errores = $this->validator->validarCambioEstado($datos, self::ESTADOS);

        if ($errores !== []) {
            throw new HttpException(422, 'validacion', $errores);
        }

        $reserva = $this->obtener($id);
        $estado = $datos['estado'];

        if (!in_array($estado, self::TRANSICIONES[$reserva['estado']], true)) {
            throw new HttpException(409, 'transicion_no_valida');
        }

        $evento = ['tipo' => $estado, 'descripcion' => $this->translator->t('evento.' . $estado)];
        $this->repository->cambiarEstado($id, $estado, $evento);

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