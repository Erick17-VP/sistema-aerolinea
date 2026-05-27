<?php
include_once("Conexion.php");

class ClaseVuelo
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = new Conexion();
        $this->conexion = $db->conexion;
    }

    public function agregar($nombre, $factor_precio)
    {
        $sql = "INSERT INTO clases_vuelo (nombre, factor_precio)
                VALUES ('$nombre', '$factor_precio')";
        return $this->conexion->ejecutar($sql);
    }

    public function listar()
    {
        return $this->conexion->consultar("SELECT * FROM clases_vuelo");
    }

    public function eliminar($id)
    {
        return $this->conexion->ejecutar("DELETE FROM clases_vuelo WHERE id = $id");
    }
}
