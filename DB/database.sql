CREATE TABLE aviones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    modelo VARCHAR(50) NOT NULL,
    capacidad INT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE pilotos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    licencia VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE destinos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ciudad VARCHAR(100) NOT NULL,
    pais VARCHAR(50) NOT NULL,
    codigo_aeropuerto VARCHAR(10) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE clases_vuelo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    factor_precio DECIMAL(3,2) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE pasajeros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20)
) ENGINE=InnoDB;

-- 3. Crear tablas dependientes
CREATE TABLE vuelos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_vuelo VARCHAR(20) NOT NULL UNIQUE,
    avion_id INT,
    piloto_id INT,
    origen_id INT,
    destino_id INT,
    fecha_hora DATETIME NOT NULL,
    precio_base DECIMAL(10,2) NOT NULL,
    estado VARCHAR(30) DEFAULT 'Programado',
    FOREIGN KEY (avion_id) REFERENCES aviones(id) ON DELETE SET NULL,
    FOREIGN KEY (piloto_id) REFERENCES pilotos(id) ON DELETE SET NULL,
    FOREIGN KEY (origen_id) REFERENCES destinos(id) ON DELETE SET NULL,
    FOREIGN KEY (destino_id) REFERENCES destinos(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vuelo_id INT,
    pasajero_id INT,
    clase_id INT,
    asiento VARCHAR(10) NOT NULL,
    precio_final DECIMAL(10,2) NOT NULL,
    fecha_reserva TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vuelo_id) REFERENCES vuelos(id) ON DELETE CASCADE,
    FOREIGN KEY (pasajero_id) REFERENCES pasajeros(id) ON DELETE CASCADE,
    FOREIGN KEY (clase_id) REFERENCES clases_vuelo(id) ON DELETE SET NULL
) ENGINE=InnoDB;