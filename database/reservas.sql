CREATE DATABASE IF NOT EXISTS misterhost
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE misterhost;

SET NAMES utf8mb4;

-- Las tablas se recrean en cada importación para poder repetirla desde cero.
DROP TABLE IF EXISTS reserva_evento;
DROP TABLE IF EXISTS reserva;

CREATE TABLE reserva (
  id                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre_huesped      VARCHAR(120) NOT NULL,
  email_huesped       VARCHAR(160) NOT NULL,
  nombre_alojamiento  VARCHAR(160) NOT NULL,
  fecha_entrada       DATE NOT NULL,
  fecha_salida        DATE NOT NULL,
  estado              ENUM('pendiente', 'confirmada', 'cancelada') NOT NULL DEFAULT 'pendiente',
  importe             DECIMAL(10,2) NOT NULL,
  notas               TEXT NULL,
  fecha_creacion      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  -- La estancia mínima es de una noche.
  CONSTRAINT chk_reserva_fechas CHECK (fecha_salida > fecha_entrada),
  CONSTRAINT chk_reserva_importe CHECK (importe >= 0),

  -- Índices para los filtros del listado (estado y fechas).
  INDEX idx_reserva_estado (estado),
  INDEX idx_reserva_fechas (fecha_entrada, fecha_salida)
) ENGINE=InnoDB;

-- Historial de cada reserva: una fila por creación o cambio de estado.
CREATE TABLE reserva_evento (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  reserva_id      INT UNSIGNED NOT NULL,
  tipo            VARCHAR(30) NOT NULL,
  descripcion     VARCHAR(255) NOT NULL,
  fecha_creacion  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  CONSTRAINT fk_evento_reserva FOREIGN KEY (reserva_id) REFERENCES reserva (id),
  
  INDEX idx_evento_reserva (reserva_id)
) ENGINE=InnoDB;

-- Datos de ejemplo: reservas en los tres estados.
INSERT INTO reserva (id, nombre_huesped, email_huesped, nombre_alojamiento, fecha_entrada, fecha_salida, estado, importe, notas, fecha_creacion) VALUES
(1, 'Laura Gómez Pérez', 'laura.gomez@example.com', 'Hotel Mirador del Mar (Málaga)', '2026-11-05', '2026-11-08', 'confirmada', 385.50, 'Llegada prevista por la tarde', '2026-09-28 10:15:00'),
(2, 'Carlos Ruiz Martín', 'carlos.ruiz@example.com', 'Apartamentos Plaza Mayor (Madrid)', '2026-11-12', '2026-11-15', 'pendiente', 270.00, NULL, '2026-10-10 09:30:00'),
(3, 'Marta Sánchez Díaz', 'marta.sanchez@example.com', 'Casa Rural El Olivar (Toledo)', '2026-12-26', '2026-12-30', 'confirmada', 520.00, 'Viaja con un perro pequeño', '2026-10-02 18:40:00'),
(4, 'Javier Fernández López', 'javier.fernandez@example.com', 'Hostal Casco Antiguo (Sevilla)', '2026-10-23', '2026-10-25', 'cancelada', 148.00, 'Cancelada por cambio de planes', '2026-09-15 12:05:00'),
(5, 'Elena Torres Navarro', 'elena.torres@example.com', 'Hotel Playa Dorada (Valencia)', '2027-01-08', '2027-01-15', 'pendiente', 745.00, 'Solicita cuna en la habitación', '2026-10-14 20:10:00'),
(6, 'Pablo Moreno Gil', 'pablo.moreno@example.com', 'Apartamento Vistas al Teide (Tenerife)', '2027-02-03', '2027-02-10', 'confirmada', 910.00, NULL, '2026-10-05 11:20:00'),
(7, 'Lucía Romero Vidal', 'lucia.romero@example.com', 'Hotel Mirador del Mar (Málaga)', '2026-09-18', '2026-09-21', 'confirmada', 360.00, NULL, '2026-08-30 16:45:00'),
(8, 'Andrés Castillo Ortega', 'andres.castillo@example.com', 'Casa Rural El Olivar (Toledo)', '2026-11-20', '2026-11-22', 'cancelada', 210.00, 'El huésped no puede viajar en esas fechas', '2026-10-01 08:55:00'),
(9, 'Sofía Herrera Campos', 'sofia.herrera@example.com', 'Hostal Casco Antiguo (Sevilla)', '2026-12-04', '2026-12-07', 'pendiente', 297.00, 'Llegada tarde, después de las 22:00', '2026-10-15 13:25:00'),
(10, 'Daniel Ortiz Prieto', 'daniel.ortiz@example.com', 'Hotel Playa Dorada (Valencia)', '2026-10-30', '2026-11-02', 'confirmada', 405.00, NULL, '2026-10-08 17:00:00');

-- Historial de cada reserva de ejemplo.
INSERT INTO reserva_evento (reserva_id, tipo, descripcion, fecha_creacion) VALUES
(1, 'creada', 'Reserva creada en estado pendiente', '2026-09-28 10:15:00'),
(1, 'confirmada', 'Reserva confirmada', '2026-09-28 12:30:00'),
(2, 'creada', 'Reserva creada en estado pendiente', '2026-10-10 09:30:00'),
(3, 'creada', 'Reserva creada en estado pendiente', '2026-10-02 18:40:00'),
(3, 'confirmada', 'Reserva confirmada', '2026-10-03 09:10:00'),
(4, 'creada', 'Reserva creada en estado pendiente', '2026-09-15 12:05:00'),
(4, 'cancelada', 'Reserva cancelada', '2026-09-20 17:45:00'),
(5, 'creada', 'Reserva creada en estado pendiente', '2026-10-14 20:10:00'),
(6, 'creada', 'Reserva creada en estado pendiente', '2026-10-05 11:20:00'),
(6, 'confirmada', 'Reserva confirmada', '2026-10-05 15:00:00'),
(7, 'creada', 'Reserva creada en estado pendiente', '2026-08-30 16:45:00'),
(7, 'confirmada', 'Reserva confirmada', '2026-08-31 10:00:00'),
(8, 'creada', 'Reserva creada en estado pendiente', '2026-10-01 08:55:00'),
(8, 'confirmada', 'Reserva confirmada', '2026-10-01 14:20:00'),
(8, 'cancelada', 'Reserva cancelada', '2026-10-09 19:05:00'),
(9, 'creada', 'Reserva creada en estado pendiente', '2026-10-15 13:25:00'),
(10, 'creada', 'Reserva creada en estado pendiente', '2026-10-08 17:00:00'),
(10, 'confirmada', 'Reserva confirmada', '2026-10-09 09:30:00');