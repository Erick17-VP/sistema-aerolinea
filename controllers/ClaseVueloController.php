<?php
require_once __DIR__ . '/../modelos/ClaseVuelo.php';

class ClaseVueloController
{
    public $claseModel;
    public $listaClases = [];
    public $formData = [
        'nombre' => '',
        'precio_base' => '',
        'cantidad_asientos' => '',
        'descripcion' => '',
        'servicios' => ''
    ];
    public $errors = [];
    public $modalOpen = false;

    public function __construct()
    {
        $this->claseModel = new ClaseVuelo();
        $this->listaClases = $this->claseModel->obtenerTodas();
        $this->processRequest();
        $this->modalOpen = $this->shouldOpenModal();
    }

    private function shouldOpenModal()
    {
        return isset($_GET['modal_abierto']) && $_GET['modal_abierto'] === 'clase';
    }

    private function processRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (empty($_POST['accion']) || $_POST['accion'] !== 'crear') {
            return;
        }

        $this->formData = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'precio_base' => trim($_POST['precio_base'] ?? ''),
            'cantidad_asientos' => trim($_POST['cantidad_asientos'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'servicios' => trim($_POST['servicios'] ?? '')
        ];

        $this->validateForm();

        if (empty($this->errors)) {
            $this->claseModel->nombre = $this->formData['nombre'];
            $this->claseModel->descripcion = $this->formData['descripcion'];
            $this->claseModel->precio_base = $this->formData['precio_base'];
            $this->claseModel->cantidad_asientos = $this->formData['cantidad_asientos'];
            $this->claseModel->servicios = $this->formData['servicios'];

            if ($this->claseModel->crear()) {
                header('Location: index.php?message=clase_creada');
                exit();
            }

            $this->errors[] = 'No se pudo guardar la nueva clase. Intenta de nuevo.';
        }

        $this->modalOpen = true;
    }

    private function validateForm()
    {
        if ($this->formData['nombre'] === '') {
            $this->errors[] = 'Debe seleccionar un nombre para la clase.';
        }

        if ($this->formData['precio_base'] === '' || !is_numeric($this->formData['precio_base']) || $this->formData['precio_base'] < 0) {
            $this->errors[] = 'El precio adicional debe ser un valor numérico válido mayor o igual a 0.';
        }

        if ($this->formData['cantidad_asientos'] === '' || !ctype_digit($this->formData['cantidad_asientos']) || (int)$this->formData['cantidad_asientos'] <= 0) {
            $this->errors[] = 'La cantidad de asientos debe ser un número entero positivo.';
        }

        if ($this->formData['descripcion'] === '') {
            $this->errors[] = 'La descripción corta es obligatoria.';
        }

        if ($this->formData['servicios'] === '') {
            $this->errors[] = 'Debes indicar los servicios ofrecidos.';
        }
    }
}
