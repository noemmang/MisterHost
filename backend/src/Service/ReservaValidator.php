<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;

// Valida los datos de entrada de las reservas.
// Devuelve un código de error por cada campo incorrecto (array vacío si todo es correcto).
class ReservaValidator
{
    // Valida los datos para crear una reserva. El estado no se pide: siempre empieza en pendiente.
    public function validarNueva(array $datos): array
    {
        $errores = [];

        // Textos obligatorios con su largo máximo.
        $maximos = ['nombre_huesped' => 120, 'email_huesped' => 160, 'nombre_alojamiento' => 160];
        foreach ($maximos as $campo => $maximo) {
            $valor = $datos[$campo] ?? null;

            if (!is_string($valor) || trim($valor) === '') {
                $errores[$campo] = 'obligatorio';
            } elseif (strlen(trim($valor)) > $maximo) {
                $errores[$campo] = 'demasiado_largo';
            }
        }

        if (!isset($errores['email_huesped']) && !filter_var(trim($datos['email_huesped']), FILTER_VALIDATE_EMAIL)) {
            $errores['email_huesped'] = 'email_no_valido';
        }

        foreach (['fecha_entrada', 'fecha_salida'] as $campo) {
            $valor = $datos[$campo] ?? null;

            if (!is_string($valor) || !$this->esFecha($valor)) {
                $errores[$campo] = 'fecha_no_valida';
            }
        }

        // La estancia mínima es de 1 noche. Con formato AAAA-MM-DD las fechas se pueden comparar como texto.
        if (!isset($errores['fecha_entrada']) && !isset($errores['fecha_salida'])
            && $datos['fecha_salida'] <= $datos['fecha_entrada']) {
            $errores['fecha_salida'] = 'fecha_salida_posterior';
        }

        // El importe debe ser un número (no texto), no negativo y que quepa en DECIMAL(10,2).
        $importe = $datos['importe'] ?? null;
        if ((!is_int($importe) && !is_float($importe)) || $importe < 0 || $importe > 99999999.99) {
            $errores['importe'] = 'importe_no_valido';
        }

        // Las notas son opcionales.
        $notas = $datos['notas'] ?? null;
        if ($notas !== null && (!is_string($notas) || strlen($notas) > 1000)) {
            $errores['notas'] = 'notas_no_validas';
        }

        return $errores;
    }

    // Valida el nuevo estado de una reserva contra la lista de estados permitidos que se le pasa.
    public function validarCambioEstado(array $datos, array $estados): array
    {
        if (!in_array($datos['estado'] ?? null, $estados, true)) {
            return ['estado' => 'estado_no_valido'];
        }

        return [];
    }

    // Comprueba que el texto sea una fecha real con formato AAAA-MM-DD (rechaza por ejemplo 2026-02-31).
    public function esFecha(string $valor): bool
    {
        $fecha = DateTime::createFromFormat('Y-m-d', $valor);

        return $fecha !== false && $fecha->format('Y-m-d') === $valor;
    }
}