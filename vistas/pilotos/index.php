<?php
// 1. Incluimos tu modelo de Piloto y creamos una instancia para usar sus métodos
require_once '../../modelos/Piloto.php';
$pilotoModel = new Piloto();

// 2. Procesamos las acciones de los formularios (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {

        // --- CREAR PILOTO ---
        if ($_POST['accion'] === 'crear') {
            $pilotoModel->nombre = $_POST['nombre'];
            $pilotoModel->apellido = $_POST['apellido'];
            $pilotoModel->numero_licencia = $_POST['numero_licencia'];
            $pilotoModel->horas_vuelo = $_POST['horas_vuelo'];
            $pilotoModel->especialidad = $_POST['especialidad'];
            $pilotoModel->estado = $_POST['estado'];
            // Nota: Si tu método para guardar se llama 'agregar' en vez de 'crear', cámbialo aquí abajo:
            $pilotoModel->crear();
        }

        // --- EDITAR PILOTO ---
        elseif ($_POST['accion'] === 'editar') {
            $pilotoModel->id_piloto = $_POST['id_piloto'];
            $pilotoModel->nombre = $_POST['nombre'];
            $pilotoModel->apellido = $_POST['apellido'];
            $pilotoModel->numero_licencia = $_POST['numero_licencia'];
            $pilotoModel->horas_vuelo = $_POST['horas_vuelo'];
            $pilotoModel->especialidad = $_POST['especialidad'];
            $pilotoModel->estado = $_POST['estado'];
            $pilotoModel->actualizar();
        }

        // --- ELIMINAR PILOTO ---
        elseif ($_POST['accion'] === 'eliminar') {
            $pilotoModel->id_piloto = $_POST['id_piloto'];
            $pilotoModel->eliminar();
        }

        // Recargamos la página para evitar reenvío de datos
        header("Location: index.php");
        exit();
    }
}

// 3. Obtenemos la lista usando tu PDO
$listaPilotos = $pilotoModel->obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pilotos - AeroControl</title>
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
                <i class="fa-solid fa-plane-departure me-2 text-primary"></i> Módulo de Pilotos
            </span>
            <div style="width: 85px;" class="d-none d-md-block"></div>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <div class="card shadow-sm card-modulo p-4 mb-5">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark m-0">Lista de Pilotos</h3>
                    <p class="text-muted small m-0">Administración de personal de vuelo según la DB.</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoPiloto">
                    <i class="fa-solid fa-plus me-2"></i>Registrar Piloto
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Piloto</th>
                            <th>Licencia</th>
                            <th>Especialidad</th>
                            <th>Horas</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($listaPilotos)): ?>
                            <?php foreach ($listaPilotos as $piloto): ?>
                                <tr>
                                    <td><?= $piloto['id_piloto'] ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($piloto['nombre'] . ' ' . $piloto['apellido']) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($piloto['numero_licencia']) ?></span></td>
                                    <td><?= htmlspecialchars($piloto['especialidad']) ?></td>
                                    <td><?= $piloto['horas_vuelo'] ?> hrs</td>
                                    <td>
                                        <?php if ($piloto['estado'] == 'Activo'): ?>
                                            <span class="badge bg-success rounded-pill">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger rounded-pill">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-warning rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#modalEditarPiloto<?= $piloto['id_piloto'] ?>">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger rounded-circle" data-bs-toggle="modal" data-bs-target="#modalEliminarPiloto<?= $piloto['id_piloto'] ?>">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalEditarPiloto<?= $piloto['id_piloto'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-pen me-2"></i>Editar Piloto</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="" method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="accion" value="editar">
                                                    <input type="hidden" name="id_piloto" value="<?= $piloto['id_piloto'] ?>">

                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label small">Nombre</label>
                                                            <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($piloto['nombre']) ?>" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label small">Apellido</label>
                                                            <input type="text" class="form-control" name="apellido" value="<?= htmlspecialchars($piloto['apellido']) ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small">Número de Licencia</label>
                                                        <input type="text" class="form-control" name="numero_licencia" value="<?= htmlspecialchars($piloto['numero_licencia']) ?>" required>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label small">Horas de Vuelo</label>
                                                            <input type="number" class="form-control" name="horas_vuelo" value="<?= $piloto['horas_vuelo'] ?>" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label small">Especialidad</label>
                                                            <input type="text" class="form-control" name="especialidad" value="<?= htmlspecialchars($piloto['especialidad']) ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small">Estado</label>
                                                        <select class="form-select" name="estado" required>
                                                            <option value="Activo" <?= ($piloto['estado'] == 'Activo') ? 'selected' : '' ?>>Activo</option>
                                                            <option value="Inactivo" <?= ($piloto['estado'] == 'Inactivo') ? 'selected' : '' ?>>Inactivo</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-warning fw-bold text-dark">Actualizar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="modalEliminarPiloto<?= $piloto['id_piloto'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Confirmar</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center py-4">
                                                <h4 class="fw-bold">¿Eliminar a <?= htmlspecialchars($piloto['nombre']) ?>?</h4>
                                                <p class="text-danger small mt-2">Esta acción no se puede deshacer.</p>
                                            </div>
                                            <div class="modal-footer bg-light justify-content-center">
                                                <form action="" method="POST" class="m-0">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="id_piloto" value="<?= $piloto['id_piloto'] ?>">
                                                    <button type="button" class="btn btn-secondary px-4 me-2" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger fw-bold px-4">Eliminar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No hay pilotos registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNuevoPiloto" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus me-2"></i>Nuevo Piloto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="crear">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small">Nombre</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small">Apellido</label>
                                <input type="text" class="form-control" name="apellido" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Número de Licencia</label>
                            <input type="text" class="form-control" name="numero_licencia" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small">Horas de Vuelo</label>
                                <input type="number" class="form-control" name="horas_vuelo" value="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small">Especialidad</label>
                                <input type="text" class="form-control" name="especialidad" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Estado</label>
                            <select class="form-select" name="estado" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-bold">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>