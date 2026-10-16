<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

// Único sitio con SQL de las reservas y su historial.
class ReservaRepository
{
    public function __construct(private PDO $db)
    {
    }

    // Devuelve las reservas que cumplan los filtros (estado, desde, hasta y q).
    // Los filtros que vengan a null se ignoran. Los valores siempre van como parámetros, nunca dentro del texto del SQL.
    public function listar(array $filtros): array
    {
        $sql = 'SELECT * FROM reserva WHERE 1 = 1';
        $params = [];

        if ($filtros['estado'] !== null) {
            $sql .= ' AND estado = :estado';
            $params['estado'] = $filtros['estado'];
        }

        // El rango de fechas se aplica sobre la fecha de entrada.
        if ($filtros['desde'] !== null) {
            $sql .= ' AND fecha_entrada >= :desde';
            $params['desde'] = $filtros['desde'];
        }

        if ($filtros['hasta'] !== null) {
            $sql .= ' AND fecha_entrada <= :hasta';
            $params['hasta'] = $filtros['hasta'];
        }

        if ($filtros['q'] !== null) {
            // Se escapan % y _ para que se busquen como texto normal y no como comodines.
            $texto = str_replace(['%', '_'], ['\%', '\_'], $filtros['q']);
            $sql .= ' AND nombre_huesped LIKE :q';
            $params['q'] = '%' . $texto . '%';
        }

        $sql .= ' ORDER BY fecha_entrada DESC, id DESC';

        $consulta = $this->db->prepare($sql);
        $consulta->execute($params);
        $reservas = $consulta->fetchAll();

        // El importe llega como texto desde la base de datos y se pasa a número para el JSON.
        foreach ($reservas as $i => $reserva) {
            $reservas[$i]['importe'] = (float) $reserva['importe'];
        }

        return $reservas;
    }

    // Devuelve una reserva con su historial de eventos o null si no existe.
    public function buscar(int $id): ?array
    {
        $consulta = $this->db->prepare('SELECT * FROM reserva WHERE id = :id');
        $consulta->execute(['id' => $id]);
        $reserva = $consulta->fetch();

        if ($reserva === false) {
            return null;
        }

        $reserva['importe'] = (float) $reserva['importe'];

        $consulta = $this->db->prepare(
            'SELECT * FROM reserva_evento WHERE reserva_id = :id ORDER BY fecha_creacion, id'
        );
        $consulta->execute(['id' => $id]);
        $reserva['eventos'] = $consulta->fetchAll();

        return $reserva;
    }
}