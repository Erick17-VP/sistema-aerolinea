REATE DATABASE aerolinea_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE aerolinea_db;

-- 1. AVIONES
CREATE TABLE aviones (
    id_avion INT AUTO_INCREMENT PRIMARY KEY,
    numero_matricula VARCHAR(50) NOT NULL UNIQUE,
    modelo VARCHAR(100) NOT NULL,
    capacidad INT NOT NULL,
    estado VARCHAR(50) DEFAULT 'Activo'
) ENGINE=InnoDB;

-- 2. PILOTOS
CREATE TABLE pilotos (
    id_piloto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    numero_licencia VARCHAR(50) NOT NULL UNIQUE,
    sueldo DECIMAL(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB;

-- 3. DESTINOS (RUTAS)
CREATE TABLE destinos (
    id_destino INT AUTO_INCREMENT PRIMARY KEY,
    origen VARCHAR(100) NOT NULL,
    destino VARCHAR(100) NOT NULL,
    codigo_aeropuerto VARCHAR(10) NOT NULL,
    duracion_estimada VARCHAR(20) NOT NULL
) ENGINE=InnoDB;

-- 4. CLASES DE VUELO (Las tarifas extras)
CREATE TABLE clases_vuelo (
    id_clase INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    cargo_adicional DECIMAL(10,2) NOT NULL,
    descripcion VARCHAR(255)
) ENGINE=InnoDB;

-- 5. VUELOS DISPONIBLES (La cartelera que ve el cliente)
CREATE TABLE vuelos (
    id_vuelo INT AUTO_INCREMENT PRIMARY KEY,
    numero_vuelo VARCHAR(50) NOT NULL UNIQUE,
    id_avion INT,
    id_destino INT,
    id_piloto INT,
    fecha_salida DATE NOT NULL,
    hora_salida TIME NOT NULL,
    precio_base DECIMAL(10,2) NOT NULL,
    estado VARCHAR(50) DEFAULT 'Disponible',
    FOREIGN KEY (id_avion) REFERENCES aviones(id_avion) ON DELETE SET NULL,
    FOREIGN KEY (id_destino) REFERENCES destinos(id_destino) ON DELETE SET NULL,
    FOREIGN KEY (id_piloto) REFERENCES pilotos(id_piloto) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 6. RESERVAS (La compra real del Boleto por el Cliente)
CREATE TABLE reservas (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    numero_reserva VARCHAR(50) NOT NULL UNIQUE,
    id_vuelo INT,
    id_clase INT,
    nombre_pasajero VARCHAR(100) NOT NULL,
    apellido_pasajero VARCHAR(100) NOT NULL,
    numero_cedula VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    numero_asiento VARCHAR(10) NOT NULL,
    precio_total DECIMAL(10,2) NOT NULL,
    fecha_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_vuelo) REFERENCES vuelos(id_vuelo) ON DELETE CASCADE,
    FOREIGN KEY (id_clase) REFERENCES clases_vuelo(id_clase) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- INSERCIÓN DE DATOS REALISTAS PARA JUGAR
-- ==========================================
INSERT INTO aviones (numero_matricula, modelo, capacidad, estado) VALUES
('XA-AER', 'Airbus A320neo', 150, 'Activo'),
('XA-MAX', 'Boeing 737 MAX 8', 178, 'Activo');

INSERT INTO pilotos (nombre, apellido, numero_licencia, sueldo) VALUES
('Carlos', 'Capitán', 'LIC-9982', 5200.00),
('Laura', 'Aviadora', 'LIC-4412', 6100.00);

INSERT INTO destinos (origen, destino, codigo_aeropuerto, duracion_estimada) VALUES
('CDMX (MEX)', 'Cancún (CUN)', 'CUN', '2h 15m'),
('CDMX (MEX)', 'Nueva York (JFK)', 'JFK', '4h 45m'),
('CDMX (MEX)', 'Madrid (MAD)', 'MAD', '10h 30m');

INSERT INTO clases_vuelo (nombre, cargo_adicional, descripcion) VALUES
('Económica', 0.00, 'Asiento estándar, incluye equipaje de mano.'),
('Premium Economy', 120.00, 'Más espacio para piernas + equipaje documentado.'),
('Ejecutiva (Business)', 350.00, 'Asiento reclinable cama, menú gourmet y acceso a Sala VIP.');

INSERT INTO vuelos (numero_vuelo, id_avion, id_destino, id_piloto, fecha_salida, hora_salida, precio_base) VALUES
('AM-320', 1, 1, 1, '2026-06-15', '08:30:00', 150.00),
('AM-747', 2, 2, 2, '2026-06-16', '14:15:00', 380.00),
('AM-101', 1, 3, 1, '2026-06-20', '21:00:00', 750.00);