<?php

declare(strict_types=1);

namespace Tests;

use App\Service\ReservaValidator;
use PHPUnit\Framework\TestCase;

// Comprueba las reglas de validación de los datos de una reserva.
class ReservaValidatorTest extends TestCase
{
    private ReservaValidator $validator;

    // Crea un validador nuevo antes de cada test.
    protected function setUp(): void
    {
        $this->validator = new ReservaValidator();
    }

    // Datos correctos de una reserva. Cada test cambia solo el campo que quiere probar.
    private function datosValidos(array $cambios = []): array
    {
        return array_merge([
            'nombre_huesped' => 'Ana García',
            'email_huesped' => 'ana@example.com',
            'nombre_alojamiento' => 'Hotel Sol',
            'fecha_entrada' => '2030-05-10',
            'fecha_salida' => '2030-05-12',
            'importe' => 150.5,
        ], $cambios);
    }

    // Una reserva con todos los datos correctos no da ningún error.
    public function testAceptaUnaReservaCorrecta(): void
    {
        $this->assertSame([], $this->validator->validarNueva($this->datosValidos()));
    }

    // Si no llega ningún dato, cada campo obligatorio da su error.
    public function testExigeLosCamposObligatorios(): void
    {
        $errores = $this->validator->validarNueva([]);

        $this->assertSame('obligatorio', $errores['nombre_huesped']);
        $this->assertSame('obligatorio', $errores['email_huesped']);
        $this->assertSame('obligatorio', $errores['nombre_alojamiento']);
        $this->assertSame('fecha_no_valida', $errores['fecha_entrada']);
        $this->assertSame('fecha_no_valida', $errores['fecha_salida']);
        $this->assertSame('importe_no_valido', $errores['importe']);
    }

    // Un email sin dominio no es válido.
    public function testRechazaUnEmailIncorrecto(): void
    {
        $errores = $this->validator->validarNueva($this->datosValidos(['email_huesped' => 'ana@']));

        $this->assertSame(['email_huesped' => 'email_no_valido'], $errores);
    }

    // La salida tiene que ser posterior a la entrada: ni el mismo día ni antes.
    public function testLaSalidaDebeSerPosteriorALaEntrada(): void
    {
        $mismoDia = $this->validator->validarNueva($this->datosValidos(['fecha_salida' => '2030-05-10']));
        $antes = $this->validator->validarNueva($this->datosValidos(['fecha_salida' => '2030-05-09']));

        $this->assertSame(['fecha_salida' => 'fecha_salida_posterior'], $mismoDia);
        $this->assertSame(['fecha_salida' => 'fecha_salida_posterior'], $antes);
    }

    // Solo se acepta un estado de la lista permitida.
    public function testRechazaUnEstadoQueNoExiste(): void
    {
        $estados = ['pendiente', 'confirmada', 'cancelada'];

        $this->assertSame([], $this->validator->validarCambioEstado(['estado' => 'confirmada'], $estados));
        $this->assertSame(['estado' => 'estado_no_valido'], $this->validator->validarCambioEstado(['estado' => 'enviada'], $estados));
    }
}