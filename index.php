<?php
// index.php
require_once 'config/conexion.php';

// Instanciar conexión para los contadores analíticos
$database = new Database();
$db = $database->getConnection();

// Consultas agregadas rápidas para el dashboard estadístico
$countVuelos = $db->query("SELECT COUNT(*) FROM vuelos")->fetchColumn();
$countAviones = $db->query("SELECT COUNT(*) FROM aviones")->fetchColumn();
$countPilotos = $db->query("SELECT COUNT(*) FROM pilotos")->fetchColumn();
$countReservas = $db->query("SELECT COUNT(*) FROM reservas")->fetchColumn();

require_once 'includes/header.php';
?>

<div class="p-5 mb-4 bg-white rounded-3 shadow-sm border">
    <div class="container-fluid py-3">
        <h1 class="display-5 fw-bold text-dark">Panel de Control de Operaciones</h1>
        <p class="col-md-8 fs-4 text-muted">Bienvenido al entorno de gestión integrada de la aerolínea. Supervise vuelos, controle flotas aéreas y administre reservas de manera segura y eficiente.</p>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-4 g-4 mt-2">
    <div class="col">
        <div class="card card-custom bg-primary text-white h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase text-white-50">Vuelos Activos</h6>
                    <h2 class="fw-bold mb-0"><?php echo $countVuelos; ?></h2>
                </div>
                <i class="fa-solid fa-plane fa-3x text-white-50"></i>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card card-custom bg-success text-white h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase text-white-50">Flota Aérea</h6>
                    <h2 class="fw-bold mb-0"><?php echo $countAviones; ?></h2>
                </div>
                <i class="fa-solid fa-plane-up fa-3x text-white-50"></i>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card card-custom bg-warning text-dark h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase text-black-50">Pilotos Activos</h6>
                    <h2 class="fw-bold mb-0"><?php echo $countPilotos; ?></h2>
                </div>
                <i class="fa-solid fa-id-card fa-3x text-black-50"></i>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card card-custom bg-danger text-white h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase text-white-50">Boletos Vendidos</h6>
                    <h2 class="fw-bold mb-0"><?php echo $countReservas; ?></h2>
                </div>
                <i class="fa-solid fa-ticket fa-3x text-white-50"></i>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>