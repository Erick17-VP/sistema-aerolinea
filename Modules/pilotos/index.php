<?php
require_once '../../config/conexion.php';

$database = new Database();
$db = $database->getConnection();

$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_guardar'])) {
    $nombre = trim($_POST['nombre']);
    $licencia = trim($_POST['licencia']);

    if (!empty($nombre) && !empty($licencia)) {
        try {
            $sql = "INSERT INTO pilotos (nombre, licencia) VALUES (:nombre, :licencia)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':licencia', $licencia, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $mensaje = "<i class='fa-solid fa-circle-check me-2'></i>Piloto registrado con éxito.";
                $tipo_mensaje = "success";
            }
        } catch (PDOException $e) {
            $mensaje = "La licencia ya existe o hubo un error en el sistema.";
            $tipo_mensaje = "danger";
        }
    } else {
        $mensaje = "Todos los campos son obligatorios.";
        $tipo_mensaje = "warning";
    }
}

$listaPilotos = $db->query("SELECT * FROM pilotos ORDER BY id DESC")->fetchAll();
require_once '../../includes/header.php';
?>

<div class="row">
    <div class="col-md-4">
        <div class="card card-custom bg-white border p-4 mb-4">
            <h4 class="fw-bold text-dark mb-3"><i class="fa-solid fa-user-plus text-primary me-2"></i>Registrar Piloto</h4>
            <hr>
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensaje; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted">Nombre Completo</label>
                    <input type="text" class="form-control" name="nombre" placeholder="Ej. Cap. Carlos Arena" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold text-muted">Número de Licencia</label>
                    <input type="text" class="form-control" name="licencia" placeholder="Ej. LIC-98745-MX" required>
                </div>
                <button type="submit" name="btn_guardar" class="btn btn-primary w-100 fw-bold shadow-sm">Guardar Piloto</button>
            </form>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card card-custom bg-white border p-4">
            <h4 class="fw-bold text-dark mb-3"><i class="fa-solid fa-id-badge text-primary me-2"></i>Pilotos Autorizados</h4>
            <hr>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">ID</th>
                            <th>Nombre del Piloto</th>
                            <th>Licencia Federal</th>
                            <th class="text-center">Estatus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($listaPilotos) > 0): foreach ($listaPilotos as $p): ?>
                                <tr>
                                    <td class="fw-bold text-primary px-3">#<?php echo $p['id']; ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($p['nombre']); ?></td>
                                    <td><code class="text-dark bg-light px-2 py-1 rounded"><?php echo htmlspecialchars($p['licencia']); ?></code></td>
                                    <td class="text-center"><span class="badge bg-success-subtle text-success px-2 py-2">Vigente</span></td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No hay pilotos registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once '../../includes/footer.php'; ?>