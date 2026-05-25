<?php
include_once("Conexion.php");

class Piloto {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
    }

    public function agregar($nombre, $licencia) {
        $sql = "INSERT INTO pilotos (nombre, licencia) VALUES ('$nombre', '$licencia')";
        return $this->conexion->ejecutar($sql);
    }

    public function listar() {
        return $this->conexion->consultar("SELECT * FROM pilotos");
    }

    public function eliminar($id) {
        return $this->conexion->ejecutar("DELETE FROM pilotos WHERE id = $id");
    }
}
?>
