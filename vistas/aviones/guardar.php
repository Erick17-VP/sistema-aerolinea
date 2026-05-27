<?php
// 1. Llamamos a nuestro modelo (Cambia 'modelos' por 'models' si tu carpeta está en inglés)
require_once dirname(__DIR__, 2) . '/modelos/Avion.php';

// 2. Verificamos que los datos vengan del formulario mediante POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Instanciamos el avión
    $avion = new Avion();

    // 3. Atrapamos los datos del formulario y se los pasamos a las propiedades de la clase
    $avion->numero_matricula = $_POST['numero_matricula'];
    $avion->modelo = $_POST['modelo'];
    $avion->capacidad = $_POST['capacidad'];

    // Si dejaron el año o mantenimiento vacío, lo mandamos como nulo
    $avion->año_fabricacion = empty($_POST['año_fabricacion']) ? null : $_POST['año_fabricacion'];
    $avion->ultimo_mantenimiento = empty($_POST['ultimo_mantenimiento']) ? null : $_POST['ultimo_mantenimiento'];

    $avion->estado = $_POST['estado'];

    // 4. Ejecutamos la función para guardar en la base de datos
    // Nota: Estoy asumiendo que tu equipo le llamó "crear" a la función de insertar. 
    // Si usaron "insertar" o "agregar", solo cambia la palabra aquí abajo.
    $resultado = $avion->crear();

    // 5. Si todo salió bien, lo regresamos a la tabla principal
    if ($resultado) {
        header("Location: index.php?mensaje=exito");
        exit();
    } else {
        echo "¡Ups! Hubo un problema al guardar. Revisa cómo se llama la función para insertar en tu archivo Avion.php.";
    }
} else {
    // Si alguien intenta entrar directo a este archivo sin usar el formulario, lo regresamos
    header("Location: index.php");
    exit();
}
