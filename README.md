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

## Decisiones

- **Base de datos:** MySQL/MariaDB, porque la app es transaccional y ya había trabajado con ella en otros proyectos. El SQL es portable entre ambos.
- **Entorno local:** XAMPP (PHP, Composer y MariaDB), por experiencia de uso en proyectos anteriores.
- **Estructura:** `backend/` contiene todo el código PHP y no es accesible desde el navegador. `frontend/` es la única carpeta pública.
- **Capas:** el controlador recibe la petición, el servicio aplica las reglas y el repositorio es el único que escribe SQL.
- **Seguridad:** consultas preparadas, filtros validados contra una lista de valores permitidos, y los errores internos solo se guardan en el log.
- **Textos:** todos salen de `backend/lang/es.php`, listos para añadir otro idioma.
- **Configuración:** los datos de conexión viven en `backend/.env`, fuera de la carpeta pública y fuera de Git.

## Qué haría diferente con más tiempo
_Pendiente._

## Escalabilidad
_Pendiente._