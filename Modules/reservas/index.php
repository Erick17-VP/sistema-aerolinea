<?php
require_once '../../config/conexion.php';

$database = new Database();
$db = $database->getConnection();

$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_reservar'])) {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $vuelo_id = intval($_POST['vuelo_id']);
    $asiento = trim($_POST['asiento']);
    $clase_id = !empty($_POST['clase_id']) ? intval($_POST['clase_id']) : null;

    if (!empty($nombre) && !empty($email) && $vuelo_id > 0 && !empty($asiento)) {
        try {
            // El motor de base de datos abre un proceso seguro
            $db->beginTransaction();

            // 1. Inserción o rescate del pasajero
            $sqlPasajero = "INSERT INTO pasajeros (nombre, email, telefono) VALUES (:nom, :email, :tel) 
                            ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id), telefono=:tel";
            $stmtP = $db->prepare($sqlPasajero);
            $stmtP->bindParam(':nom', $nombre);
            $stmtP->bindParam(':email', $email);
            $stmtP->bindParam(':tel', $telefono);
            $stmtP->execute();

            $pasajero_id = $db->lastInsertId();

            // 2. Obtener precio base del vuelo seleccionado para calcular costos
            $stmtV = $db->prepare("SELECT precio_base FROM vuelos WHERE id = :vid");
            $stmtV->bindParam(':vid', $vuelo_id, PDO::PARAM_INT);
            $stmtV->execute();
            $precio_base = $stmtV->fetchColumn();

            // Factor multiplicador por defecto para el cálculo del boleto
            $factor = 1.00;
            if ($clase_id) {
                $stmtC = $db->prepare("SELECT factor_precio FROM clases_vuelo WHERE id = :cid");
                $stmtC->bindParam(':cid', $clase_id, PDO::PARAM_INT);
                $stmtC->execute();
                $factor = $stmtC->fetchColumn() ?: 1.00;
            }
            $precio_final = $precio_base * $factor;

            // 3. Crear el boleto (Reserva) vinculando las llaves foráneas correspondientes
            $sqlReserva = "INSERT INTO reservas (vuelo_id, pasajero_id, clase_id, asiento, precio_final) 
                           VALUES (:vid, :pid, :cid, :asiento, :pfinal)";
            $stmtR = $db->prepare($sqlReserva);
            $stmtR->bindParam(':vid', $vuelo_id, PDO::PARAM_INT);
            $stmtR->bindParam(':pid', $pasajero_id, PDO::PARAM_INT);
            $stmtR->bindParam(':cid', $clase_id, PDO::PARAM_INT);
            $stmtR->bindParam(':asiento', $asiento);
            $stmtR->bindParam(':pfinal', $precio_final);
            $stmtR->execute();

            // Guardar cambios definitivos
            $db->commit();
            $mensaje = "¡Reservación y boleto expedidos con éxito!";
            $tipo_mensaje = "success";
        } catch (PDOException $e) {
            $db->rollBack();
            $mensaje = "Error de procesamiento: El asiento solicitado ya se encuentra ocupado para este vuelo.";
            $tipo_mensaje = "danger";
        }
    } else {
        $mensaje = "Complete los datos mínimos requeridos.";
        $tipo_mensaje = "warning";
    }
}

// Poblar clases semilla de manera automatizada si no existen registros previos
$db->query("INSERT IGNORE INTO clases_vuelo (id, nombre, factor_precio) VALUES (1, 'Turista', 1.00), (2, 'Ejecutiva', 1.50)");
$clases = $db->query("SELECT * FROM clases_vuelo")->fetchAll();
$vuelosDisponibles = $db->query("SELECT v.id, v.numero_vuelo, d.ciudad FROM vuelos v JOIN destinos d ON v.destino_id = d.id")->fetchAll();

// Listado global relacional de boletos emitidos
$queryBoletos = "SELECT r.id, r.asiento, r.precio_final, p.nombre AS pasajero, v.numero_vuelo AS vuelo, c.nombre AS clase
                 FROM reservas r
                 JOIN pasajeros p ON r.pasajero_id = p.id
                 JOIN vuelos v ON r.vuelo_id = v.id
                 LEFT JOIN clases_vuelo c ON r.clase_id = c.id
                 ORDER BY r.id DESC";
$boletos = $db->query($queryBoletos)->fetchAll();

require_once '../../includes/header.php';
?>

<div class="row">
    <div class="col-md-4">
        <div class="card card-custom bg-white border p-4 mb-4">
            <h4 class="fw-bold text-dark mb-3"><i class="fa-solid fa-passport text-primary me-2"></i>Despacho de Boletos</h4>
            <hr>
            <?php if (!empty($mensaje)): ?><div class="alert alert-<?php echo $tipo_mensaje; ?>"><?php echo $mensaje; ?></div><?php endif; ?>
            <form action="" method="POST">
                <div class="mb-2"><label class="form-label small fw-bold">Pasajero</label><input type="text" class="form-control form-control-sm" name="nombre" placeholder="Nombre completo" required></div>
                <div class="mb-2"><label class="form-label small fw-bold">E-mail corporativo</label><input type="email" class="form-control form-control-sm" name="email" placeholder="correo@servidor.com" required></div>
                <div class="mb-2"><label class="form-label small fw-bold">Teléfono móvil</label><input type="text" class="form-control form-control-sm" name="telefono" placeholder="10 dígitos"></div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Seleccionar Vuelo</label>
                    <select class="form-select form-select-sm" name="vuelo_id" required>
                        <option value="">Destinos disponibles...</option>
                        <?php foreach ($vuelosDisponibles as $vd): ?><option value="<?php echo $vd['id']; ?>"><?php echo $vd['numero_vuelo']; ?> - Destino: <?php echo $vd['ciudad']; ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6"><label class="form-label small fw-bold">Asiento</label><input type="text" class="form-control form-control-sm" name="asiento" placeholder="Ej. 12A" required></div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Clase</label>
                        <select class="form-select form-select-sm" name="clase_id">
                            <option value="">Seleccione...</option><?php foreach ($clases as $cl): ?><option value="<?php echo $cl['id']; ?>"><?php echo $cl['nombre']; ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" name="btn_reservar" class="btn btn-success w-100 fw-bold shadow-sm"><i class="fa-solid fa-print me-2"></i>Emitir Reservación</button>
            </form>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card card-custom bg-white border p-4">
            <h4 class="fw-bold text-dark mb-3"><i class="fa-solid fa-receipt text-primary me-2"></i>Manifiesto de Pasajeros y Boletos</h4>
            <hr>
            <div class="table-responsive">
                <table class="table table-hover align-middle" style="font-size: 0.9rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Folio</th>
                            <th>Nombre Pasajero</th>
                            <th>Vuelo / Asiento</th>
                            <th>Clase asignada</th>
                            <th>Tarifa Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($boletos) > 0): foreach ($boletos as $b): ?>
                                <tr>
                                    <td class="fw-bold text-success">FL-<?php echo sprintf("%04d", $b['id']); ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($b['pasajero']); ?></td>
                                    <td><span class="badge bg-dark me-1"><?php echo $b['vuelo']; ?></span><span class="badge bg-secondary"><?php echo $b['asiento']; ?></span></td>
                                    <td><span class="text-uppercase small fw-bold text-muted"><?php echo $b['clase'] ?? 'Turista'; ?></span></td>
                                    <td class="fw-bold text-dark">$<?php echo number_format($b['precio_final'], 2); ?></td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No se registran boletos emitidos el día de hoy.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once '../../includes/footer.php'; ?>