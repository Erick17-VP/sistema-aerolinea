<?php
// modules/aviones/index.php
require_once '../../config/conexion.php';

$database = new Database();
$db = $database->getConnection();

$mensaje = "";
$tipo_mensaje = "";

// 1. LÓGICA DE PROGRAMACIÓN CONTROLADORA: Captura y proceso de formulario mediante POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_guardar'])) {
    $modelo = trim($_POST['modelo']);
    $capacidad = intval($_POST['capacidad']);

    if (!empty($modelo) && $capacidad > 0) {
        try {
            // Sentencia SQL preparada para evitar inyecciones de código malicioso
            $sql = "INSERT INTO aviones (modelo, capacidad) VALUES (:modelo, :capacidad)";
            $stmt = $db->prepare($sql);

            // Vinculación explícita de parámetros sanitizados
            $stmt->bindParam(':modelo', $modelo, PDO::PARAM_STR);
            $stmt->bindParam(':capacidad', $capacidad, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $mensaje = "<i class='fa-solid fa-circle-check me-2'></i>Aeronave registrada de forma exitosa en el inventario.";
                $tipo_mensaje = "success";
            }
        } catch (PDOException $e) {
            $mensaje = "Error operativo en la base de datos: " . $e->getMessage();
            $tipo_mensaje = "danger";
        }
    } else {
        $mensaje = "Validación fallida: Todos los campos son obligatorios y la capacidad debe ser mayor a cero.";
        $tipo_mensaje = "warning";
    }
}

// 2. CONSULTA DE DATOS: Lectura del listado actual de la tabla aviones
$queryAviones = "SELECT * FROM aviones ORDER BY id DESC";
$stmtAviones = $db->query($queryAviones);
$listaAviones = $stmtAviones->fetchAll();

require_once '../../includes/header.php';
?>

<div class="row">
    <div class="col-md-4">
        <div class="card card-custom bg-white border p-4 mb-4">
            <h4 class="fw-bold text-dark mb-3"><i class="fa-solid fa-circle-plus text-primary me-2"></i>Registrar Avión</h4>
            <hr>

            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label for="modelo" class="form-label fw-semibold text-muted">Modelo de la Aeronave</label>
                    <input type="text" class="form-control" id="modelo" name="modelo" placeholder="Ej. Boeing 737-800" required>
                </div>
                <div class="mb-4">
                    <label for="capacidad" class="form-label fw-semibold text-muted">Capacidad de Pasajeros</label>
                    <input type="number" class="form-control" id="capacidad" name="capacidad" min="1" placeholder="Ej. 180" required>
                </div>
                <button type="submit" name="btn_guardar" class="btn btn-primary w-100 fw-bold shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Guardar en Flota
                </button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-custom bg-white border p-4">
            <h4 class="fw-bold text-dark mb-3"><i class="fa-solid fa-list text-primary me-2"></i>Inventario de Flota Activa</h4>
            <hr>
            <div class="table-responsive">
                <table class="table table-hover align-middle border-start border-end">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-3">ID Interno</th>
                            <th class="py-3">Modelo de Avión</th>
                            <th class="py-3">Capacidad Máxima</th>
                            <th class="py-3 text-center">Estado Técnico</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($listaAviones) > 0): ?>
                            <?php foreach ($listaAviones as $avion): ?>
                                <tr>
                                    <td class="fw-bold text-primary px-3">#<?php echo $avion['id']; ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($avion['modelo']); ?></td>
                                    <td><span class="badge bg-secondary px-3 py-2 fs-7"><?php echo $avion['capacidad']; ?> asientos</span></td>
                                    <td class="text-center"><span class="badge bg-success-subtle text-success px-3 py-2"><i class="fa-solid fa-check-circle me-1"></i>Operativo</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-plane-slash fa-2x d-block mb-2"></i>No existen aeronaves dadas de alta en el inventario.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>