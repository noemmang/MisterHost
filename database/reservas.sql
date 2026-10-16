CREATE DATABASE IF NOT EXISTS misterhost
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE misterhost;

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

  -- Índices para los filtros del listado.
  INDEX idx_reserva_estado (estado),
  INDEX idx_reserva_fechas (fecha_entrada, fecha_salida)
) ENGINE=InnoDB;

-- Historial de cada reserva: una fila por creación o cambio de estado.
CREATE TABLE reserva_evento (
  id                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  reserva_id          INT UNSIGNED NOT NULL,
  tipo                VARCHAR(30) NOT NULL,
  descripcion         VARCHAR(255) NOT NULL,
  fecha_creacion      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  CONSTRAINT fk_evento_reserva FOREIGN KEY (reserva_id) REFERENCES reserva (id),

  INDEX idx_evento_reserva (reserva_id)
) ENGINE=InnoDB;