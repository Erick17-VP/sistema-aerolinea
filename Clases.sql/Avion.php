<?php
include_once("Conexion.php");

class Avion {
    private $conexion;
    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->conexion;
    }
    
    public function agregar($modelo, $capacidad) {
        $stmt = $this->conexion->prepare(
            "INSERT INTO aviones (modelo, capacidad)
             VALUES (?, ?)"
        );
        $stmt->bind_param("si", $modelo, $capacidad);
        return $stmt->execute();
    }
    
    public function listar() {
        $sql = "SELECT * FROM aviones";
        return $this->conexion->query($sql);
    }

    public function eliminar($id) {
        $stmt = $this->conexion->prepare(
            "DELETE FROM aviones WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
