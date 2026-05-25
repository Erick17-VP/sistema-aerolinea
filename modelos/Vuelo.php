<?php

require_once __DIR__ . '/../config/database.php';

class Vuelo {
    private $db;
    private $table = 'vuelos';

    // Propiedades
    public $id_vuelo;
    public $numero_vuelo;
    public $id_avion;
    public $id_destino_origen;
    public $id_destino_destino;
    public $id_piloto;
    public $fecha_salida;
    public $hora_salida;
    public $fecha_llegada;
    public $hora_llegada;
    public $precio_base;
    public $estado;
    public $fecha_creacion;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    // Obtener todos los vuelos con detalles
    public function obtenerTodos() {
        try {
            $query = "SELECT v.*,
                             a.numero_matricula,
                             a.modelo,
                             p.nombre as piloto_nombre,
                             p.apellido as piloto_apellido,
                             do.nombre as origen_nombre,
                             dd.nombre as destino_nombre
                      FROM " . $this->table . " v
                      INNER JOIN aviones a ON v.id_avion = a.id_avion
                      INNER JOIN pilotos p ON v.id_piloto = p.id_piloto
                      INNER JOIN destinos do ON v.id_destino_origen = do.id_destino
                      INNER JOIN destinos dd ON v.id_destino_destino = dd.id_destino
                      ORDER BY v.fecha_salida DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    // Obtener vuelo por ID con detalles
    public function obtenerPorId($id) {
        try {
            $query = "SELECT v.*,
                             a.numero_matricula,
                             a.modelo,
                             a.capacidad,
                             p.nombre as piloto_nombre,
                             p.apellido as piloto_apellido,
                             do.nombre as origen_nombre,
                             do.pais as origen_pais,
                             dd.nombre as destino_nombre,
                             dd.pais as destino_pais
                      FROM " . $this->table . " v
                      INNER JOIN aviones a ON v.id_avion = a.id_avion
                      INNER JOIN pilotos p ON v.id_piloto = p.id_piloto
                      INNER JOIN destinos do ON v.id_destino_origen = do.id_destino
                      INNER JOIN destinos dd ON v.id_destino_destino = dd.id_destino
                      WHERE v.id_vuelo = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    // Obtener vuelos por destino
    public function obtenerPorDestino($id_destino) {
        try {
            $query = "SELECT * FROM " . $this->table . "
                      WHERE id_destino_destino = :id_destino
                      AND estado = 'activo'
                      ORDER BY fecha_salida ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_destino', $id_destino);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    // Obtener vuelos disponibles (estado = 'disponible' y fecha_salida > fecha actual)
    public function obtenerDisponibles() {
        try {
            $query = "SELECT * FROM " . $this->table . "
                      WHERE estado = 'disponible'
                      AND fecha_salida > NOW()
                      ORDER BY fecha_salida ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    //  Crear nuevo vuelo
    public function crear() {
        try {
            $query = "INSERT INTO " . $this->table . "
                      (numero_vuelo, id_avion, id_destino_origen, id_destino_destino, 
                       id_piloto, fecha_salida, hora_salida, fecha_llegada, hora_llegada, 
                       precio_base, estado)
                      VALUES (:numero_vuelo, :id_avion, :id_destino_origen, :id_destino_destino,
                              :id_piloto, :fecha_salida, :hora_salida, :fecha_llegada, :hora_llegada,
                              :precio_base, :estado)";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':numero_vuelo', $this->numero_vuelo);
            $stmt->bindParam(':id_avion', $this->id_avion);
            $stmt->bindParam(':id_destino_origen', $this->id_destino_origen);
            $stmt->bindParam(':id_destino_destino', $this->id_destino_destino);
            $stmt->bindParam(':id_piloto', $this->id_piloto);
            $stmt->bindParam(':fecha_salida', $this->fecha_salida);
            $stmt->bindParam(':hora_salida', $this->hora_salida);
            $stmt->bindParam(':fecha_llegada', $this->fecha_llegada);
            $stmt->bindParam(':hora_llegada', $this->hora_llegada);
            $stmt->bindParam(':precio_base', $this->precio_base);
            $stmt->bindParam(':estado', $this->estado);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Actualizar vuelo
    public function actualizar() {
        try {
            $query = "UPDATE " . $this->table . "
                      SET numero_vuelo = :numero_vuelo,
                          id_avion = :id_avion,
                          id_destino_origen = :id_destino_origen,
                          id_destino_destino = :id_destino_destino,
                          id_piloto = :id_piloto,
                          fecha_salida = :fecha_salida,
                          hora_salida = :hora_salida,
                          fecha_llegada = :fecha_llegada,
                          hora_llegada = :hora_llegada,
                          precio_base = :precio_base,
                          estado = :estado
                      WHERE id_vuelo = :id";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':id', $this->id_vuelo);
            $stmt->bindParam(':numero_vuelo', $this->numero_vuelo);
            $stmt->bindParam(':id_avion', $this->id_avion);
            $stmt->bindParam(':id_destino_origen', $this->id_destino_origen);
            $stmt->bindParam(':id_destino_destino', $this->id_destino_destino);
            $stmt->bindParam(':id_piloto', $this->id_piloto);
            $stmt->bindParam(':fecha_salida', $this->fecha_salida);
            $stmt->bindParam(':hora_salida', $this->hora_salida);
            $stmt->bindParam(':fecha_llegada', $this->fecha_llegada);
            $stmt->bindParam(':hora_llegada', $this->hora_llegada);
            $stmt->bindParam(':precio_base', $this->precio_base);
            $stmt->bindParam(':estado', $this->estado);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Eliminar vuelo
    public function eliminar() {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id_vuelo = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $this->id_vuelo);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Cambiar estado del vuelo (activo, cancelado, completado)
    public function cambiarEstado($nuevoEstado) {
        try {
            $query = "UPDATE " . $this->table . "
                      SET estado = :estado
                      WHERE id_vuelo = :id";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':estado', $nuevoEstado);
            $stmt->bindParam(':id', $this->id_vuelo);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
?>
