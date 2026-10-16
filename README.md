# MisterHost

Gestión de reservas de alojamiento en PHP nativo, Twig y JavaScript vanilla.
Prueba técnica para MisterPlan.

## Estado
En construcción, paso a paso.

## Requisitos
- PHP 8.2 o superior
- MySQL 8.0.16+ o MariaDB 10.2+

## Base de datos

El script `database/reservas.sql` crea la base de datos `misterhost` y sus tablas. Se puede importar tantas veces como se quiera: recrea las tablas desde cero.

```bash
mysql -u root -p -e "source database/reservas.sql"
```

## Arrancar en local

```bash
php -S localhost:8000 -t frontend
```

Después abrir http://localhost:8000

## Mejoras y escalabilidad
_Pendiente._