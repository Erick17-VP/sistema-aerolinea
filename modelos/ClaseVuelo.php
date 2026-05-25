<?php

require_once __DIR__ . '/../config/database.php';

class ClaseVuelo {
    private $db;
    private $table = 'clases_vuelo';

    // Propiedades
    public $id_clase;
    public $nombre;
    public $descripcion;
    public $precio_base;
    public $cantidad_asientos;
    public $servicios;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    // Obtener todas las clases de vuelo
    public function obtenerTodas() {
        try {
            $query = "SELECT * FROM " . $this->table . " ORDER BY nombre ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    // Obtener clase de vuelo por ID
    public function obtenerPorId($id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id_clase = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    // Crear nueva clase de vuelo
    public function crear() {
        try {
            $query = "INSERT INTO " . $this->table . "
                      (nombre, descripcion, precio_base, cantidad_asientos, servicios)
                      VALUES (:nombre, :descripcion, :precio_base, :cantidad_asientos, :servicios)";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':descripcion', $this->descripcion);
            $stmt->bindParam(':precio_base', $this->precio_base);
            $stmt->bindParam(':cantidad_asientos', $this->cantidad_asientos);
            $stmt->bindParam(':servicios', $this->servicios);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    // Actualizar clase de vuelo
    public function actualizar() {
        try {
            $query = "UPDATE " . $this->table . "
                      SET nombre = :nombre,
                          descripcion = :descripcion,
                          precio_base = :precio_base,
                          cantidad_asientos = :cantidad_asientos,
                          servicios = :servicios
                      WHERE id_clase = :id";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':id', $this->id_clase);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':descripcion', $this->descripcion);
            $stmt->bindParam(':precio_base', $this->precio_base);
            $stmt->bindParam(':cantidad_asientos', $this->cantidad_asientos);
            $stmt->bindParam(':servicios', $this->servicios);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    // Eliminar clase de vuelo
    public function eliminar() {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id_clase = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $this->id_clase);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
?>
