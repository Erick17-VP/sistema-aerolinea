<?php
include_once("Conexion.php");

class Reserva {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $db->conexion;
    }

    public function crear($vuelo_id, $pasajero_id, $clase_id, $asiento, $precio_final) {
        $sql = "INSERT INTO reservas (vuelo_id, pasajero_id, clase_id, asiento, precio_final)
                VALUES ('$vuelo_id', '$pasajero_id', '$clase_id', '$asiento', '$precio_final')";
        return $this->conexion->ejecutar($sql);
    }

    public function listar() {
        $sql = "SELECT r.id, v.numero_vuelo, p.nombre AS pasajero, c.nombre AS clase, r.asiento, r.precio_final, r.fecha_reserva
                FROM reservas r
                JOIN vuelos v ON r.vuelo_id = v.id
                JOIN pasajeros p ON r.pasajero_id = p.id
                JOIN clases_vuelo c ON r.clase_id = c.id";
        return $this->conexion->consultar($sql);
    }

    public function eliminar($id) {
        return $this->conexion->ejecutar("DELETE FROM reservas WHERE id = $id");
    }
}
?>
