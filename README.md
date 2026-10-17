# MisterHost

Gestión de reservas de alojamiento en PHP nativo, Twig y JavaScript vanilla.
Prueba técnica para MisterPlan.

## Estado
En construcción.

## Cómo levantarlo en local

Requisitos: PHP 8.2+ con la extensión `pdo_mysql`, Composer y MySQL 8.0.16+ o MariaDB 10.2+.

1. Crear la base de datos con datos de ejemplo (recrea las tablas desde cero cada vez que se importa):

```bash
mysql -u root -p -e "source database/reservas.sql"
```

2. Configurar la conexión. Copiar la plantilla y ajustar los datos si no coinciden con los de la base de datos local:

```bash
cp backend/.env.example backend/.env
```

3. Instalar las dependencias:

```bash
composer install -d backend
```

4. Arrancar:

```bash
php -S localhost:8000 -t frontend
```

Después abrir http://localhost:8000/api/reservas

## API

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/reservas` | Listado. Filtros combinables: `estado`, `desde`, `hasta` (sobre la fecha de entrada, formato AAAA-MM-DD) y `q` (nombre del huésped) |
| GET | `/api/reservas/{id}` | Detalle con su historial de eventos |
| POST | `/api/reservas` | Crea una reserva en estado `pendiente` (201). Cuerpo JSON: `nombre_huesped`, `email_huesped`, `nombre_alojamiento`, `fecha_entrada`, `fecha_salida`, `importe` y `notas` (opcional) |
| PATCH | `/api/reservas/{id}/estado` | Confirma o cancela una reserva. Cuerpo JSON: `{"estado": "confirmada"}` o `{"estado": "cancelada"}` |

Errores: 400 (JSON mal formado o filtro no válido), 404, 405, 409 (cambio de estado no permitido) y 500. Si los datos no son válidos, 422 con el error de cada campo en `campos`.

## Decisiones

- **Base de datos:** MySQL/MariaDB, porque la app es transaccional y ya había trabajado con ella en otros proyectos. El SQL es portable entre ambos.
- **Entorno local:** XAMPP (PHP, Composer y MariaDB), por experiencia de uso en proyectos anteriores.
- **Estructura:** `backend/` contiene todo el código PHP y no es accesible desde el navegador. `frontend/` es la única carpeta pública.
- **Capas:** el controlador recibe la petición, el servicio aplica las reglas y el repositorio es el único que escribe SQL.
- **Seguridad:** consultas preparadas, filtros validados contra una lista de valores permitidos, y los errores internos solo se guardan en el log.
- **Validación:** el servidor valida todos los datos de entrada y no se fía del cliente. Solo se leen los campos conocidos y el estado inicial nunca lo decide el cliente.
- **Historial:** crear una reserva o cambiar su estado y guardar el evento se hace en una sola transacción, así no queda historial huérfano.
- **Estados:** una reserva pendiente puede pasar a confirmada o cancelada, y una confirmada a cancelada. Una cancelada no cambia más. Las transiciones son una constante de `ReservaService`.
- **Textos:** todos salen de `backend/lang/es.php`, listos para añadir otro idioma.
- **Configuración:** los datos de conexión viven en `backend/.env`, fuera de la carpeta pública y fuera de Git.

## Qué haría diferente con más tiempo

- **Concurrencia:** dos peticiones simultáneas sobre la misma reserva podrían saltarse la comprobación de la transición. Se arreglaría bloqueando la fila (`SELECT ... FOR UPDATE`) dentro de la transacción.

## Escalabilidad
_Pendiente._