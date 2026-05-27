<?php
// 1. Incluimos los modelos necesarios
require_once '../../modelos/Vuelo.php';
require_once '../../modelos/Avion.php';
require_once '../../modelos/Piloto.php';
require_once '../../modelos/Destino.php';

// Instanciamos los objetos
$vueloModel   = new Vuelo();
$avionModel   = new Avion();
$pilotoModel  = new Piloto();
$destinoModel = new Destino();

// 2. Procesamiento de formularios (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {

        // --- CREAR VUELO ---
        if ($_POST['accion'] === 'crear') {
            $vueloModel->numero_vuelo       = $_POST['numero_vuelo']; // Generado automáticamente
            $vueloModel->id_avion           = $_POST['id_avion'];
            $vueloModel->id_piloto          = $_POST['id_piloto'];
            $vueloModel->id_destino_origen  = $_POST['id_destino_origen'];
            $vueloModel->id_destino_destino = $_POST['id_destino_destino'];
            $vueloModel->fecha_salida       = $_POST['fecha_salida'];
            $vueloModel->hora_salida        = $_POST['hora_salida'];
            $vueloModel->fecha_llegada      = $_POST['fecha_llegada'];
            $vueloModel->hora_llegada       = $_POST['hora_llegada'];
            $vueloModel->precio_base        = $_POST['precio_base']; // Precio base del vuelo
            $vueloModel->estado             = 'Programado';

            $vueloModel->crear();
        }

        // --- ELIMINAR VUELO ---
        elseif ($_POST['accion'] === 'eliminar') {
            $vueloModel->id_vuelo = $_POST['id_vuelo'];
            $vueloModel->eliminar();
        }

        header("Location: index.php");
        exit();
    }
}

// 3. Obtener listas para llenar los selects y la tabla
$listaVuelos   = $vueloModel->obtenerTodos();
$listaAviones  = $avionModel->obtenerTodos();
$listaPilotos  = $pilotoModel->obtenerTodos();
$listaDestinos = $destinoModel->obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Vuelos - AeroControl</title>
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
                <i class="fa-solid fa-plane-up text-info me-2"></i> Control de Vuelos y Rutas
            </span>
            <div style="width: 85px;" class="d-none d-md-block"></div>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <div class="card shadow-sm card-modulo p-4 mb-5">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark m-0">Itinerario de Vuelos</h3>
                    <p class="text-muted small m-0">Programación de rutas, asignación de naves y precio base del vuelo.</p>
                </div>
                <button class="btn btn-info text-white fw-bold rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoVuelo" id="btnAbrirNuevoVuelo">
                    <i class="fa-solid fa-plus me-2"></i>Nuevo Vuelo
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>N° Vuelo</th>
                            <th>Ruta (Origen - Destino)</th>
                            <th>Fechas y Horas</th>
                            <th>Avión / Piloto</th>
                            <th>Precio Base</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($listaVuelos)): ?>
                            <?php foreach ($listaVuelos as $vuelo): ?>
                                <tr>
                                    <td><strong class="text-primary"><?= $vuelo['numero_vuelo'] ?></strong></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($vuelo['origen_nombre']) ?></div>
                                        <i class="fa-solid fa-arrow-down text-muted small my-1"></i>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($vuelo['destino_nombre']) ?></div>
                                    </td>
                                    <td class="small">
                                        <div class="text-success fw-bold"><i class="fa-solid fa-plane-departure me-1"></i> <?= $vuelo['fecha_salida'] ?> | <?= $vuelo['hora_salida'] ?></div>
                                        <div class="text-danger fw-bold"><i class="fa-solid fa-plane-arrival me-1"></i> <?= $vuelo['fecha_llegada'] ?> | <?= $vuelo['hora_llegada'] ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-dark d-block mb-1">Matrícula: <?= htmlspecialchars($vuelo['numero_matricula']) ?></span>
                                        <span class="small text-muted"><i class="fa-solid fa-user-tie me-1"></i> <?= htmlspecialchars($vuelo['piloto_nombre'] . ' ' . $vuelo['piloto_apellido']) ?></span>
                                    </td>
                                    <td class="fw-bold text-success">$<?= number_format($vuelo['precio_base'], 2) ?></td>
                                    <td>
                                        <?php if ($vuelo['estado'] == 'Programado'): ?>
                                            <span class="badge bg-primary">Programado</span>
                                        <?php elseif ($vuelo['estado'] == 'Activo'): ?>
                                            <span class="badge bg-success">En Curso</span>
                                        <?php elseif ($vuelo['estado'] == 'Completado'): ?>
                                            <span class="badge bg-secondary">Completado</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Cancelado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-secondary rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#modalEstado<?= $vuelo['id_vuelo'] ?>" title="Cambiar Estado">
                                            <i class="fa-solid fa-arrow-rotate-left"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger rounded-circle" data-bs-toggle="modal" data-bs-target="#modalEliminar<?= $vuelo['id_vuelo'] ?>">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalEstado<?= $vuelo['id_vuelo'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-secondary text-white">
                                                <h6 class="modal-title fw-bold">Cambiar Estado del Vuelo</h6>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="" method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="accion" value="editar_estado">
                                                    <input type="hidden" name="id_vuelo" value="<?= $vuelo['id_vuelo'] ?>">
                                                    <label class="form-label small fw-bold">Seleccione el nuevo estado:</label>
                                                    <select class="form-select" name="estado" required>
                                                        <option value="Programado" <?= $vuelo['estado'] == 'Programado' ? 'selected' : '' ?>>Programado</option>
                                                        <option value="Activo" <?= $vuelo['estado'] == 'Activo' ? 'selected' : '' ?>>En Curso (Activo)</option>
                                                        <option value="Completado" <?= $vuelo['estado'] == 'Completado' ? 'selected' : '' ?>>Completado</option>
                                                        <option value="Cancelado" <?= $vuelo['estado'] == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                                    </select>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">Actualizar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="modalEliminar<?= $vuelo['id_vuelo'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title fw-bold">Eliminar Vuelo</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center py-4">
                                                <p>¿Está seguro de que desea eliminar permanentemente el vuelo <strong><?= $vuelo['numero_vuelo'] ?></strong>?</p>
                                                <p class="small text-danger">Atención: Esto podría afectar las reservas asociadas a este vuelo.</p>
                                            </div>
                                            <div class="modal-footer bg-light justify-content-center">
                                                <form action="" method="POST" class="m-0">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="id_vuelo" value="<?= $vuelo['id_vuelo'] ?>">
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
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-plane-slash fs-1 d-block mb-3 text-secondary"></i>
                                    No hay vuelos programados en el sistema.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNuevoVuelo" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-plane-circle-check me-2"></i>Registrar Nuevo Vuelo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST" autocomplete="off" id="formNuevoVuelo">
                    <div class="modal-body bg-light">
                        <input type="hidden" name="accion" value="crear">

                        <div class="row g-3">
                            <div class="col-md-12 mb-2">
                                <label class="form-label small fw-bold text-primary">Número de Vuelo (Único y Automático)</label>
                                <input type="text" id="inputDisplayVuelo" class="form-control fw-bold border-primary text-center bg-white text-primary fs-5" readonly style="letter-spacing: 2px;">
                                <input type="hidden" id="inputHiddenVuelo" name="numero_vuelo">
                            </div>

                            <hr class="my-3 text-muted">
                            <h6 class="fw-bold m-0 text-secondary"><i class="fa-solid fa-users-gear me-2"></i>Asignación de Personal y Equipo</h6>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Avión Asignado</label>
                                <select class="form-select" id="selectAvion" name="id_avion" required>
                                    <option value="" disabled selected>Seleccione el avión...</option>
                                    <?php foreach ($listaAviones as $avion): ?>
                                        <option value="<?= $avion['id_avion'] ?>">
                                            Matrícula: <?= htmlspecialchars($avion['numero_matricula']) ?> - <?= htmlspecialchars($avion['modelo']) ?> (Cap: <?= $avion['capacidad'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Piloto a Cargo</label>
                                <select class="form-select" id="selectPiloto" name="id_piloto" required>
                                    <option value="" disabled selected>Seleccione el piloto...</option>
                                    <?php foreach ($listaPilotos as $piloto): ?>
                                        <option value="<?= $piloto['id_piloto'] ?>">
                                            <?= htmlspecialchars($piloto['nombre'] . ' ' . $piloto['apellido']) ?> (Lic: <?= htmlspecialchars($piloto['numero_licencia']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <hr class="my-3 text-muted">
                            <h6 class="fw-bold m-0 text-secondary"><i class="fa-solid fa-map-location-dot me-2"></i>Ruta del Vuelo</h6>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Origen (Salida)</label>
                                <select class="form-select" id="selectOrigen" name="id_destino_origen" required>
                                    <option value="" disabled selected>Seleccione ciudad de origen...</option>
                                    <?php foreach ($listaDestinos as $destino): ?>
                                        <option value="<?= $destino['id_destino'] ?>"><?= htmlspecialchars($destino['nombre']) ?> (<?= htmlspecialchars($destino['codigo_aeropuerto']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Destino (Llegada)</label>
                                <select class="form-select" id="selectDestino" name="id_destino_destino" required>
                                    <option value="" disabled selected>Seleccione ciudad de destino...</option>
                                    <?php foreach ($listaDestinos as $destino): ?>
                                        <option value="<?= $destino['id_destino'] ?>"><?= htmlspecialchars($destino['nombre']) ?> (<?= htmlspecialchars($destino['codigo_aeropuerto']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <hr class="my-3 text-muted">
                            <h6 class="fw-bold m-0 text-secondary"><i class="fa-solid fa-clock me-2"></i>Itinerario y Costos</h6>

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-success">Fecha Salida</label>
                                <input type="date" class="form-control border-success" id="inputFechaSalida" name="fecha_salida" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-success">Hora Salida</label>
                                <input type="time" class="form-control border-success" id="inputHoraSalida" name="hora_salida" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-danger">Fecha Llegada</label>
                                <input type="date" class="form-control border-danger" id="inputFechaLlegada" name="fecha_llegada" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-danger">Hora Llegada</label>
                                <input type="time" class="form-control border-danger" id="inputHoraLlegada" name="hora_llegada" required>
                            </div>

                            <div class="col-md-12 mt-4">
                                <div class="p-3 bg-dark text-white rounded-3 shadow-sm">
                                    <label class="form-label fw-bold m-0 fs-5 text-warning mb-2"><i class="fa-solid fa-tag me-2"></i>Precio Base del Vuelo ($):</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-success text-white border-0 fw-bold">$</span>
                                        <input type="number" step="0.01" min="0" class="form-control fw-bold text-success" id="inputPrecioBase" name="precio_base" placeholder="Ej: 250.00" autocomplete="new-password" required>
                                    </div>
                                    <small class="text-light opacity-75 mt-1 d-block">Este es el precio base del vuelo. El cargo adicional de la clase se suma al total en la reserva.</small>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer bg-white">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info fw-bold text-white shadow-sm">Guardar Vuelo Programado</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const btnAbrirNuevoVuelo = document.getElementById('btnAbrirNuevoVuelo');
            const form = document.getElementById('formNuevoVuelo');

            const inputDisplayVuelo = document.getElementById('inputDisplayVuelo');
            const inputHiddenVuelo = document.getElementById('inputHiddenVuelo');

            // 1. Función para generar un número de vuelo único
            function generarNumeroVuelo() {
                // Genera un código aleatorio estilo "VUE-8B3X9"
                const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                let aleatorio = '';
                for (let i = 0; i < 5; i++) {
                    aleatorio += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
                }
                const codigoFinal = 'VUE-' + aleatorio;

                // Lo mostramos en pantalla (bloqueado) y lo ponemos en el input oculto que se envía por POST
                inputDisplayVuelo.value = codigoFinal;
                inputHiddenVuelo.value = codigoFinal;
            }

            // 2. Limpieza total y auto-generación al hacer clic en "Nuevo Vuelo"
            btnAbrirNuevoVuelo.addEventListener('click', function() {
                // Reiniciar todos los campos nativos
                form.reset();

                // Forzar limpieza de selects por seguridad contra caché
                document.getElementById('selectAvion').selectedIndex = 0;
                document.getElementById('selectPiloto').selectedIndex = 0;
                document.getElementById('selectOrigen').selectedIndex = 0;
                document.getElementById('selectDestino').selectedIndex = 0;

                // Forzar limpieza de fechas y horas
                document.getElementById('inputFechaSalida').value = '';
                document.getElementById('inputHoraSalida').value = '';
                document.getElementById('inputFechaLlegada').value = '';
                document.getElementById('inputHoraLlegada').value = '';
                document.getElementById('inputPrecioBase').value = '';

                // Ejecutar la generación del código único
                generarNumeroVuelo();
            });
        });
    </script>
</body>

</html>