<?php
include_once("Conexion.php");

class Vuelo {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $db->conexion;
    }

    public function agregar($numero, $avion_id, $piloto_id, $origen_id, $destino_id, $fecha_hora, $precio_base, $estado) {
        $sql = "INSERT INTO vuelos (numero_vuelo, avion_id, piloto_id, origen_id, destino_id, fecha_hora, precio_base, estado)
                VALUES ('$numero', '$avion_id', '$piloto_id', '$origen_id', '$destino_id', '$fecha_hora', '$precio_base', '$estado')";
        return $this->conexion->ejecutar($sql);
    }

    public function listar() {
        $sql = "SELECT v.id, v.numero_vuelo, a.modelo AS avion, p.nombre AS piloto,
                       o.ciudad AS origen, d.ciudad AS destino, v.fecha_hora, v.precio_base, v.estado
                FROM vuelos v
                LEFT JOIN aviones a ON v.avion_id = a.id
                LEFT JOIN pilotos p ON v.piloto_id = p.id
                LEFT JOIN destinos o ON v.origen_id = o.id
                LEFT JOIN destinos d ON v.destino_id = d.id";
        return $this->conexion->consultar($sql);
    }

    public function eliminar($id) {
        return $this->conexion->ejecutar("DELETE FROM vuelos WHERE id = $id");
    }
}
?>
