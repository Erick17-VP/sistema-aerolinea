CREATE DATABASE aerolinea_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE aerolinea_db;

-- 2. CREACIÓN DE TABLAS

CREATE TABLE aviones (
    id_avion INT AUTO_INCREMENT PRIMARY KEY,
    numero_matricula VARCHAR(50) NOT NULL UNIQUE,
    modelo VARCHAR(100) NOT NULL,
    capacidad INT NOT NULL,
    año_fabricacion INT,
    ultimo_mantenimiento DATE,
    estado VARCHAR(50) DEFAULT 'Activo'
) ENGINE=InnoDB;

CREATE TABLE pilotos (
    id_piloto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    numero_licencia VARCHAR(50) NOT NULL UNIQUE,
    horas_vuelo INT DEFAULT 0,
    especialidad VARCHAR(100),
    estado VARCHAR(50) DEFAULT 'Activo',
    sueldo DECIMAL(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB;

CREATE TABLE destinos (
    id_destino INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    pais VARCHAR(100) NOT NULL,
    codigo_aeropuerto VARCHAR(10) NOT NULL UNIQUE,
    ciudad VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE clases_vuelo (
    id_clase INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio_base DECIMAL(10,2) NOT NULL,
    cantidad_asientos INT NOT NULL,
    servicios TEXT
) ENGINE=InnoDB;

CREATE TABLE vuelos (
    id_vuelo INT AUTO_INCREMENT PRIMARY KEY,
    numero_vuelo VARCHAR(50) NOT NULL UNIQUE,
    id_avion INT,
    id_destino_origen INT,
    id_destino_destino INT,
    id_piloto INT,
    fecha_salida DATE NOT NULL,
    hora_salida TIME NOT NULL,
    fecha_llegada DATE NOT NULL,
    hora_llegada TIME NOT NULL,
    precio_base DECIMAL(10,2) NOT NULL,
    estado VARCHAR(50) DEFAULT 'Programado',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_avion) REFERENCES aviones(id_avion) ON DELETE SET NULL,
    FOREIGN KEY (id_destino_origen) REFERENCES destinos(id_destino) ON DELETE SET NULL,
    FOREIGN KEY (id_destino_destino) REFERENCES destinos(id_destino) ON DELETE SET NULL,
    FOREIGN KEY (id_piloto) REFERENCES pilotos(id_piloto) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE reservas (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    numero_reserva VARCHAR(50) NOT NULL UNIQUE,
    id_vuelo INT,
    id_clase INT,
    nombre_pasajero VARCHAR(100) NOT NULL,
    apellido_pasajero VARCHAR(100) NOT NULL,
    numero_cedula VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20),
    numero_asiento VARCHAR(10) NOT NULL,
    precio_total DECIMAL(10,2) NOT NULL,
    estado VARCHAR(50) DEFAULT 'Confirmada',
    fecha_reserva TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_vuelo) REFERENCES vuelos(id_vuelo) ON DELETE CASCADE,
    FOREIGN KEY (id_clase) REFERENCES clases_vuelo(id_clase) ON DELETE SET NULL
) ENGINE=InnoDB;


-- ==========================================================
-- 3. INSERCIÓN DE DATOS DE PRUEBA (POBLAR LA BASE DE DATOS)
-- ==========================================================

-- Insertar Flota de Aviones
INSERT INTO aviones (numero_matricula, modelo, capacidad, año_fabricacion, ultimo_mantenimiento, estado) VALUES
('XA-VLO', 'Boeing 737 MAX', 180, 2019, '2023-10-15', 'Activo'),
('XA-AER', 'Airbus A320neo', 150, 2021, '2023-11-20', 'Activo'),
('XA-FLY', 'Boeing 787 Dreamliner', 250, 2018, '2023-09-05', 'Activo');

-- Insertar Escuadrón de Pilotos
INSERT INTO pilotos (nombre, apellido, numero_licencia, horas_vuelo, especialidad, estado, sueldo) VALUES
('Carlos', 'Pérez', 'LIC-1001', 2500, 'Vuelos Nacionales', 'Activo', 4500.00),
('Ana', 'Gómez', 'LIC-1002', 4200, 'Vuelos Transatlánticos', 'Activo', 6500.00),
('Javier', 'Hernández', 'LIC-1003', 1200, 'Copiloto de Carga', 'Activo', 2800.00);

-- Insertar Destinos / Aeropuertos
INSERT INTO destinos (nombre, pais, codigo_aeropuerto, ciudad) VALUES
('Aeropuerto Internacional Benito Juárez', 'México', 'MEX', 'Ciudad de México'),
('Aeropuerto Internacional John F. Kennedy', 'Estados Unidos', 'JFK', 'Nueva York'),
('Aeropuerto Internacional El Dorado', 'Colombia', 'BOG', 'Bogotá'),
('Aeropuerto de París-Charles de Gaulle', 'Francia', 'CDG', 'París');

-- Insertar Clases de Vuelo y Tarifas
INSERT INTO clases_vuelo (nombre, descripcion, precio_base, cantidad_asientos, servicios) VALUES
('Económica', 'Asiento estándar. Ideal para vuelos cortos.', 50.00, 120, 'Equipaje de mano (10kg), Snack básico'),
('Premium Economy', 'Mayor espacio para las piernas y reclinación.', 150.00, 40, 'Equipaje facturado (23kg), Embarque prioritario'),
('Ejecutiva (Business)', 'Asientos tipo cama y privacidad.', 400.00, 20, 'Acceso VIP, Comida a la carta, WiFi gratis');

-- Insertar Vuelos Programados
INSERT INTO vuelos (numero_vuelo, id_avion, id_destino_origen, id_destino_destino, id_piloto, fecha_salida, hora_salida, fecha_llegada, hora_llegada, precio_base, estado) VALUES
('VUE-MXNY1', 1, 1, 2, 1, '2024-06-15', '08:00:00', '2024-06-15', '13:30:00', 350.00, 'Programado'),
('VUE-BGMX2', 2, 3, 1, 3, '2024-06-16', '14:00:00', '2024-06-16', '18:15:00', 250.00, 'Programado'),
('VUE-MXPR3', 3, 1, 4, 2, '2024-06-20', '20:00:00', '2024-06-21', '14:00:00', 850.00, 'Programado');

-- Insertar Reservas Confirmadas
INSERT INTO reservas (numero_reserva, id_vuelo, id_clase, nombre_pasajero, apellido_pasajero, numero_cedula, email, telefono, numero_asiento, precio_total, estado) VALUES
('RES-A8B9C0', 1, 1, 'Roberto', 'Sánchez', 'INE-9876543', 'roberto@mail.com', '5512345678', '12A', 400.00, 'Confirmada'),
('RES-X1Y2Z3', 1, 3, 'Mónica', 'Valdez', 'PAS-1234567', 'monica@mail.com', '5598765432', '2B', 750.00, 'Confirmada'),
('RES-L9M8N7', 3, 2, 'Fernando', 'Torres', 'INE-5544332', 'fer@mail.com', '5544332211', '15C', 1000.00, 'Confirmada');