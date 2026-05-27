<?php
// 1. Incluimos el modelo de Aviones (Ajusta la ruta si es necesario)
require_once '../../modelos/Avion.php';
$avionModel = new Avion();

// 2. Procesamos las acciones si se envía información por el formulario (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {

        // --- ACCIÓN: CREAR ---
        if ($_POST['accion'] === 'crear') {
            $avionModel->numero_matricula = $_POST['numero_matricula'];
            $avionModel->modelo = $_POST['modelo'];
            $avionModel->capacidad = $_POST['capacidad'];
            $avionModel->año_fabricacion = $_POST['anio_fabricacion'];
            $avionModel->ultimo_mantenimiento = $_POST['ultimo_mantenimiento'];
            $avionModel->estado = $_POST['estado'];
            // Asegúrate de que el método para insertar en tu clase Avion se llame 'crear' o 'agregar'
            $avionModel->crear();
        }

        // --- ACCIÓN: EDITAR ---
        elseif ($_POST['accion'] === 'editar') {
            $avionModel->id_avion = $_POST['id_avion'];
            $avionModel->numero_matricula = $_POST['numero_matricula'];
            $avionModel->modelo = $_POST['modelo'];
            $avionModel->capacidad = $_POST['capacidad'];
            $avionModel->año_fabricacion = $_POST['anio_fabricacion'];
            $avionModel->ultimo_mantenimiento = $_POST['ultimo_mantenimiento'];
            $avionModel->estado = $_POST['estado'];
            $avionModel->actualizar();
        }

        // --- ACCIÓN: ELIMINAR ---
        elseif ($_POST['accion'] === 'eliminar') {
            $avionModel->id_avion = $_POST['id_avion'];
            $avionModel->eliminar();
        }

        // Recargamos la página limpia para que se apliquen los cambios en la tabla
        header("Location: index.php");
        exit();
    }
}

// 3. Obtenemos la lista de aviones de la base de datos
$listaAviones = $avionModel->obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Aviones - AeroControl</title>
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
                <i class="fa-solid fa-plane me-2 text-primary"></i> Módulo Administrativo de Aviones
            </span>
            <div style="width: 85px;" class="d-none d-md-block"></div>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <div class="card shadow-sm card-modulo p-4 mb-5">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark m-0">Listado de Aeronaves</h3>
                    <p class="text-muted small m-0">Control de la flota aérea registrada en base de datos.</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoAvion">
                    <i class="fa-solid fa-plus me-2"></i>Registrar Aeronave
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Matrícula</th>
                            <th>Modelo</th>
                            <th>Capacidad</th>
                            <th>Año Fab.</th>
                            <th>Último Mant.</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($listaAviones)): ?>
                            <?php foreach ($listaAviones as $avion): ?>
                                <tr>
                                    <td><?= $avion['id_avion'] ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($avion['numero_matricula']) ?></span></td>
                                    <td class="fw-bold"><?= htmlspecialchars($avion['modelo']) ?></td>
                                    <td><?= $avion['capacidad'] ?> pax</td>
                                    <td><?= $avion['año_fabricacion'] ?></td>
                                    <td><?= date("d/m/Y", strtotime($avion['ultimo_mantenimiento'])) ?></td>
                                    <td>
                                        <?php if ($avion['estado'] == 'Activo'): ?>
                                            <span class="badge bg-success rounded-pill">Activo</span>
                                        <?php elseif ($avion['estado'] == 'En Mantenimiento'): ?>
                                            <span class="badge bg-warning text-dark rounded-pill">En Mantenimiento</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger rounded-pill"><?= htmlspecialchars($avion['estado']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-warning rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#modalEditarAvion<?= $avion['id_avion'] ?>">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>

                                        <button class="btn btn-sm btn-outline-danger rounded-circle" data-bs-toggle="modal" data-bs-target="#modalEliminarAvion<?= $avion['id_avion'] ?>">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalEditarAvion<?= $avion['id_avion'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title text-dark fw-bold"><i class="fa-solid fa-pen me-2"></i>Editar Aeronave</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="" method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="accion" value="editar">
                                                    <input type="hidden" name="id_avion" value="<?= $avion['id_avion'] ?>">

                                                    <div class="mb-3">
                                                        <label class="form-label text-muted small">Número de Matrícula</label>
                                                        <input type="text" class="form-control" name="numero_matricula" value="<?= htmlspecialchars($avion['numero_matricula']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label text-muted small">Modelo</label>
                                                        <input type="text" class="form-control" name="modelo" value="<?= htmlspecialchars($avion['modelo']) ?>" required>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label text-muted small">Capacidad</label>
                                                            <input type="number" class="form-control" name="capacidad" value="<?= $avion['capacidad'] ?>" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label text-muted small">Año Fabricación</label>
                                                            <input type="number" class="form-control" name="anio_fabricacion" value="<?= $avion['año_fabricacion'] ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label text-muted small">Último Mantenimiento</label>
                                                        <input type="date" class="form-control" name="ultimo_mantenimiento" value="<?= $avion['ultimo_mantenimiento'] ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label text-muted small">Estado</label>
                                                        <select class="form-select" name="estado" required>
                                                            <option value="Activo" <?= ($avion['estado'] == 'Activo') ? 'selected' : '' ?>>Activo</option>
                                                            <option value="En Mantenimiento" <?= ($avion['estado'] == 'En Mantenimiento') ? 'selected' : '' ?>>En Mantenimiento</option>
                                                            <option value="Inactivo" <?= ($avion['estado'] == 'Inactivo') ? 'selected' : '' ?>>Inactivo</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-warning text-dark fw-bold">Guardar Cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="modalEliminarAvion<?= $avion['id_avion'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Confirmar Eliminación</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center py-4">
                                                <i class="fa-solid fa-trash-can text-danger mb-3" style="font-size: 4rem;"></i>
                                                <h4 class="fw-bold">¿Estás seguro?</h4>
                                                <p class="text-muted mb-1">Vas a eliminar el avión con matrícula <strong><?= htmlspecialchars($avion['numero_matricula']) ?></strong> (Modelo: <?= htmlspecialchars($avion['modelo']) ?>).</p>
                                                <p class="text-danger small fw-bold mt-2">Esta acción es permanente y no se puede deshacer.</p>
                                            </div>
                                            <div class="modal-footer bg-light justify-content-center">
                                                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                                                <form action="" method="POST" class="m-0">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="id_avion" value="<?= $avion['id_avion'] ?>">
                                                    <button type="submit" class="btn btn-danger fw-bold px-4">Sí, Eliminar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-plane-slash fs-1 d-block mb-3"></i>
                                    No hay aviones registrados en el sistema.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNuevoAvion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus me-2"></i>Registrar Aeronave</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="crear">

                        <div class="mb-3">
                            <label class="form-label text-muted small">Número de Matrícula</label>
                            <input type="text" class="form-control" name="numero_matricula" placeholder="Ej: XA-AER" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Modelo de Aeronave</label>
                            <input type="text" class="form-control" name="modelo" placeholder="Ej: Boeing 737" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small">Capacidad (Pasajeros)</label>
                                <input type="number" class="form-control" name="capacidad" placeholder="Ej: 180" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small">Año Fabricación</label>
                                <input type="number" class="form-control" name="anio_fabricacion" placeholder="Ej: 2020" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Fecha Último Mantenimiento</label>
                            <input type="date" class="form-control" name="ultimo_mantenimiento" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Estado Inicial</label>
                            <select class="form-select" name="estado" required>
                                <option value="Activo">Activo</option>
                                <option value="En Mantenimiento">En Mantenimiento</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-bold">Guardar Avión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>