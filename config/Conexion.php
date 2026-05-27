<?php
// config/conexion.php

// 1. Importamos el archivo donde están los datos de la base
require_once __DIR__ . '/config.php';

class Database
{
    private $conexion;

    // ¡ESTA ES LA FUNCIÓN QUE VS CODE ESTÁ BUSCANDO!
    public function getConnection()
    {
        $this->conexion = null;

        try {
            // Accedemos a los datos usando "Config::CONSTANTE"
            $dsn = "mysql:host=" . Config::HOST . ";dbname=" . Config::DATABASE . ";charset=utf8";

            $this->conexion = new PDO($dsn, Config::USER, Config::PASSWORD);

            // Configuraciones de seguridad
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            echo "Error crítico de conexión: " . $exception->getMessage();
        }

        return $this->conexion;
    }
}
