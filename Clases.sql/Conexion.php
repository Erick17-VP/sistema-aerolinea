<?php
class Conexion {
    private $conexion;

    // Constructor: establece la conexión con la base de datos
    public function __construct() {
        $this->conexion = new mysqli("localhost", "root", "", "aerolinea");
        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
    }

    // Ejecutar consultas que no devuelven resultados (INSERT, UPDATE, DELETE)
    public function ejecutar($sql) {
        return $this->conexion->query($sql);
    }

    // Consultar datos (SELECT)
    public function consultar($sql) {
        return $this->conexion->query($sql);
    }

    // Obtener el último ID insertado
    public function obtenerUltimoID() {
        return $this->conexion->insert_id;
    }

    // Cerrar la conexión
    public function cerrar() {
        $this->conexion->close();
    }
}
?>
