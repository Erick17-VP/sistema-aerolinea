<?php
require_once __DIR__ . '/../config/database.php';
class Destino {
    private $db;
    private $table = 'destinos';

    // Propiedades
    public $id_destino;
    public $nombre;
    public $pais;
    public $codigo_aeropuerto;
    public $ciudad;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    //obtener todos los destinos
    public function obtenerTodos() {
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

    // Obtener destino por ID
    public function obtenerPorId($id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id_destino = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    // Crear nuevo destino
    public function crear() {
        try {
            $query = "INSERT INTO " . $this->table . "
                      (nombre, pais, codigo_aeropuerto, ciudad)
                      VALUES (:nombre, :pais, :codigo_aeropuerto, :ciudad)";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':pais', $this->pais);
            $stmt->bindParam(':codigo_aeropuerto', $this->codigo_aeropuerto);
            $stmt->bindParam(':ciudad', $this->ciudad);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Actualizar destino
    public function actualizar() {
        try {
            $query = "UPDATE " . $this->table . "
                      SET nombre = :nombre,
                          pais = :pais,
                          codigo_aeropuerto = :codigo_aeropuerto,
                          ciudad = :ciudad
                      WHERE id_destino = :id";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':id', $this->id_destino);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':pais', $this->pais);
            $stmt->bindParam(':codigo_aeropuerto', $this->codigo_aeropuerto);
            $stmt->bindParam(':ciudad', $this->ciudad);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Eliminar destino
    public function eliminar() {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id_destino = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $this->id_destino);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
?>
