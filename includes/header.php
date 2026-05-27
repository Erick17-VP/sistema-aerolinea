<?php
// includes/header.php
// Definimos una constante para manejar rutas dinámicas absolutas independientemente del nivel de carpeta
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/aerolinea/');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema AeroLínea - Panel Profesional</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 1px;
        }

        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
        }

        .card-custom:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php">
                <i class="fa-solid fa-plane-departure text-info me-2"></i>AEROLÍNEA
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-span"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>modules/vuelos/index.php"><i class="fa-solid fa-calendar-days me-1"></i> Vuelos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>modules/aviones/index.php"><i class="fa-solid fa-plane-up me-1"></i> Aviones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>modules/pilotos/index.php"><i class="fa-solid fa-id-card me-1"></i> Pilotos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>modules/reservas/index.php"><i class="fa-solid fa-ticket me-1"></i> Reservas</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container my-4">