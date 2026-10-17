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

3. Instalar las dependencias (Twig):

```bash
composer install -d backend
```

4. Arrancar:

```bash
php -S localhost:8000 -t frontend
```

Después abrir http://localhost:8000

## Páginas

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/` | Listado de reservas con filtros (`estado`, `desde`, `hasta`, `q`). Los filtros viajan por la URL, así que funcionan sin JavaScript |
| GET | `/reservas/{id}` | Detalle de una reserva con su historial de eventos |

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
- **Capas:** el controlador recibe la petición, el servicio aplica las reglas y el repositorio es el único que escribe SQL. Hay un controlador para la API y otro para las páginas, y los dos usan el mismo servicio.
- **Seguridad:** consultas preparadas, filtros validados contra una lista de valores permitidos, y los errores internos solo se guardan en el log.
- **Salida HTML:** Twig con el autoescape activado y sin ningún `|raw`, así que cualquier dato guardado se pinta como texto.
- **Cabeceras:** `Content-Security-Policy: default-src 'self'`, `X-Content-Type-Options` y `X-Frame-Options`. No hay código ni estilos inline.
- **Errores:** un único `ErrorHandler`: JSON en `/api` y una página de error en el resto.
- **Validación:** el servidor valida todos los datos de entrada y no se fía del cliente. Solo se leen los campos conocidos y el estado inicial nunca lo decide el cliente.
- **Historial:** crear una reserva o cambiar su estado y guardar el evento se hace en una sola transacción, así no queda historial huérfano.
- **Estados:** una reserva pendiente puede pasar a confirmada o cancelada, y una confirmada a cancelada. Una cancelada no cambia más. Las transiciones son una constante de `ReservaService`.
- **Textos:** todos salen de `backend/lang/es.php`, listos para añadir otro idioma.
- **Configuración:** los datos de conexión viven en `backend/.env`, fuera de la carpeta pública y fuera de Git.

## Qué haría diferente con más tiempo

- **CRUD completo:** la app crea, consulta y cancela reservas, pero no permite editar sus datos ni borrarlas. Se añadirían `PUT /api/reservas/{id}` (con la misma validación que al crear y un evento en el historial) y `DELETE /api/reservas/{id}`, con su formulario de edición.
- **Orden del listado:** hoy el orden es fijo (por fecha de entrada, la más reciente primero). Se añadiría un parámetro de orden con una lista blanca de columnas (huésped, alojamiento, fechas, importe y estado) y su dirección, que se activaría pulsando la cabecera de cada columna.
- **Filtros plegables en móvil:** meter la zona de filtros en un menú desplegable para que en pantallas pequeñas ocupe menos alto y la tabla gane espacio.
- **Docker:** un entorno reproducible (PHP, MariaDB y el servidor web) que se levante con un solo comando, sin depender de XAMPP.
- **Migraciones versionadas:** en lugar de un único `.sql` que recrea todo, un fichero por cambio de esquema para poder evolucionar la base de datos sin perder datos.
- **Alojamientos y disponibilidad:** una entidad `alojamiento` (con sus entidades `ciudad` y `pais`) a la que apunte la reserva, en lugar de un nombre en texto. Con ella se comprobaría la disponibilidad de fechas dentro de una transacción con bloqueo de filas, para que dos reservas simultáneas no se solapen.
- **Concurrencia:** dos peticiones simultáneas sobre la misma reserva podrían saltarse la comprobación de la transición. Se arreglaría bloqueando la fila (`SELECT ... FOR UPDATE`) dentro de la transacción.
- **Notificaciones:** un servicio que envíe emails al huésped al crear, confirmar o cancelar una reserva.
- **Usuarios y roles:** autenticación con dos roles. El usuario normal solo vería y gestionaría sus propias reservas, y el administrador podría gestionarlas todas.
- **Dominio propio:** servir la app desde un dominio propio con HTTPS, en lugar de la URL del hosting gratuito.
- **Formato por idioma:** el formato de fechas e importes (`d/m/Y`, `1.234,50 €`) está fijo en las plantillas. Con más idiomas habría que formatearlos según el idioma activo.
- **Dominio propio:** Si bien se sale de la regla de solo usar tegnologia nativa, usaria un framework y una libreria especializada en tablas como DevExtreme.

## Escalabilidad

- **Nube con escalado automático:** desplegar en un proveedor como AWS o Azure, donde el servicio escala solo según la carga (más instancias cuando suben las reservas) y la base de datos gestionada hace copias de seguridad automáticas.
- **Procesamiento asíncrono:** las tareas secundarias, como enviar los emails, saldrían de la petición y pasarían a una cola atendida por un proceso aparte. Así crear una reserva no espera a esas tareas y responde igual de rápido aunque aumente el volumen.