<?php
include_once("Conexion.php");

class Avion {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
    }

    public function agregar($modelo, $capacidad) {
        $sql = "INSERT INTO aviones (modelo, capacidad) VALUES ('$modelo', '$capacidad')";
        return $this->conexion->ejecutar($sql);
    }

    public function listar() {
        return $this->conexion->consultar("SELECT * FROM aviones");
    }

    public function eliminar($id) {
        return $this->conexion->ejecutar("DELETE FROM aviones WHERE id = $id");
    }
}
?>
