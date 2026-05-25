<?php
require_once __DIR__ . '/../config/database.php';

class Avion {
    private $db;
    private $table = 'aviones';

    // Propiedades
    public $id_avion;
    public $numero_matricula;
    public $modelo;
    public $capacidad;
    public $año_fabricacion;
    public $ultimo_mantenimiento;
    public $estado;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    // Obtener todos los aviones
    public function obtenerTodos() {
        try {
            $query = "SELECT * FROM " . $this->table . " ORDER BY numero_matricula ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    // Obtener avión por ID
    public function obtenerPorId($id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id_avion = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    //  Crear nuevo avión
    public function crear() {
        try {
            $query = "INSERT INTO " . $this->table . "
                      (numero_matricula, modelo, capacidad, año_fabricacion, ultimo_mantenimiento, estado)
                      VALUES (:numero_matricula, :modelo, :capacidad, :año_fabricacion, :ultimo_mantenimiento, :estado)";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':numero_matricula', $this->numero_matricula);
            $stmt->bindParam(':modelo', $this->modelo);
            $stmt->bindParam(':capacidad', $this->capacidad);
            $stmt->bindParam(':año_fabricacion', $this->año_fabricacion);
            $stmt->bindParam(':ultimo_mantenimiento', $this->ultimo_mantenimiento);
            $stmt->bindParam(':estado', $this->estado);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Actualizar avión
    public function actualizar() {
        try {
            $query = "UPDATE " . $this->table . "
                      SET numero_matricula = :numero_matricula,
                          modelo = :modelo,
                          capacidad = :capacidad,
                          año_fabricacion = :año_fabricacion,
                          ultimo_mantenimiento = :ultimo_mantenimiento,
                          estado = :estado
                      WHERE id_avion = :id";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':id', $this->id_avion);
            $stmt->bindParam(':numero_matricula', $this->numero_matricula);
            $stmt->bindParam(':modelo', $this->modelo);
            $stmt->bindParam(':capacidad', $this->capacidad);
            $stmt->bindParam(':año_fabricacion', $this->año_fabricacion);
            $stmt->bindParam(':ultimo_mantenimiento', $this->ultimo_mantenimiento);
            $stmt->bindParam(':estado', $this->estado);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Eliminar avión
    public function eliminar() {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id_avion = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $this->id_avion);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
?>
