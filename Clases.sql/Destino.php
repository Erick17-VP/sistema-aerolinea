<?php
include_once("Conexion.php");

class Destino {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
    }

    public function agregar($ciudad, $pais, $codigo) {
        $sql = "INSERT INTO destinos (ciudad, pais, codigo_aeropuerto)
                VALUES ('$ciudad', '$pais', '$codigo')";
        return $this->conexion->ejecutar($sql);
    }

    public function listar() {
        return $this->conexion->consultar("SELECT * FROM destinos");
    }

    public function eliminar($id) {
        return $this->conexion->ejecutar("DELETE FROM destinos WHERE id = $id");
    }
}
?>
