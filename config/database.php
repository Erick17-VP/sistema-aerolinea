<?php
/**
 * Configuración de conexión a la base de datos
 */

class Database {
    private $host = 'localhost';
    private $db = 'aerolinea_db';
    private $user = 'root';
    private $password = '';
    private $conn;

    public function connect() {
        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db,
                $this->user,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            return null;
        }
    }

    public function getConnection() {
        return $this->conn ?? $this->connect();
    }
}
?>
