<?php
require_once __DIR__ . '/../config/database.php';

class Piloto {
    private $db;
    private $table = 'pilotos';

    // Propiedades
    public $id_piloto;
    public $nombre;
    public $apellido;
    public $numero_licencia;
    public $horas_vuelo;
    public $especialidad;
    public $estado;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    // Obtener todos los pilotos
    public function obtenerTodos() {
        try {
            $query = "SELECT * FROM " . $this->table . " ORDER BY apellido ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    // Obtener piloto por ID
    public function obtenerPorId($id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id_piloto = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    // Crear nuevo piloto
    public function crear() {
        try {
            $query = "INSERT INTO " . $this->table . "
                      (nombre, apellido, numero_licencia, horas_vuelo, especialidad, estado)
                      VALUES (:nombre, :apellido, :numero_licencia, :horas_vuelo, :especialidad, :estado)";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':apellido', $this->apellido);
            $stmt->bindParam(':numero_licencia', $this->numero_licencia);
            $stmt->bindParam(':horas_vuelo', $this->horas_vuelo);
            $stmt->bindParam(':especialidad', $this->especialidad);
            $stmt->bindParam(':estado', $this->estado);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Actualizar piloto
    public function actualizar() {
        try {
            $query = "UPDATE " . $this->table . "
                      SET nombre = :nombre,
                          apellido = :apellido,
                          numero_licencia = :numero_licencia,
                          horas_vuelo = :horas_vuelo,
                          especialidad = :especialidad,
                          estado = :estado
                      WHERE id_piloto = :id";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':id', $this->id_piloto);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':apellido', $this->apellido);
            $stmt->bindParam(':numero_licencia', $this->numero_licencia);
            $stmt->bindParam(':horas_vuelo', $this->horas_vuelo);
            $stmt->bindParam(':especialidad', $this->especialidad);
            $stmt->bindParam(':estado', $this->estado);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Eliminar piloto
    public function eliminar() {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id_piloto = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $this->id_piloto);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
?>
