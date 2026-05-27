<?php
require_once '../../config/conexion.php';

$database = new Database();
$db = $database->getConnection();

$mensaje = "";
$tipo_mensaje = "";

// Procesar Registro de Vuelo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_guardar'])) {
    $num_vuelo = trim($_POST['numero_vuelo']);
    $avion_id = !empty($_POST['avion_id']) ? intval($_POST['avion_id']) : null;
    $piloto_id = !empty($_POST['piloto_id']) ? intval($_POST['piloto_id']) : null;
    $origen_id = !empty($_POST['origen_id']) ? intval($_POST['origen_id']) : null;
    $destino_id = !empty($_POST['destino_id']) ? intval($_POST['destino_id']) : null;
    $fecha_hora = $_POST['fecha_hora'];
    $precio_base = floatval($_POST['precio_base']);

    if ($origen_id === $destino_id) {
        $mensaje = "El aeropuerto de origen no puede ser igual al de destino.";
        $tipo_mensaje = "danger";
    } else {
        try {
            $sql = "INSERT INTO vuelos (numero_vuelo, avion_id, piloto_id, origen_id, destino_id, fecha_hora, precio_base) 
                    VALUES (:num, :avion, :piloto, :origen, :destino, :fecha, :precio)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':num', $num_vuelo);
            $stmt->bindParam(':avion', $avion_id, PDO::PARAM_INT);
            $stmt->bindParam(':piloto', $piloto_id, PDO::PARAM_INT);
            $stmt->bindParam(':origen', $origen_id, PDO::PARAM_INT);
            $stmt->bindParam(':destino', $destino_id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha', $fecha_hora);
            $stmt->bindParam(':precio', $precio_base);

            if ($stmt->execute()) {
                $mensaje = "Vuelo programado exitosamente.";
                $tipo_mensaje = "success";
            }
        } catch (PDOException $e) {
            $mensaje = "Error al programar itinerario: El número de vuelo ya existe.";
            $tipo_mensaje = "danger";
        }
    }
}

// Cargar catálogos dinámicos para los Selects del Formulario
$aviones = $db->query("SELECT id, modelo FROM aviones")->fetchAll();
$pilotos = $db->query("SELECT id, nombre FROM pilotos")->fetchAll();
// Inyectar destinos semilla si la tabla está vacía para asegurar el correcto despliegue del proyecto
$db->query("INSERT IGNORE INTO destinos (ciudad, pais, codigo_aeropuerto) VALUES 
('CDMX', 'México', 'MEX'), ('Cancún', 'México', 'CUN'), ('Madrid', 'España', 'MAD')");
$destinos = $db->query("SELECT id, ciudad, codigo_aeropuerto FROM destinos")->fetchAll();

// Consulta relacional con JOINs complejos para la visualización del usuario final
$queryBitacora = "SELECT v.id, v.numero_vuelo, v.fecha_hora, v.precio_base, v.estado,
                  a.modelo AS avion, p.nombre AS piloto, 
                  d1.ciudad AS origen, d1.codigo_aeropuerto AS cod_origen,
                  d2.ciudad AS destino, d2.codigo_aeropuerto AS cod_destino
                  FROM vuelos v
                  LEFT JOIN aviones a ON v.avion_id = a.id
                  LEFT JOIN pilotos p ON v.piloto_id = p.id
                  LEFT JOIN destinos d1 ON v.origen_id = d1.id
                  LEFT JOIN destinos d2 ON v.destino_id = d2.id
                  ORDER BY v.fecha_hora ASC";
$bitacora = $db->query($queryBitacora)->fetchAll();

require_once '../../includes/header.php';
?>

<div class="row">
    <div class="col-md-4">
        <div class="card card-custom bg-white border p-4 mb-4">
            <h4 class="fw-bold text-dark mb-3"><i class="fa-solid fa-route text-primary me-2"></i>Programar Vuelo</h4>
            <hr>
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show"><?php echo $mensaje; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="mb-2"><label class="form-label small fw-bold">No. Vuelo</label><input type="text" class="form-control" name="numero_vuelo" placeholder="Ej. AM-243" required></div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Asignar Avión</label>
                    <select class="form-select" name="avion_id" required>
                        <option value="">Seleccione aeronave...</option>
                        <?php foreach ($aviones as $av): ?><option value="<?php echo $av['id']; ?>"><?php echo $av['modelo']; ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Asignar Piloto</label>
                    <select class="form-select" name="piloto_id" required>
                        <option value="">Seleccione capitán...</option>
                        <?php foreach ($pilotos as $pi): ?><option value="<?php echo $pi['id']; ?>"><?php echo $pi['nombre']; ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Origen</label>
                        <select class="form-select" name="origen_id" required>
                            <?php foreach ($destinos as $de): ?><option value="<?php echo $de['id']; ?>"><?php echo $de['ciudad']; ?> (<?php echo $de['codigo_aeropuerto']; ?>)</option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Destino</label>
                        <select class="form-select" name="destino_id" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($destinos as $de): ?><option value="<?php echo $de['id']; ?>"><?php echo $de['ciudad']; ?> (<?php echo $de['codigo_aeropuerto']; ?>)</option><?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-2"><label class="form-label small fw-bold">Fecha y Hora</label><input type="datetime-local" class="form-control" name="fecha_hora" required></div>
                <div class="mb-3"><label class="form-label small fw-bold">Precio Base ($)</label><input type="number" step="0.01" class="form-control" name="precio_base" placeholder="0.00" required></div>
                <button type="submit" name="btn_guardar" class="btn btn-primary w-100 fw-bold">Publicar Vuelo</button>
            </form>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card card-custom bg-white border p-4">
            <h4 class="fw-bold text-dark mb-3"><i class="fa-solid fa-plane-slat text-primary me-2"></i>Bitácora de Itinerarios</h4>
            <hr>
            <div class="table-responsive">
                <table class="table table-hover align-middle" style="font-size: 0.9rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Vuelo</th>
                            <th>Ruta</th>
                            <th>Aeronave / Piloto</th>
                            <th>Salida</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($bitacora) > 0): foreach ($bitacora as $v): ?>
                                <tr>
                                    <td><span class="badge bg-dark fw-bold"><?php echo $v['numero_vuelo']; ?></span></td>
                                    <td class="fw-semibold text-secondary"><?php echo $v['origen']; ?> (<?php echo $v['cod_origen']; ?>) <i class="fa-solid fa-arrow-right mx-1 text-muted"></i> <?php echo $v['destino']; ?> (<?php echo $v['cod_destino']; ?>)</td>
                                    <td><small class="d-block text-dark font-monospace"><i class="fa-solid fa-plane me-1"></i><?php echo $v['avion'] ?? 'No asignado'; ?></small><small class="d-block text-muted"><i class="fa-solid fa-user-tie me-1"></i><?php echo $v['piloto'] ?? 'No asignado'; ?></small></td>
                                    <td><small class="fw-bold"><?php echo date('d/m/Y H:i', strtotime($v['fecha_hora'])); ?></small></td>
                                    <td class="fw-bold text-success">$<?php echo number_format($v['precio_base'], 2); ?></td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No existen vuelos programados en el sistema actual.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once '../../includes/footer.php'; ?>