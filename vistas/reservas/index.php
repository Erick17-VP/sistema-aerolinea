<?php
// Asegúrate de que las rutas de tus modelos correspondan a tu estructura de carpetas
require_once '../../modelos/Vuelo.php';
require_once '../../modelos/Reserva.php';

// Conexión rápida PDO nativa para alimentar los selects dinámicos de esta vista
$db = new PDO("mysql:host=localhost;dbname=aerolinea_db", "root", "VEPE-1702");

$paso = 1;
$vuelo_seleccionado = null;
$clases_disponibles = $db->query("SELECT * FROM clases_vuelo")->fetchAll(PDO::FETCH_ASSOC);

// --- CONTROL DEL FLUJO DE COMPRA REAL (PASO A PASO) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // PASO 1 -> PASO 2: El cliente eligió un vuelo de la cartelera
    if (isset($_POST['seleccionar_vuelo'])) {
        $id_vuelo = $_POST['id_vuelo'];

        // Buscamos toda la información de ese vuelo para mostrársela en el ticket
        $stmt = $db->prepare("SELECT v.*, d.origen, d.destino, d.duracion_estimada FROM vuelos v JOIN destinos d ON v.id_destino = d.id_destino WHERE v.id_vuelo = ?");
        $stmt->execute([$id_vuelo]);
        $vuelo_seleccionado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($vuelo_seleccionado) {
            $paso = 2; // Avanzamos al formulario de pasajeros
        }
    }

    // PASO 2 -> FINALIZAR: El cliente llena sus datos y confirma el pago del boleto
    if (isset($_POST['confirmar_compra'])) {
        $id_vuelo      = $_POST['id_vuelo'];
        $id_clase      = $_POST['id_clase'];
        $nombre        = $_POST['nombre_pasajero'];
        $apellido      = $_POST['apellido_pasajero'];
        $cedula        = $_POST['numero_cedula'];
        $email         = $_POST['email'];
        $asiento       = $_POST['numero_asiento'];

        // Obtener costos en el servidor para calcular el total real (Seguridad antibloqueo/filtraciones)
        $vStmt = $db->prepare("SELECT precio_base FROM vuelos WHERE id_vuelo = ?");
        $vStmt->execute([$id_vuelo]);
        $p_base = $vStmt->fetchColumn();

        $cStmt = $db->prepare("SELECT cargo_adicional FROM clases_vuelo WHERE id_clase = ?");
        $cStmt->execute([$id_clase]);
        $p_extra = $cStmt->fetchColumn();

        $precio_total = $p_base + $p_extra;
        $numero_reserva = 'PNR-' . strtoupper(substr(md5(time()), 0, 6)); // Código de barra real de aerolínea

        // Guardar la compra en la Base de Datos
        $insert = $db->prepare("INSERT INTO reservas (numero_reserva, id_vuelo, id_clase, nombre_pasajero, apellido_pasajero, numero_cedula, email, numero_asiento, precio_total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->execute([$numero_reserva, $id_vuelo, $id_clase, $nombre, $apellido, $cedula, $email, $asiento]);

        // Redireccionamos para limpiar el formulario y simular éxito
        header("Location: index.php?success=1&ticket=" . $numero_reserva);
        exit();
    }
}

// Obtener todas las reservas hechas para la tabla de control
$todas_las_reservas = $db->query("SELECT r.*, v.numero_vuelo, d.destino, c.nombre as clase_nombre FROM reservas r JOIN vuelos v ON r.id_vuelo = v.id_vuelo JOIN destinos d ON v.id_destino = d.id_destino JOIN clases_vuelo c ON r.id_clase = c.id_clase ORDER BY r.id_reserva DESC")->fetchAll(PDO::FETCH_ASSOC);

// Obtener los vuelos activos para la cartelera inicial
$cartelera_vuelos = $db->query("SELECT v.*, d.origen, d.destino, d.duracion_estimada FROM vuelos v JOIN destinos d ON v.id_destino = d.id_destino WHERE v.estado = 'Disponible'")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reserva de Vuelos - FlyControl</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light text-dark">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <span class="navbar-brand fw-bold fs-4"><i class="fa-solid fa-plane-departure me-2"></i> AeroLínea Express</span>
            <span class="navbar-text text-white-50 d-none d-md-inline">Portal de Compra de Boletos</span>
        </div>
    </nav>

    <div class="container my-5">

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success shadow border-0 p-4 mb-4 rounded-3 animate__animated animate__fadeIn">
                <div class="d-flex align-items-center">
                    <i class="fa-solid fa-circle-check text-success fs-1 me-3"></i>
                    <div>
                        <h4 class="alert-heading fw-bold mb-1">¡Abordaje Confirmado!</h4>
                        <p class="mb-0 text-muted">Tu boleto ha sido procesado de forma segura. Código de reserva oficial: <strong class="text-dark"><?= htmlspecialchars($_GET['ticket']) ?></strong></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($paso === 1): ?>
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary text-white rounded-3 p-2 me-3"><i class="fa-solid fa-search fs-4"></i></div>
                    <div>
                        <h4 class="fw-bold mb-0">Selecciona tu Destino</h4>
                        <small class="text-muted">Vuelos comerciales disponibles en tiempo real sin almacenamiento de cookies ni filtraciones</small>
                    </div>
                </div>

                <div class="row g-3">
                    <?php foreach ($cartelera_vuelos as $vuelo): ?>
                        <div class="col-md-4">
                            <div class="card h-100 border rounded-3 p-3 shadow-hover bg-white">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-secondary text-white small fw-bold"><?= $vuelo['numero_vuelo'] ?></span>
                                    <span class="text-muted small"><i class="fa-solid fa-clock me-1"></i> <?= $vuelo['duracion_estimada'] ?></span>
                                </div>
                                <h5 class="fw-bold text-dark my-2"><?= $vuelo['origen'] ?> <i class="fa-solid fa-arrow-right text-primary mx-1 fs-6"></i> <?= $vuelo['destino'] ?></h5>
                                <p class="text-muted small mb-3"><i class="fa-solid fa-calendar me-1"></i> Salida: <?= $vuelo['fecha_salida'] ?> a las <?= substr($vuelo['hora_salida'], 0, 5) ?> hrs.</p>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <div>
                                        <small class="text-muted d-block small">Desde</small>
                                        <span class="fs-4 fw-bold text-success">$<?= number_format($vuelo['precio_base'], 2) ?></span>
                                    </div>
                                    <form action="" method="POST">
                                        <input type="hidden" name="id_vuelo" value="<?= $vuelo['id_vuelo'] ?>">
                                        <button type="submit" name="seleccionar_vuelo" class="btn btn-primary fw-bold px-3 btn-sm rounded-2">
                                            Elegir Vuelo <i class="fa-solid fa-chevron-right ms-1"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php elseif ($paso === 2): ?>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-primary text-white rounded-4 p-4 sticky-top" style="top: 20px;">
                        <h5 class="fw-bold mb-3"><i class="fa-solid fa-ticket me-2"></i>Tu Itinerario</h5>
                        <div class="border-bottom border-white-10 pb-3 mb-3">
                            <h4 class="fw-bold mb-1"><?= $vuelo_seleccionado['origen'] ?></h4>
                            <i class="fa-solid fa-arrow-down my-2 fs-5 text-white-50"></i>
                            <h4 class="fw-bold mb-0"><?= $vuelo_seleccionado['destino'] ?></h4>
                        </div>
                        <ul class="list-unstyled small mb-4 opacity-75">
                            <li class="mb-2"><i class="fa-solid fa-plane me-2"></i> Vuelo comercial: <?= $vuelo_seleccionado['numero_vuelo'] ?></li>
                            <li class="mb-2"><i class="fa-solid fa-calendar me-2"></i> Fecha: <?= $vuelo_seleccionado['fecha_salida'] ?></li>
                            <li class="mb-2"><i class="fa-solid fa-clock me-2"></i> Despegue: <?= substr($vuelo_seleccionado['hora_salida'], 0, 5) ?> hrs</li>
                            <li><i class="fa-solid fa-stopwatch me-2"></i> Duración de ruta: <?= $vuelo_seleccionado['duracion_estimada'] ?></li>
                        </ul>
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top border-white-10">
                            <span class="small opacity-75">Tarifa Base Fija:</span>
                            <span class="fs-3 fw-bold">$<?= number_format($vuelo_seleccionado['precio_base'], 2) ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h4 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-id-card text-primary me-2"></i>Información del Pasajero</h4>

                        <form action="" method="POST" autocomplete="off">
                            <input type="hidden" name="id_vuelo" value="<?= $vuelo_seleccionado['id_vuelo'] ?>">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Nombre(s)</label>
                                    <input type="text" class="form-control" name="nombre_pasajero" required placeholder="Ej. Juan">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Apellido(s)</label>
                                    <input type="text" class="form-control" name="apellido_pasajero" required placeholder="Ej. Pérez">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Número de Cédula / Identificación</label>
                                    <input type="text" class="form-control" name="numero_cedula" required placeholder="Ej. ID-99812">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Correo Electrónico (Para envío del Ticket)</label>
                                    <input type="email" class="form-control" name="email" required placeholder="juan.perez@mail.com">
                                </div>

                                <hr class="my-4 text-muted">

                                <h5 class="fw-bold text-dark mb-2"><i class="fa-solid fa-chair text-primary me-2"></i>Distribución de Cabina y Asientos</h5>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Clase y Confort (Afecta tarifa final)</label>
                                    <select class="form-select" name="id_clase" required>
                                        <?php foreach ($clases_disponibles as $clase): ?>
                                            <option value="<?= $clase['id_clase'] ?>">
                                                <?= $clase['nombre'] ?> (+$<?= number_format($clase['cargo_adicional'], 2) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Asiento Disponible Físico</label>
                                    <select class="form-select" name="numero_asiento" required>
                                        <option value="" disabled selected>Escoge un asiento asignado...</option>
                                        <?php
                                        $letras = ['A', 'B', 'C', 'D', 'E', 'F'];
                                        for ($fila = 1; $fila <= 25; $fila++) {
                                            foreach ($letras as $letra) {
                                                $asiento = $fila . $letra;
                                                if ($fila <= 4) $tag = " [Ejecutiva]";
                                                elseif ($fila <= 10) $tag = " [Premium]";
                                                else $tag = " [Económica]";

                                                echo "<option value='$asiento'>Fila $fila - Asiento $asiento $tag</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                <a href="index.php" class="btn btn-light fw-bold text-muted px-4 rounded-3">Cambiar Vuelo</a>
                                <button type="submit" name="confirmar_compra" class="btn btn-success fw-bold px-4 rounded-3 shadow-sm">
                                    <i class="fa-solid fa-credit-card me-2"></i>Emitir Boleto y Confirmar Pago
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-4 p-4 mt-5">
            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-list-check text-secondary me-2"></i>Historial de Boletos Emitidos (Control de la Aerolínea)</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small">
                        <tr>
                            <th>Código Ticket</th>
                            <th>Pasajero</th>
                            <th>Vuelo / Destino</th>
                            <th>Asiento - Clase</th>
                            <th>Total Pagado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($todas_las_reservas)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4 small">No se han vendido boletos comerciales el día de hoy.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($todas_las_reservas as $reserva): ?>
                                <tr>
                                    <td class="fw-bold text-primary font-monospace"><?= $reserva['numero_reserva'] ?></td>
                                    <td>
                                        <span class="d-block fw-bold text-dark"><?= htmlspecialchars($reserva['nombre_pasajero'] . ' ' . $reserva['apellido_pasajero']) ?></span>
                                        <small class="text-muted small"><?= htmlspecialchars($reserva['email']) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border me-1"><?= $reserva['numero_vuelo'] ?></span>
                                        <span class="small text-muted"><?= htmlspecialchars($reserva['destino']) ?></span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark"><i class="fa-solid fa-couch small me-1"></i><?= $reserva['numero_asiento'] ?></span>
                                        <small class="d-block text-muted small"><?= htmlspecialchars($reserva['clase_nombre']) ?></small>
                                    </td>
                                    <td class="fw-bold text-success">$<?= number_format($reserva['precio_total'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>

</html>