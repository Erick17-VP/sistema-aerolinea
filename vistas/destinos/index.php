<?php
// 1. Incluimos el modelo de Destino
require_once '../../modelos/Destino.php';

// Instanciamos el objeto
$destinoModel = new Destino();

// 2. Procesamiento de formularios (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {

        // --- CREAR DESTINO ---
        if ($_POST['accion'] === 'crear') {
            $destinoModel->nombre = $_POST['nombre'];
            $destinoModel->pais = $_POST['pais'];
            $destinoModel->codigo_aeropuerto = $_POST['codigo_aeropuerto'];
            $destinoModel->ciudad = $_POST['ciudad'];
            $destinoModel->crear();
        }

        // --- EDITAR DESTINO ---
        elseif ($_POST['accion'] === 'editar') {
            $destinoModel->id_destino = $_POST['id_destino'];
            $destinoModel->nombre = $_POST['nombre'];
            $destinoModel->pais = $_POST['pais'];
            $destinoModel->codigo_aeropuerto = $_POST['codigo_aeropuerto'];
            $destinoModel->ciudad = $_POST['ciudad'];
            $destinoModel->actualizar();
        }

        // --- ELIMINAR DESTINO ---
        elseif ($_POST['accion'] === 'eliminar') {
            $destinoModel->id_destino = $_POST['id_destino'];
            $destinoModel->eliminar();
        }

        // Redireccionamos para limpiar los datos del formulario
        header("Location: index.php");
        exit();
    }
}

// 3. Obtener la lista completa de destinos
$listaDestinos = $destinoModel->obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Destinos - AeroControl</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .card-modulo {
            border: none;
            border-radius: 15px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container-fluid px-4">
            <a href="../../index.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-2"></i>Inicio
            </a>
            <span class="navbar-brand mb-0 h1 mx-auto text-white">
                <i class="fa-solid fa-earth-americas text-info me-2"></i> Módulo de Destinos y Rutas
            </span>
            <div style="width: 85px;" class="d-none d-md-block"></div>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <div class="card shadow-sm card-modulo p-4 mb-5">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark m-0">Aeropuertos y Ciudades</h3>
                    <p class="text-muted small m-0">Catálogo de destinos nacionales e internacionales para vuelos.</p>
                </div>
                <button class="btn btn-info text-white fw-bold rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoDestino">
                    <i class="fa-solid fa-plus me-2"></i>Registrar Destino
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Código IATA</th>
                            <th>Nombre del Aeropuerto</th>
                            <th>Ciudad</th>
                            <th>País</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($listaDestinos)): ?>
                            <?php foreach ($listaDestinos as $destino): ?>
                                <tr>
                                    <td><?= $destino['id_destino'] ?></td>
                                    <td>
                                        <span class="badge bg-primary px-2 py-1 fs-6 shadow-sm border border-primary">
                                            <?= htmlspecialchars($destino['codigo_aeropuerto']) ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold text-dark">
                                        <i class="fa-solid fa-location-dot text-danger me-2 small"></i>
                                        <?= htmlspecialchars($destino['nombre']) ?>
                                    </td>
                                    <td><?= htmlspecialchars($destino['ciudad']) ?></td>
                                    <td><?= htmlspecialchars($destino['pais']) ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-warning rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#modalEditarDestino<?= $destino['id_destino'] ?>">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger rounded-circle" data-bs-toggle="modal" data-bs-target="#modalEliminarDestino<?= $destino['id_destino'] ?>">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalEliminarDestino<?= $destino['id_destino'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Confirmar Eliminación</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center py-4">
                                                <h4 class="fw-bold">¿Eliminar Destino?</h4>
                                                <p class="text-muted mt-2">Vas a eliminar el aeropuerto <strong><?= htmlspecialchars($destino['nombre']) ?> (<?= htmlspecialchars($destino['codigo_aeropuerto']) ?>)</strong>.</p>
                                                <p class="text-danger small fw-bold">No podrás eliminarlo si existen vuelos programados usando este destino.</p>
                                            </div>
                                            <div class="modal-footer bg-light justify-content-center">
                                                <form action="" method="POST" class="m-0">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="id_destino" value="<?= $destino['id_destino'] ?>">
                                                    <button type="button" class="btn btn-secondary px-4 me-2" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger fw-bold px-4">Sí, Eliminar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="modalEditarDestino<?= $destino['id_destino'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-pen me-2"></i>Editar Destino</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="" method="POST">
                                                <div class="modal-body bg-light">
                                                    <input type="hidden" name="accion" value="editar">
                                                    <input type="hidden" name="id_destino" value="<?= $destino['id_destino'] ?>">

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Nombre del Aeropuerto</label>
                                                        <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($destino['nombre']) ?>" required>
                                                    </div>
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label class="form-label small fw-bold">Código IATA</label>
                                                            <input type="text" class="form-control text-uppercase" name="codigo_aeropuerto" value="<?= htmlspecialchars($destino['codigo_aeropuerto']) ?>" maxlength="10" required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label small fw-bold">Ciudad</label>
                                                            <input type="text" class="form-control" name="ciudad" value="<?= htmlspecialchars($destino['ciudad']) ?>" required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label small fw-bold">País</label>
                                                            <input type="text" class="form-control" name="pais" value="<?= htmlspecialchars($destino['pais']) ?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-white">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-warning fw-bold text-dark">Guardar Cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-map-location-dot fs-1 d-block mb-3 text-secondary"></i>
                                    No hay destinos registrados en la base de datos.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNuevoDestino" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus me-2"></i>Registrar Nuevo Destino</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body bg-light">
                        <input type="hidden" name="accion" value="crear">

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nombre del Aeropuerto</label>
                            <input type="text" class="form-control" name="nombre" placeholder="Ej: Aeropuerto Internacional Benito Juárez" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Código IATA</label>
                                <input type="text" class="form-control text-uppercase" name="codigo_aeropuerto" placeholder="Ej: MEX" maxlength="10" required>
                                <div class="form-text small">Máx 10 letras</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Ciudad</label>
                                <input type="text" class="form-control" name="ciudad" placeholder="Ej: CDMX" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">País</label>
                                <input type="text" class="form-control" name="pais" placeholder="Ej: México" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info fw-bold text-white">Guardar Destino</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>