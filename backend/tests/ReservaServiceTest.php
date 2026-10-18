<?php

declare(strict_types=1);

namespace Tests;

use App\Core\HttpException;
use App\Core\Translator;
use App\Repository\ReservaRepository;
use App\Service\ReservaService;
use App\Service\ReservaValidator;
use PHPUnit\Framework\TestCase;

// Comprueba las reglas de negocio del servicio. El repositorio es falso, así que no se toca la base de datos.
class ReservaServiceTest extends TestCase
{
    // Crea el servicio con el repositorio que se le pasa. El validador y el traductor son los reales.
    private function crearServicio(ReservaRepository $repositorio): ReservaService
    {
        return new ReservaService($repositorio, new ReservaValidator(), new Translator('es'));
    }

    // Repositorio falso donde la reserva 5 existe con el estado indicado.
    private function repositorioConReserva(string $estado): ReservaRepository
    {
        $repositorio = $this->createMock(ReservaRepository::class);
        $repositorio->method('buscar')->willReturn(['id' => 5, 'estado' => $estado]);

        return $repositorio;
    }

    // Una reserva pendiente puede pasar a confirmada y se guarda el cambio.
    public function testUnaReservaPendientePuedeConfirmarse(): void
    {
        $repositorio = $this->repositorioConReserva('pendiente');
        $repositorio->expects($this->once())->method('cambiarEstado')->with(5, 'confirmada', $this->anything());

        $this->crearServicio($repositorio)->cambiarEstado(5, ['estado' => 'confirmada']);
    }

    // Una reserva confirmada puede pasar a cancelada y se guarda el cambio.
    public function testUnaReservaConfirmadaPuedeCancelarse(): void
    {
        $repositorio = $this->repositorioConReserva('confirmada');
        $repositorio->expects($this->once())->method('cambiarEstado')->with(5, 'cancelada', $this->anything());

        $this->crearServicio($repositorio)->cambiarEstado(5, ['estado' => 'cancelada']);
    }

    // Una reserva cancelada ya no cambia: da error 409 y no se guarda nada.
    public function testUnaReservaCanceladaNoPuedeCambiar(): void
    {
        $repositorio = $this->repositorioConReserva('cancelada');
        $repositorio->expects($this->never())->method('cambiarEstado');

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('transicion_no_valida');

        $this->crearServicio($repositorio)->cambiarEstado(5, ['estado' => 'confirmada']);
    }

    // Cambiar el estado de una reserva que no existe da error 404.
    public function testCambiarElEstadoDeUnaReservaQueNoExisteDa404(): void
    {
        $repositorio = $this->createStub(ReservaRepository::class);
        $repositorio->method('buscar')->willReturn(null);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('no_encontrado');

        $this->crearServicio($repositorio)->cambiarEstado(99, ['estado' => 'confirmada']);
    }

    // Filtrar por un estado que no existe da error 400.
    public function testRechazaUnFiltroDeEstadoNoValido(): void
    {
        $repositorio = $this->createStub(ReservaRepository::class);
        $filtros = ['estado' => 'enviada', 'desde' => null, 'hasta' => null, 'q' => null, 'alojamiento' => null];

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('filtro_no_valido');

        $this->crearServicio($repositorio)->listar($filtros);
    }

    // El cliente no decide el estado inicial: aunque envíe otro, la reserva se guarda como pendiente.
    public function testUnaReservaNuevaSeGuardaSiempreComoPendiente(): void
    {
        $repositorio = $this->repositorioConReserva('pendiente');
        $repositorio->expects($this->once())
            ->method('crear')
            ->with($this->callback(fn (array $reserva) => $reserva['estado'] === 'pendiente'), $this->anything())
            ->willReturn(5);

        $datos = [
            'nombre_huesped' => 'Ana García',
            'email_huesped' => 'ana@example.com',
            'nombre_alojamiento' => 'Hotel Sol',
            'fecha_entrada' => '2030-05-10',
            'fecha_salida' => '2030-05-12',
            'importe' => 150.5,
            'estado' => 'confirmada',
        ];

        $this->crearServicio($repositorio)->crear($datos);
    }
}