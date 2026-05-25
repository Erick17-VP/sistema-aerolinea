<?php
include_once("Conexion.php");

class Pasajero {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
    }

    public function agregar($nombre, $email, $telefono) {
        $sql = "INSERT INTO pasajeros (nombre, email, telefono)
                VALUES ('$nombre', '$email', '$telefono')";
        return $this->conexion->ejecutar($sql);
    }

    public function listar() {
        return $this->conexion->consultar("SELECT * FROM pasajeros");
    }

    public function eliminar($id) {
        return $this->conexion->ejecutar("DELETE FROM pasajeros WHERE id = $id");
    }
}
?>
