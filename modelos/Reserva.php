<?php
require_once __DIR__ . '/../config/database.php';

class Reserva {
    private $db;
    private $table = 'reservas';

    // Propiedades
    public $id_reserva;
    public $numero_reserva;
    public $id_vuelo;
    public $id_clase;
    public $nombre_pasajero;
    public $apellido_pasajero;
    public $numero_cedula;
    public $email;
    public $telefono;
    public $numero_asiento;
    public $precio_total;
    public $estado;
    public $fecha_reserva;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    // Obtener todas las reservas con detalles
    public function obtenerTodas() {
        try {
            $query = "SELECT r.*,
                             v.numero_vuelo,
                             v.fecha_salida,
                             do.nombre as origen,
                             dd.nombre as destino,
                             c.nombre as clase_nombre
                      FROM " . $this->table . " r
                      INNER JOIN vuelos v ON r.id_vuelo = v.id_vuelo
                      INNER JOIN destinos do ON v.id_destino_origen = do.id_destino
                      INNER JOIN destinos dd ON v.id_destino_destino = dd.id_destino
                      INNER JOIN clases_vuelo c ON r.id_clase = c.id_clase
                      ORDER BY r.fecha_reserva DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    // Obtener reserva por ID con detalles
    public function obtenerPorId($id) {
        try {
            $query = "SELECT r.*,
                             v.numero_vuelo,
                             v.fecha_salida,
                             v.hora_salida,
                             do.nombre as origen,
                             dd.nombre as destino,
                             c.nombre as clase_nombre,
                             c.precio_base
                      FROM " . $this->table . " r
                      INNER JOIN vuelos v ON r.id_vuelo = v.id_vuelo
                      INNER JOIN destinos do ON v.id_destino_origen = do.id_destino
                      INNER JOIN destinos dd ON v.id_destino_destino = dd.id_destino
                      INNER JOIN clases_vuelo c ON r.id_clase = c.id_clase
                      WHERE r.id_reserva = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    // Obtener reservas por vuelo
    public function obtenerPorVuelo($id_vuelo) {
        try {
            $query = "SELECT * FROM " . $this->table . "
                      WHERE id_vuelo = :id_vuelo
                      AND estado = 'confirmada'
                      ORDER BY numero_asiento ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_vuelo', $id_vuelo);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    // Obtener reservas por pasajero (número de cédula)
    public function obtenerPorPasajero($numero_cedula) {
        try {
            $query = "SELECT * FROM " . $this->table . "
                      WHERE numero_cedula = :numero_cedula
                      ORDER BY fecha_reserva DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':numero_cedula', $numero_cedula);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    // Crear nueva reserva
    public function crear() {
        try {
            // Generar número de reserva único
            $this->numero_reserva = 'RES-' . date('YmdHis') . rand(100, 999);

            $query = "INSERT INTO " . $this->table . "
                      (numero_reserva, id_vuelo, id_clase, nombre_pasajero, apellido_pasajero,
                       numero_cedula, email, telefono, numero_asiento, precio_total, estado)
                      VALUES (:numero_reserva, :id_vuelo, :id_clase, :nombre_pasajero, :apellido_pasajero,
                              :numero_cedula, :email, :telefono, :numero_asiento, :precio_total, :estado)";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':numero_reserva', $this->numero_reserva);
            $stmt->bindParam(':id_vuelo', $this->id_vuelo);
            $stmt->bindParam(':id_clase', $this->id_clase);
            $stmt->bindParam(':nombre_pasajero', $this->nombre_pasajero);
            $stmt->bindParam(':apellido_pasajero', $this->apellido_pasajero);
            $stmt->bindParam(':numero_cedula', $this->numero_cedula);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':telefono', $this->telefono);
            $stmt->bindParam(':numero_asiento', $this->numero_asiento);
            $stmt->bindParam(':precio_total', $this->precio_total);
            $stmt->bindParam(':estado', $this->estado);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Actualizar reserva
    public function actualizar() {
        try {
            $query = "UPDATE " . $this->table . "
                      SET id_vuelo = :id_vuelo,
                          id_clase = :id_clase,
                          nombre_pasajero = :nombre_pasajero,
                          apellido_pasajero = :apellido_pasajero,
                          numero_cedula = :numero_cedula,
                          email = :email,
                          telefono = :telefono,
                          numero_asiento = :numero_asiento,
                          precio_total = :precio_total,
                          estado = :estado
                      WHERE id_reserva = :id";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':id', $this->id_reserva);
            $stmt->bindParam(':id_vuelo', $this->id_vuelo);
            $stmt->bindParam(':id_clase', $this->id_clase);
            $stmt->bindParam(':nombre_pasajero', $this->nombre_pasajero);
            $stmt->bindParam(':apellido_pasajero', $this->apellido_pasajero);
            $stmt->bindParam(':numero_cedula', $this->numero_cedula);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':telefono', $this->telefono);
            $stmt->bindParam(':numero_asiento', $this->numero_asiento);
            $stmt->bindParam(':precio_total', $this->precio_total);
            $stmt->bindParam(':estado', $this->estado);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Eliminar reserva
    public function eliminar() {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id_reserva = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $this->id_reserva);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Cambiar estado de la reserva (confirmada, cancelada, pendiente)
    public function cambiarEstado($nuevoEstado) {
        try {
            $query = "UPDATE " . $this->table . "
                      SET estado = :estado
                      WHERE id_reserva = :id";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':estado', $nuevoEstado);
            $stmt->bindParam(':id', $this->id_reserva);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Verificar si un asiento específico está disponible para un vuelo
    public function asientoDisponible($id_vuelo, $numero_asiento) {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table . "
                      WHERE id_vuelo = :id_vuelo
                      AND numero_asiento = :numero_asiento
                      AND estado = 'confirmada'";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_vuelo', $id_vuelo);
            $stmt->bindParam(':numero_asiento', $numero_asiento);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] == 0;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
?>
