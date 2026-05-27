<?php
// 1. Incluimos el modelo de Clase de Vuelo
require_once '../../modelos/ClaseVuelo.php';

// Instanciamos el objeto
$claseModel = new ClaseVuelo();

// 2. Procesamiento de formularios (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {

        // --- CREAR CLASE ---
        if ($_POST['accion'] === 'crear') {
            $claseModel->nombre = $_POST['nombre'];
            $claseModel->descripcion = $_POST['descripcion'];
            $claseModel->precio_base = $_POST['precio_base'];
            $claseModel->cantidad_asientos = $_POST['cantidad_asientos'];
            $claseModel->servicios = $_POST['servicios'];
            $claseModel->crear();
        }

        // --- EDITAR CLASE ---
        elseif ($_POST['accion'] === 'editar') {
            $claseModel->id_clase = $_POST['id_clase'];
            $claseModel->nombre = $_POST['nombre'];
            $claseModel->descripcion = $_POST['descripcion'];
            $claseModel->precio_base = $_POST['precio_base'];
            $claseModel->cantidad_asientos = $_POST['cantidad_asientos'];
            $claseModel->servicios = $_POST['servicios'];
            $claseModel->actualizar();
        }

        // --- ELIMINAR CLASE ---
        elseif ($_POST['accion'] === 'eliminar') {
            $claseModel->id_clase = $_POST['id_clase'];
            $claseModel->eliminar();
        }

        // Redireccionar para evitar reenvío de formulario
        header("Location: index.php");
        exit();
    }
}

// 3. Obtener la lista completa de clases de vuelo
$listaClases = $claseModel->obtenerTodas();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clases de Vuelo - AeroControl</title>
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
                <i class="fa-solid fa-crown text-warning me-2"></i> Clases de Vuelo
            </span>
            <div style="width: 85px;" class="d-none d-md-block"></div>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <div class="card shadow-sm card-modulo p-4 mb-5">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark m-0">Categorías y Tarifas de Cabina</h3>
                    <p class="text-muted small m-0">Configuración de las secciones del avión, cargos de clase y comodidades.</p>
                </div>
                <button class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaClase">
                    <i class="fa-solid fa-plus me-2"></i>Nueva Clase
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre de Clase</th>
                            <th>Descripción</th>
                            <th>Cargo Adicional</th>
                            <th>Asientos Disponibles</th>
                            <th>Servicios Adicionales</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($listaClases)): ?>
                            <?php foreach ($listaClases as $clase): ?>
                                <tr>
                                    <td class="fw-bold text-primary fs-5">
                                        <i class="fa-solid fa-couch me-2 small text-secondary"></i><?= htmlspecialchars($clase['nombre']) ?>
                                    </td>
                                    <td class="text-muted small" style="max-width: 250px;"><?= htmlspecialchars($clase['descripcion']) ?></td>
                                    <td class="fw-bold text-success">$<?= number_format($clase['precio_base'], 2) ?> <small class="text-muted">cargo</small></td>
                                    <td>
                                        <span class="badge bg-dark px-3 rounded-pill"><?= htmlspecialchars($clase['cantidad_asientos']) ?> asientos</span>
                                    </td>
                                    <td>
                                        <span class="text-dark small"><i class="fa-solid fa-star text-warning me-1"></i> <?= htmlspecialchars($clase['servicios']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-warning rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#modalEditarClase<?= $clase['id_clase'] ?>">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger rounded-circle" data-bs-toggle="modal" data-bs-target="#modalEliminarClase<?= $clase['id_clase'] ?>">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalEliminarClase<?= $clase['id_clase'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Atención</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center py-4">
                                                <h4 class="fw-bold">¿Eliminar Clase de Vuelo?</h4>
                                                <p class="text-muted mt-2">Vas a remover la categoría <strong><?= htmlspecialchars($clase['nombre']) ?></strong>. Esto podría afectar a las reservas asociadas.</p>
                                            </div>
                                            <div class="modal-footer bg-light justify-content-center">
                                                <form action="" method="POST" class="m-0">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="id_clase" value="<?= $clase['id_clase'] ?>">
                                                    <button type="button" class="btn btn-secondary px-4 me-2" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger fw-bold px-4">Confirmar Eliminación</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="modalEditarClase<?= $clase['id_clase'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-pen me-2"></i>Modificar Clase</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="" method="POST">
                                                <div class="modal-body bg-light">
                                                    <input type="hidden" name="accion" value="editar">
                                                    <input type="hidden" name="id_clase" value="<?= $clase['id_clase'] ?>">

                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <label class="form-label small fw-bold">Nombre de la Clase</label>
                                                            <select class="form-select" name="nombre" required>
                                                                <option value="Económica" <?= $clase['nombre'] == 'Económica' ? 'selected' : '' ?>>Económica</option>
                                                                <option value="Premium Economy" <?= $clase['nombre'] == 'Premium Economy' ? 'selected' : '' ?>>Premium Economy</option>
                                                                <option value="Ejecutiva (Business)" <?= $clase['nombre'] == 'Ejecutiva (Business)' ? 'selected' : '' ?>>Ejecutiva (Business)</option>
                                                                <option value="Primera Clase" <?= $clase['nombre'] == 'Primera Clase' ? 'selected' : '' ?>>Primera Clase</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">Cargo Adicional ($)</label>
                                                            <input type="number" step="0.01" class="form-control" name="precio_base" value="<?= $clase['precio_base'] ?>" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">Cantidad de Asientos</label>
                                                            <input type="number" class="form-control" name="cantidad_asientos" value="<?= $clase['cantidad_asientos'] ?>" required>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label small fw-bold">Descripción</label>
                                                            <textarea class="form-control" name="descripcion" rows="2" required><?= htmlspecialchars($clase['descripcion']) ?></textarea>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label small fw-bold">Servicios Incluidos</label>
                                                            <input type="text" class="form-control" name="servicios" value="<?= htmlspecialchars($clase['servicios']) ?>">
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
                                    <i class="fa-solid fa-couch fs-1 d-block mb-3 text-secondary"></i>
                                    No hay clases de vuelo registradas todavía.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNuevaClase" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus me-2"></i>Agregar Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body bg-light">
                        <input type="hidden" name="accion" value="crear">

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold">Categoría de Clase</label>
                                <select class="form-select fw-bold text-primary" id="selectCategoria" name="nombre" required>
                                    <option value="" disabled selected>Seleccione la categoría...</option>
                                    <option value="Económica">Económica</option>
                                    <option value="Premium Economy">Premium Economy</option>
                                    <option value="Ejecutiva (Business)">Ejecutiva (Business)</option>
                                    <option value="Primera Clase">Primera Clase</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Cargo Adicional por Clase ($)</label>
                                <input type="number" step="0.01" class="form-control" id="inputPrecio" name="precio_base" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Asientos en esta sección</label>
                                <input type="number" class="form-control" name="cantidad_asientos" placeholder="Ej: 45" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Descripción Corta</label>
                                <textarea class="form-control" id="inputDescripcion" name="descripcion" rows="2" required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Servicios Ofrecidos</label>
                                <input type="text" class="form-control" id="inputServicios" name="servicios" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning fw-bold text-dark">Crear Clase</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Diccionario con los datos fijos de cada clase
            const datosClases = {
                "Económica": {
                    precio: "50.00",
                    descripcion: "Asiento estándar. Ideal para vuelos cortos y presupuesto ajustado.",
                    servicios: "Equipaje de mano (10kg), Snack básico"
                },
                "Premium Economy": {
                    precio: "150.00",
                    descripcion: "Mayor espacio para las piernas y reclinación mejorada. Ubicación preferencial.",
                    servicios: "Equipaje facturado (23kg), Embarque prioritario, Comida estándar"
                },
                "Ejecutiva (Business)": {
                    precio: "400.00",
                    descripcion: "Asientos tipo cama, privacidad y confort premium para viajes de negocios.",
                    servicios: "2 Equipajes (23kg), Acceso a sala VIP, Menú a la carta, WiFi gratis"
                },
                "Primera Clase": {
                    precio: "900.00",
                    descripcion: "El máximo lujo en el aire. Suites privadas y servicio completamente personalizado.",
                    servicios: "Equipaje ilimitado, Suite privada, Chef a bordo, Transporte VIP"
                }
            };

            const selectElement = document.getElementById('selectCategoria');
            const inputPrecio = document.getElementById('inputPrecio');
            const inputDescripcion = document.getElementById('inputDescripcion');
            const inputServicios = document.getElementById('inputServicios');

            // Escuchamos cada vez que cambia la opción en el select
            selectElement.addEventListener('change', function() {
                const claseSeleccionada = this.value;

                // Si la clase existe en nuestro diccionario, rellenamos los campos
                if (datosClases[claseSeleccionada]) {
                    inputPrecio.value = datosClases[claseSeleccionada].precio;
                    inputDescripcion.value = datosClases[claseSeleccionada].descripcion;
                    inputServicios.value = datosClases[claseSeleccionada].servicios;
                }
            });
        });
    </script>
</body>

</html>