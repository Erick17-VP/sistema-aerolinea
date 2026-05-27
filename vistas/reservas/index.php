<?php
// 1. Incluimos los modelos necesarios
require_once '../../modelos/Reserva.php';
require_once '../../modelos/Vuelo.php';
require_once '../../modelos/ClaseVuelo.php';

// Instanciamos los objetos
$reservaModel = new Reserva();
$vueloModel = new Vuelo();
$claseModel = new ClaseVuelo();

// 2. Procesamiento de formularios (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {

        // --- CREAR RESERVA ---
        if ($_POST['accion'] === 'crear') {
            $reservaModel->numero_reserva    = $_POST['numero_reserva'];
            $reservaModel->id_vuelo          = $_POST['id_vuelo'];
            $reservaModel->id_clase          = $_POST['id_clase'];
            $reservaModel->nombre_pasajero   = $_POST['nombre_pasajero'];
            $reservaModel->apellido_pasajero = $_POST['apellido_pasajero'];
            $reservaModel->numero_cedula     = $_POST['numero_cedula'];
            $reservaModel->email             = $_POST['email'];
            $reservaModel->telefono          = $_POST['telefono'];
            $reservaModel->numero_asiento    = $_POST['numero_asiento'];
            $reservaModel->precio_total      = $_POST['precio_total'];
            $reservaModel->estado            = 'Confirmada';

            $reservaModel->crear();
        }

        // --- EDITAR ESTADO DE RESERVA ---
        elseif ($_POST['accion'] === 'editar_estado') {
            $reservaModel->id_reserva = $_POST['id_reserva'];
            $reservaModel->cambiarEstado($_POST['estado']);
        }

        // --- ELIMINAR RESERVA ---
        elseif ($_POST['accion'] === 'eliminar') {
            $reservaModel->id_reserva = $_POST['id_reserva'];
            $reservaModel->eliminar();
        }

        header("Location: index.php");
        exit();
    }
}

// 3. Obtener listas para llenar los selects y la tabla
$listaReservas = $reservaModel->obtenerTodas();
$listaVuelos   = $vueloModel->obtenerTodos();
$listaClases   = $claseModel->obtenerTodas();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reservas - AeroControl</title>
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
                <i class="fa-solid fa-ticket text-warning me-2"></i> Control de Reservaciones y Pasajeros
            </span>
            <div style="width: 85px;" class="d-none d-md-block"></div>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <div class="card shadow-sm card-modulo p-4 mb-5">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark m-0">Listado de Pasajeros Abordo</h3>
                    <p class="text-muted small m-0">Administración de tickets, asientos asignados y estados de pago.</p>
                </div>
                <button class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaReserva" id="btnAbrirNuevaReserva">
                    <i class="fa-solid fa-plus me-2"></i>Nueva Reservación
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Pasajero</th>
                            <th>Identificación</th>
                            <th>Vuelo / Ruta</th>
                            <th>Clase / Asiento</th>
                            <th>Total Pagado</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($listaReservas)): ?>
                            <?php foreach ($listaReservas as $reserva): ?>
                                <tr>
                                    <td><strong class="text-primary"><?= $reserva['numero_reserva'] ?></strong></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($reserva['nombre_pasajero'] . ' ' . $reserva['apellido_pasajero']) ?></div>
                                        <span class="text-muted small"><?= htmlspecialchars($reserva['email']) ?></span>
                                    </td>
                                    <td class="small text-secondary"><?= htmlspecialchars($reserva['numero_cedula']) ?></td>
                                    <td>
                                        <span class="badge bg-dark mb-1">Vuelo: <?= htmlspecialchars($reserva['numero_vuelo']) ?></span>
                                        <div class="small fw-bold text-truncate" style="max-width: 180px;">
                                            <?= htmlspecialchars($reserva['origen']) ?> <i class="fa-solid fa-arrow-right mx-1 text-muted small"></i> <?= htmlspecialchars($reserva['destino']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold"><?= htmlspecialchars($reserva['clase_nombre']) ?></div>
                                        <span class="badge bg-secondary">Asiento: <?= htmlspecialchars($reserva['numero_asiento']) ?></span>
                                    </td>
                                    <td class="fw-bold text-success">$<?= number_format($reserva['precio_total'], 2) ?></td>
                                    <td>
                                        <?php if ($reserva['estado'] == 'Confirmada'): ?>
                                            <span class="badge bg-success">Confirmada</span>
                                        <?php elseif ($reserva['estado'] == 'Pendiente'): ?>
                                            <span class="badge bg-warning text-dark">Pendiente</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Cancelada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-secondary rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#modalEstado<?= $reserva['id_reserva'] ?>">
                                            <i class="fa-solid fa-arrow-rotate-left"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger rounded-circle" data-bs-toggle="modal" data-bs-target="#modalEliminar<?= $reserva['id_reserva'] ?>">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalEstado<?= $reserva['id_reserva'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-secondary text-white">
                                                <h6 class="modal-title fw-bold">Cambiar Estado</h6>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="" method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="accion" value="editar_estado">
                                                    <input type="hidden" name="id_reserva" value="<?= $reserva['id_reserva'] ?>">
                                                    <label class="form-label small fw-bold">Seleccione el nuevo estado:</label>
                                                    <select class="form-select" name="estado" required>
                                                        <option value="Confirmada" <?= $reserva['estado'] == 'Confirmada' ? 'selected' : '' ?>>Confirmada</option>
                                                        <option value="Pendiente" <?= $reserva['estado'] == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                                        <option value="Cancelada" <?= $reserva['estado'] == 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
                                                    </select>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">Actualizar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="modalEliminar<?= $reserva['id_reserva'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title fw-bold">Eliminar Reservación</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center py-4">
                                                <p>¿Está seguro de que desea eliminar permanentemente la reserva <strong><?= $reserva['numero_reserva'] ?></strong>?</p>
                                            </div>
                                            <div class="modal-footer bg-light justify-content-center">
                                                <form action="" method="POST" class="m-0">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="id_reserva" value="<?= $reserva['id_reserva'] ?>">
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
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-ticket fs-1 d-block mb-3 text-secondary"></i>
                                    No hay reservas registradas en el sistema.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNuevaReserva" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-plane-departure me-2"></i>Registrar Nueva Reservación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST" autocomplete="off" id="formNuevaReserva">
                    <div class="modal-body bg-light">
                        <input type="hidden" name="accion" value="crear">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-primary">Código de Reserva (Automático)</label>
                                <input type="text" id="inputDisplayCodigo" class="form-control fw-bold border-primary text-center bg-white text-primary" readonly style="font-size: 1.1rem; letter-spacing: 1px;">
                                <input type="hidden" id="inputHiddenCodigo" name="numero_reserva">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Asiento Asignado</label>
                                <input type="text" class="form-control text-uppercase text-center fw-bold" id="inputAsiento" name="numero_asiento" placeholder="Ej: 12A" autocomplete="new-password" required>
                            </div>

                            <hr class="my-3 text-muted">
                            <h6 class="fw-bold m-0 text-secondary"><i class="fa-solid fa-user me-2"></i>Datos del Pasajero</h6>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Nombre(s)</label>
                                <input type="text" class="form-control" id="inputNombre" name="nombre_pasajero" placeholder="Nombre" autocomplete="new-password" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Apellido(s)</label>
                                <input type="text" class="form-control" id="inputApellido" name="apellido_pasajero" placeholder="Apellido" autocomplete="new-password" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">N° Identificación (DNI/Pasaporte)</label>
                                <input type="text" class="form-control text-uppercase" id="inputCedula" name="numero_cedula" placeholder="Número" autocomplete="new-password" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Correo Electrónico</label>
                                <input type="email" class="form-control" id="inputEmail" name="email" placeholder="correo@ejemplo.com" autocomplete="new-password" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Teléfono de Contacto</label>
                                <input type="tel" class="form-control" id="inputTelefono" name="telefono" placeholder="Número de telf" autocomplete="new-password">
                            </div>

                            <hr class="my-3 text-muted">
                            <h6 class="fw-bold m-0 text-secondary"><i class="fa-solid fa-dollar-sign me-2"></i>Vuelo, Clase y Tarifas</h6>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Seleccionar Vuelo Programado</label>
                                <select class="form-select" id="selectVuelo" name="id_vuelo" required>
                                    <option value="" data-precio="0" disabled selected>Elija el número de vuelo...</option>
                                    <?php foreach ($listaVuelos as $vuelo): ?>
                                        <option value="<?= $vuelo['id_vuelo'] ?>" data-precio="<?= $vuelo['precio_base'] ?>">
                                            Vuelo: <?= htmlspecialchars($vuelo['numero_vuelo']) ?> | <?= htmlspecialchars($vuelo['origen_nombre']) ?> → <?= htmlspecialchars($vuelo['destino_nombre']) ?> ($<?= number_format($vuelo['precio_base'], 2) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Seleccionar Clase de Cabina</label>
                                <select class="form-select" id="selectClase" name="id_clase" required>
                                    <option value="" data-cargo="0" disabled selected>Elija la clase...</option>
                                    <?php foreach ($listaClases as $clase): ?>
                                        <option value="<?= $clase['id_clase'] ?>" data-cargo="<?= $clase['precio_base'] ?>">
                                            <?= htmlspecialchars($clase['nombre']) ?> (Cargo +$<?= number_format($clase['precio_base'], 2) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Este cargo se suma al precio base del vuelo para calcular el total.</small>
                            </div>

                            <div class="col-12 mt-4">
                                <div class="p-3 bg-dark text-white rounded-3 d-flex justify-content-between align-items-center shadow-sm">
                                    <span class="fw-bold m-0 fs-5 text-warning"><i class="fa-solid fa-calculator me-2"></i>Precio Total de la Reserva:</span>
                                    <div>
                                        <span class="fs-3 fw-bold text-success">$</span>
                                        <input type="text" id="displayTotal" class="bg-transparent border-0 text-success fs-3 fw-bold w-auto text-end" value="0.00" readonly style="outline: none; max-width: 150px;">
                                        <input type="hidden" id="inputPrecioTotal" name="precio_total" value="0.00">
                                    </div>
                                </div>
                                <div class="mt-2 text-end text-muted small">Total = precio base del vuelo + cargo de clase seleccionado.</div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer bg-white">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning fw-bold text-dark">Confirmar Reservación</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const btnAbrirNuevaReserva = document.getElementById('btnAbrirNuevaReserva');
            const form = document.getElementById('formNuevaReserva');

            const selectVuelo = document.getElementById('selectVuelo');
            const selectClase = document.getElementById('selectClase');
            const displayTotal = document.getElementById('displayTotal');
            const inputPrecioTotal = document.getElementById('inputPrecioTotal');

            const inputDisplayCodigo = document.getElementById('inputDisplayCodigo');
            const inputHiddenCodigo = document.getElementById('inputHiddenCodigo');

            // Función para generar un código único aleatorio desde el lado del cliente al abrir el formulario
            function generarCodigoReserva() {
                const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                let resultado = 'RES-';
                for (let i = 0; i < 6; i++) {
                    resultado += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
                }
                inputDisplayCodigo.value = resultado;
                inputHiddenCodigo.value = resultado;
            }

            // FUNCIÓN DE LIMPIEZA ABSOLUTA AL HACER CLICK EN NUEVA RESERVA
            btnAbrirNuevaReserva.addEventListener('click', function() {
                // 1. Resetea los campos nativos del formulario
                form.reset();

                // 2. Limpia manualmente cada input para destruir cualquier rastro antiguo
                document.getElementById('inputAsiento').value = "";
                document.getElementById('inputNombre').value = "";
                document.getElementById('inputApellido').value = "";
                document.getElementById('inputCedula').value = "";
                document.getElementById('inputEmail').value = "";
                document.getElementById('inputTelefono').value = "";

                // 3. Forzar valores por defecto en selects y precios
                selectVuelo.selectedIndex = 0;
                selectClase.selectedIndex = 0;
                displayTotal.value = "0.00";
                inputPrecioTotal.value = "0.00";

                // 4. Generar un código completamente fresco y nuevo para esta venta
                generarCodigoReserva();
            });

            // Función para calcular el total
            function calcularTotal() {
                const optionVuelo = selectVuelo.options[selectVuelo.selectedIndex];
                const precioVuelo = parseFloat(optionVuelo.getAttribute('data-precio')) || 0;

                const optionClase = selectClase.options[selectClase.selectedIndex];
                const cargoClase = parseFloat(optionClase.getAttribute('data-cargo')) || 0;

                const total = precioVuelo + cargoClase;

                displayTotal.value = total.toFixed(2);
                inputPrecioTotal.value = total.toFixed(2);
            }

            selectVuelo.addEventListener('change', calcularTotal);
            selectClase.addEventListener('change', calcularTotal);
        });
    </script>
</body>

</html>