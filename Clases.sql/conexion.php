<?php

class Conexion {
    private $host = "localhost";
    private $usuario = "root";
    private $password = "";
    private $bd = "aviones";

    public $conexion;

    public function __construct() {
        $this->conexion = new mysqli(
            $this->host,
            $this->usuario,
            $this->password,
            $this->bd
        );

        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
    }

    public function ejecutar($sql) {
        return $this->conexion->query($sql);
    }

    public function consultar($sql) {
        return $this->conexion->query($sql);
    }
}
?>
