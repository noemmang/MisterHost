<?php

declare(strict_types=1);

namespace Tests;

use App\Core\Database;
use App\Repository\ReservaRepository;
use PDO;
use PHPUnit\Framework\TestCase;

// Comprueba los filtros del listado contra la base de datos real (la del .env, que debe estar levantada).
// Las reservas de prueba se insertan dentro de una transacción que se deshace al terminar,
// así que los datos de la base de datos no cambian.
class ReservaRepositoryTest extends TestCase
{
    private static PDO $db;
    private ReservaRepository $repositorio;

    // Conecta con la base de datos e inserta tres reservas de prueba dentro de una transacción.
    // Llevan nombres y fechas (año 2030) que no existen en los datos de ejemplo.
    public static function setUpBeforeClass(): void
    {
        $config = parse_ini_file(__DIR__ . '/../.env', false, INI_SCANNER_RAW);
        self::$db = (new Database($config))->connect();
        self::$db->beginTransaction();

        self::insertar('ZZTEST Ana', 'ZZTEST Hotel Sol', '2030-01-10', '2030-01-12', 'pendiente');
        self::insertar('ZZTEST Luis', 'ZZTEST Hotel Sol', '2030-02-10', '2030-02-12', 'confirmada');
        self::insertar('ZZTEST Marta', 'ZZTEST Casa Luna', '2030-03-10', '2030-03-12', 'cancelada');
    }

    // Deshace la transacción para que las reservas de prueba no se queden guardadas.
    public static function tearDownAfterClass(): void
    {
        self::$db->rollBack();
    }

    // Crea el repositorio antes de cada test.
    protected function setUp(): void
    {
        $this->repositorio = new ReservaRepository(self::$db);
    }

    // Inserta una reserva de prueba con los datos indicados.
    private static function insertar(string $nombre, string $alojamiento, string $entrada, string $salida, string $estado): void
    {
        $consulta = self::$db->prepare(
            'INSERT INTO reserva (nombre_huesped, email_huesped, nombre_alojamiento, fecha_entrada, fecha_salida, estado, importe)
            VALUES (:nombre, \'test@example.com\', :alojamiento, :entrada, :salida, :estado, 100)'
        );
        $consulta->execute([
            'nombre' => $nombre,
            'alojamiento' => $alojamiento,
            'entrada' => $entrada,
            'salida' => $salida,
            'estado' => $estado,
        ]);
    }

    // Lista con los filtros indicados y devuelve solo los nombres de los huéspedes, para comparar fácil.
    // Por defecto busca "ZZTEST", así solo salen las reservas de prueba.
    private function nombresListados(array $filtros): array
    {
        $base = ['estado' => null, 'desde' => null, 'hasta' => null, 'q' => 'ZZTEST', 'alojamiento' => null];

        return array_column($this->repositorio->listar(array_merge($base, $filtros)), 'nombre_huesped');
    }

    // Solo salen las reservas del estado indicado.
    public function testFiltraPorEstado(): void
    {
        $this->assertSame(['ZZTEST Luis'], $this->nombresListados(['estado' => 'confirmada']));
    }

    // El rango de fechas se aplica sobre la fecha de entrada.
    public function testFiltraPorRangoDeFechas(): void
    {
        $this->assertSame(['ZZTEST Luis'], $this->nombresListados(['desde' => '2030-02-01', 'hasta' => '2030-02-28']));
        $this->assertSame([], $this->nombresListados(['desde' => '2030-04-01']));
    }

    // La búsqueda por nombre devuelve solo los huéspedes cuyo nombre contiene el texto.
    public function testFiltraPorNombreDelHuesped(): void
    {
        $this->assertSame(['ZZTEST Ana'], $this->nombresListados(['q' => 'ZZTEST Ana']));
    }

    // La búsqueda por alojamiento devuelve las reservas de ese alojamiento.
    public function testFiltraPorAlojamiento(): void
    {
        $this->assertCount(2, $this->nombresListados(['alojamiento' => 'Hotel Sol']));
        $this->assertSame(['ZZTEST Marta'], $this->nombresListados(['alojamiento' => 'Casa Luna']));
    }

    // Los filtros se pueden combinar: solo sale lo que cumple todos a la vez.
    public function testCombinaVariosFiltros(): void
    {
        // En Hotel Sol hay dos reservas (Ana y Luis), pero solo una está confirmada.
        $this->assertSame(['ZZTEST Luis'], $this->nombresListados(['alojamiento' => 'Hotel Sol', 'estado' => 'confirmada']));
        $this->assertSame([], $this->nombresListados(['alojamiento' => 'Casa Luna', 'estado' => 'pendiente']));
    }
}