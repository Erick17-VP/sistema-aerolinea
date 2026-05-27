<?php
require_once '../../modelos/Vuelo.php';
require_once '../../modelos/Avion.php';
require_once '../../modelos/Piloto.php';
require_once '../../modelos/Destino.php';

$vueloModel   = new Vuelo();
$avionModel   = new Avion();
$pilotoModel  = new Piloto();
$destinoModel = new Destino();

// Control de pasos del formulario (Por defecto empezamos en el Paso 1)
$paso = 1;

// Capturamos las listas básicas para los selects
$listaAviones  = $avionModel->obtenerTodos();
$listaPilotos  = $pilotoModel->obtenerTodos();
$listaDestinos = $destinoModel->obtenerTodos();
$listaVuelos   = $vueloModel->obtenerTodos();

// --- PROCESAMIENTO DE PASOS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Si el usuario presionó "Siguiente" en el Paso 1
    if (isset($_POST['ir_a_paso_2'])) {
        $paso = 2;

        // Guardamos temporalmente lo seleccionado en el Paso 1
        $id_avion      = $_POST['id_avion'];
        $id_piloto     = $_POST['id_piloto'];
        $id_origen     = $_POST['id_destino_origen'];
        $id_destino    = $_POST['id_destino_destino'];
        $fecha_salida  = $_POST['fecha_salida'];
        $hora_salida   = $_POST['hora_salida'];

        // Generamos el número de vuelo único usando PHP puro
        $numero_vuelo  = 'VUE-' . strtoupper(substr(md5(time()), 0, 5));

        // AUTOMATIZACIÓN 1: Costo fijo y Horas de viaje según el destino seleccionado
        // (Configurado directamente en PHP para evitar alterar la BD)
        switch ($id_destino) {
            case 1: // Ejemplo: México (MEX)
                $precio_base = 300.00;
                $horas_viaje = 3;
                break;
            case 2: // Ejemplo: Nueva York (JFK)
                $precio_base = 550.00;
                $horas_viaje = 5;
                break;
            case 3: // Ejemplo: Bogotá (BOG)
                $precio_base = 400.00;
                $horas_viaje = 4;
                break;
            default:
                $precio_base = 250.00;
                $horas_viaje = 2;
                break;
        }

        // AUTOMATIZACIÓN 2: Calcular la hora y fecha de llegada automáticamente en el servidor
        $timestamp_salida = strtotime("$fecha_salida $hora_salida");
        $timestamp_llegada = $timestamp_salida + ($horas_viaje * 3600); // Sumamos las horas en segundos

        $fecha_llegada = date('Y-m-d', $timestamp_llegada);
        $hora_llegada  = date('H:i', $timestamp_llegada);
    }

    // Si el usuario presionó "Guardar Vuelo" en el Paso 2
    if (isset($_POST['finalizar_registro'])) {
        $vueloModel->numero_vuelo       = $_POST['numero_vuelo'];
        $vueloModel->id_avion           = $_POST['id_avion'];
        $vueloModel->id_piloto          = $_POST['id_piloto'];
        $vueloModel->id_destino_origen  = $_POST['id_destino_origen'];
        $vueloModel->id_destino_destino = $_POST['id_destino_destino'];
        $vueloModel->fecha_salida       = $_POST['fecha_salida'];
        $vueloModel->hora_salida        = $_POST['hora_salida'];
        $vueloModel->fecha_llegada      = $_POST['fecha_llegada'];
        $vueloModel->hora_llegada       = $_POST['hora_llegada'];
        $vueloModel->precio_base        = $_POST['precio_base'];
        $vueloModel->estado             = 'Programado';

        $vueloModel->crear();

        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Vuelos - AeroControl</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container-fluid px-4">
            <span class="navbar-brand mb-0 h1"><i class="fa-solid fa-plane-up text-info me-2"></i> Programación de Vuelos</span>
        </div>
    </nav>

    <div class="container px-4">

        <div class="card shadow-sm border-0 rounded-3 p-4 mb-5">
            <h4 class="fw-bold text-secondary mb-3">
                <i class="fa-solid fa-route me-2"></i>Asistente de Programación Automatizada
            </h4>

            <div class="progress mb-4" style="height: 8px;">
                <div class="progress-bar bg-info" style="width: <?= $paso == 1 ? '50%' : '100%' ?>;"></div>
            </div>

            <?php if ($paso === 1): ?>
                <form action="" method="POST" autocomplete="off">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Avión Asignado</label>
                            <select class="form-select" name="id_avion" required>
                                <option value="" disabled selected>Seleccione el avión...</option>
                                <?php foreach ($listaAviones as $avion): ?>
                                    <option value="<?= $avion['id_avion'] ?>">Matrícula: <?= $avion['numero_matricula'] ?> (<?= $avion['modelo'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Piloto a Cargo</label>
                            <select class="form-select" name="id_piloto" required>
                                <option value="" disabled selected>Seleccione el piloto...</option>
                                <?php foreach ($listaPilotos as $piloto): ?>
                                    <option value="<?= $piloto['id_piloto'] ?>"><?= $piloto['nombre'] ?> <?= $piloto['apellido'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Origen</label>
                            <select class="form-select" name="id_destino_origen" required>
                                <option value="" disabled selected>Seleccione origen...</option>
                                <?php foreach ($listaDestinos as $destino): ?>
                                    <option value="<?= $destino['id_destino'] ?>"><?= $destino['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Destino (Establece precio y duración)</label>
                            <select class="form-select" name="id_destino_destino" required>
                                <option value="" disabled selected>Seleccione destino...</option>
                                <?php foreach ($listaDestinos as $destino): ?>
                                    <option value="<?= $destino['id_destino'] ?>"><?= $destino['nombre'] ?> (<?= $destino['codigo_aeropuerto'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-success">Fecha de Salida</label>
                            <input type="date" class="form-control border-success" name="fecha_salida" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-success">Hora de Salida</label>
                            <input type="time" class="form-control border-success" name="hora_salida" required>
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <button type="submit" name="ir_a_paso_2" class="btn btn-info text-white fw-bold px-4">
                            Calcular Itinerario y Precios <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>

            <?php elseif ($paso === 2): ?>
                <form action="" method="POST">
                    <input type="hidden" name="id_avion" value="<?= $id_avion ?>">
                    <input type="hidden" name="id_piloto" value="<?= $id_piloto ?>">
                    <input type="hidden" name="id_destino_origen" value="<?= $id_origen ?>">
                    <input type="hidden" name="id_destino_destino" value="<?= $id_destino ?>">
                    <input type="hidden" name="fecha_salida" value="<?= $fecha_salida ?>">
                    <input type="hidden" name="hora_salida" value="<?= $hora_salida ?>">

                    <input type="hidden" name="numero_vuelo" value="<?= $numero_vuelo ?>">
                    <input type="hidden" name="fecha_llegada" value="<?= $fecha_llegada ?>">
                    <input type="hidden" name="hora_llegada" value="<?= $hora_llegada ?>">
                    <input type="hidden" name="precio_base" value="<?= $precio_base ?>">

                    <div class="alert alert-success d-flex align-items-center mb-4">
                        <i class="fa-solid fa-circle-check fs-4 me-3"></i>
                        <div><strong>¡Cálculos completados!</strong> El sistema ha verificado los tiempos de vuelo y las tarifas bases fijas correspondientes.</div>
                    </div>

                    <div class="row g-3 bg-white p-3 rounded border">
                        <div class="col-md-4">
                            <label class="form-label text-muted small d-block">Número de Vuelo Auto-Generado</label>
                            <span class="fs-5 fw-bold text-primary"><?= $numero_vuelo ?></span>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small d-block">Llegada Calculada Automatizada</label>
                            <span class="fs-5 fw-bold text-dark">
                                <i class="fa-solid fa-calendar-days text-danger me-1"></i> <?= $fecha_llegada ?>
                                <i class="fa-solid fa-clock text-danger ms-2 me-1"></i> <?= $hora_llegada ?>
                            </span>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small d-block">Precio Base Fijo Destino</label>
                            <span class="fs-5 fw-bold text-success">$<?= number_format($precio_base, 2) ?></span>
                        </div>

                        <hr class="my-3">

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Tipo de Cabina / Clase</label>
                            <select class="form-select border-secondary" name="clase_asiento" required>
                                <option value="Económica">Clase Económica (Filas 11 a 30)</option>
                                <option value="Premium">Premium Economy (Filas 5 a 10)</option>
                                <option value="Ejecutiva">Clase Ejecutiva (Filas 1 a 4)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Asiento Asignado (Mapeo automático)</label>
                            <select class="form-select border-secondary" name="numero_asiento" required>
                                <option value="" disabled selected>Escoja un asiento libre...</option>
                                <?php
                                $letras = ['A', 'B', 'C', 'D', 'E', 'F'];
                                for ($fila = 1; $fila <= 30; $fila++) {
                                    foreach ($letras as $letra) {
                                        $asiento = $fila . $letra;
                                        // Aquí le mostramos al usuario a qué clase pertenece visualmente cada opción
                                        if ($fila <= 4) $tag = " [Ejecutiva]";
                                        elseif ($fila <= 10) $tag = " [Premium]";
                                        else $tag = " [Económica]";

                                        echo "<option value='$asiento'>Asiento $asiento $tag</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php" class="btn btn-secondary fw-bold px-4">Volver / Cancelar</a>
                        <button type="submit" name="finalizar_registro" class="btn btn-success fw-bold px-4">
                            <i class="fa-solid fa-plane-circle-check me-2"></i>Guardar Vuelo Programado
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <div class="card shadow-sm border-0 rounded-3 p-4">
            <h5 class="fw-bold text-dark mb-3">Itinerarios Activos en el Sistema</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>N° Vuelo</th>
                            <th>Ruta</th>
                            <th>Salida / Llegada</th>
                            <th>Precio Tarifa</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listaVuelos as $v): ?>
                            <tr>
                                <td class="fw-bold text-primary"><?= $v['numero_vuelo'] ?></td>
                                <td><?= htmlspecialchars($v['origen_nombre'] ?? 'Origen') ?> a <?= htmlspecialchars($v['destino_nombre'] ?? 'Destino') ?></td>
                                <td class="small">
                                    <span class="text-success">S: <?= $v['fecha_salida'] ?> (<?= $v['hora_salida'] ?>)</span><br>
                                    <span class="text-danger">LL: <?= $v['fecha_llegada'] ?> (<?= $v['hora_llegada'] ?>)</span>
                                </td>
                                <td class="fw-bold text-success">$<?= number_format($v['precio_base'], 2) ?></td>
                                <td><span class="badge bg-primary"><?= $v['estado'] ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>

</html>