<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AeroControl - Panel Principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar-custom {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }

        .card-menu {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #ffffff;
        }

        .card-menu:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .icon-shape {
            width: 60px;
            height: 60px;
            background-color: #e9ecef;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            margin-bottom: 15px;
        }

        /* Colores dinámicos para los iconos de cada módulo */
        .icon-vuelos {
            color: #0d6efd;
            background-color: #e7f1ff;
        }

        .icon-reservas {
            color: #fd7e14;
            background-color: #fff3cd;
        }

        .icon-pilotos {
            color: #198754;
            background-color: #d1e7dd;
        }

        .icon-aviones {
            color: #6f42c1;
            background-color: #e2d9f3;
        }

        .icon-destinos {
            color: #0dcaf0;
            background-color: #cff4fc;
        }

        .icon-clases {
            color: #dc3545;
            background-color: #f8d7da;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark navbar-custom shadow mb-5 py-3">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="#">
                <i class="fa-solid fa-plane-departure me-2"></i> AeroControl
            </a>
            <span class="navbar-text text-white-50 d-none d-md-block">
                Sistema de Gestión Aeroportuaria v2.0
            </span>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row mb-4">
            <div class="col-10">
                <h2 class="fw-bold text-dark m-0">Panel de Administración</h2>
                <p class="text-muted">Selecciona el módulo del sistema que deseas gestionar de acuerdo a la base de datos.</p>
            </div>
        </div>

        <div class="row g-4">

            <div class="col-md-6 col-lg-4">
                <div class="card card-menu h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="icon-shape icon-vuelos">
                            <i class="fa-solid fa-plane"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Vuelos</h4>
                        <p class="text-muted small">Programación de rutas, asignación de aviones, pilotos, horarios y control de estados de vuelo. Incluye el precio base del trayecto.</p>
                    </div>
                    <div class="mt-3">
                        <a href="vistas/vuelos/index.php" class="btn btn-outline-primary w-100 rounded-pill fw-bold">
                            Gestionar Vuelos <i class="fa-solid fa-chevron-right ms-1 small"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card card-menu h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="icon-shape icon-reservas">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Reservas</h4>
                        <p class="text-muted small">Control de boletos vendidos, datos de pasajeros, asignación de asientos y precios finales de clases.</p>
                    </div>
                    <div class="mt-3">
                        <a href="vistas/reservas/index.php" class="btn btn-outline-warning w-100 rounded-pill fw-bold text-dark">
                            Gestionar Reservas <i class="fa-solid fa-chevron-right ms-1 small"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card card-menu h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="icon-shape icon-pilotos">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Pilotos</h4>
                        <p class="text-muted small">Registro del personal de vuelo, números de licencia, control de horas acumuladas y especialidades.</p>
                    </div>
                    <div class="mt-3">
                        <a href="vistas/pilotos/index.php" class="btn btn-outline-success w-100 rounded-pill fw-bold">
                            Gestionar Pilotos <i class="fa-solid fa-chevron-right ms-1 small"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card card-menu h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="icon-shape icon-aviones">
                            <i class="fa-solid fa-jet-fighter"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Flota de Aviones</h4>
                        <p class="text-muted small">Administración de aeronaves, matrículas únicas, capacidad de pasajeros y fechas de mantenimiento.</p>
                    </div>
                    <div class="mt-3">
                        <a href="vistas/aviones/index.php" class="btn btn-outline-purple w-100 rounded-pill fw-bold" style="color: #6f42c1; border-color: #6f42c1;">
                            Gestionar Flota <i class="fa-solid fa-chevron-right ms-1 small"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card card-menu h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="icon-shape icon-destinos">
                            <i class="fa-solid fa-earth-americas"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Destinos</h4>
                        <p class="text-muted small">Catálogo de aeropuertos internacionales y nacionales con sus respectivos códigos IATA, ciudades y países.</p>
                    </div>
                    <div class="mt-3">
                        <a href="vistas/destinos/index.php" class="btn btn-outline-info w-100 rounded-pill fw-bold text-dark">
                            Gestionar Destinos <i class="fa-solid fa-chevron-right ms-1 small"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card card-menu h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="icon-shape icon-clases">
                            <i class="fa-solid fa-chair"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Clases de Vuelo</h4>
                        <p class="text-muted small">Configuración de categorías de cabina con cargos adicionales, servicios y asientos disponibles.</p>
                    </div>
                    <div class="mt-3">
                        <a href="vistas/clases_vuelo/index.php" class="btn btn-outline-danger w-100 rounded-pill fw-bold">
                            Gestionar Clases <i class="fa-solid fa-chevron-right ms-1 small"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer class="bg-dark text-white-50 text-center py-3 mt-5 shadow-sm">
        <div class="container">
            <small>&copy; 2026 AeroControl Inc. Todos los derechos reservados a la tripulación.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>