<?php
require_once __DIR__ . '/../modelos/Vuelo.php';
require_once __DIR__ . '/../modelos/Avion.php';
require_once __DIR__ . '/../modelos/Piloto.php';
require_once __DIR__ . '/../modelos/Destino.php';

class VueloController
{
    public $vueloModel;
    public $avionModel;
    public $pilotoModel;
    public $destinoModel;
    public $listaVuelos = [];
    public $listaAviones = [];
    public $listaPilotos = [];
    public $listaDestinos = [];
    public $formData = [
        'numero_vuelo' => '',
        'id_avion' => '',
        'id_piloto' => '',
        'id_destino_origen' => '',
        'id_destino_destino' => '',
        'fecha_salida' => '',
        'hora_salida' => '',
        'fecha_llegada' => '',
        'hora_llegada' => '',
        'precio_base' => '',
        'estado' => 'Programado'
    ];
    public $errors = [];
    public $modalOpen = false;

    public function __construct()
    {
        $this->vueloModel = new Vuelo();
        $this->avionModel = new Avion();
        $this->pilotoModel = new Piloto();
        $this->destinoModel = new Destino();

        $this->listaVuelos = $this->vueloModel->obtenerTodos();
        $this->listaAviones = $this->avionModel->obtenerTodos();
        $this->listaPilotos = $this->pilotoModel->obtenerTodos();
        $this->listaDestinos = $this->destinoModel->obtenerTodos();

        $this->processRequest();
        $this->modalOpen = $this->shouldOpenModal();
    }

    private function shouldOpenModal()
    {
        return isset($_GET['modal_abierto']) && $_GET['modal_abierto'] === 'vuelo';
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
            'numero_vuelo' => trim($_POST['numero_vuelo'] ?? ''),
            'id_avion' => trim($_POST['id_avion'] ?? ''),
            'id_piloto' => trim($_POST['id_piloto'] ?? ''),
            'id_destino_origen' => trim($_POST['id_destino_origen'] ?? ''),
            'id_destino_destino' => trim($_POST['id_destino_destino'] ?? ''),
            'fecha_salida' => trim($_POST['fecha_salida'] ?? ''),
            'hora_salida' => trim($_POST['hora_salida'] ?? ''),
            'fecha_llegada' => trim($_POST['fecha_llegada'] ?? ''),
            'hora_llegada' => trim($_POST['hora_llegada'] ?? ''),
            'precio_base' => trim($_POST['precio_base'] ?? ''),
            'estado' => trim($_POST['estado'] ?? 'Programado')
        ];

        $this->validateForm();

        if (empty($this->errors)) {
            $this->vueloModel->numero_vuelo = $this->formData['numero_vuelo'];
            $this->vueloModel->id_avion = $this->formData['id_avion'];
            $this->vueloModel->id_piloto = $this->formData['id_piloto'];
            $this->vueloModel->id_destino_origen = $this->formData['id_destino_origen'];
            $this->vueloModel->id_destino_destino = $this->formData['id_destino_destino'];
            $this->vueloModel->fecha_salida = $this->formData['fecha_salida'];
            $this->vueloModel->hora_salida = $this->formData['hora_salida'];
            $this->vueloModel->fecha_llegada = $this->formData['fecha_llegada'];
            $this->vueloModel->hora_llegada = $this->formData['hora_llegada'];
            $this->vueloModel->precio_base = $this->formData['precio_base'];
            $this->vueloModel->estado = $this->formData['estado'];

            if ($this->vueloModel->crear()) {
                header('Location: index.php?message=vuelo_creado');
                exit();
            }

            $this->errors[] = 'No se pudo guardar el vuelo. Intenta nuevamente.';
        }

        $this->modalOpen = true;
    }

    private function validateForm()
    {
        if ($this->formData['numero_vuelo'] === '') {
            $this->errors[] = 'El número de vuelo es obligatorio.';
        }

        if ($this->formData['id_avion'] === '') {
            $this->errors[] = 'Debes seleccionar un avión.';
        }

        if ($this->formData['id_piloto'] === '') {
            $this->errors[] = 'Debes seleccionar un piloto.';
        }

        if ($this->formData['id_destino_origen'] === '' || $this->formData['id_destino_destino'] === '') {
            $this->errors[] = 'Debes seleccionar origen y destino.';
        }

        if ($this->formData['id_destino_origen'] !== '' && $this->formData['id_destino_destino'] !== '' && $this->formData['id_destino_origen'] === $this->formData['id_destino_destino']) {
            $this->errors[] = 'El origen y el destino no pueden ser iguales.';
        }

        if ($this->formData['fecha_salida'] === '' || $this->formData['hora_salida'] === '') {
            $this->errors[] = 'Debes ingresar fecha y hora de salida.';
        }

        if ($this->formData['fecha_llegada'] === '' || $this->formData['hora_llegada'] === '') {
            $this->errors[] = 'Debes ingresar fecha y hora de llegada.';
        }

        if ($this->formData['precio_base'] === '' || !is_numeric($this->formData['precio_base']) || $this->formData['precio_base'] < 0) {
            $this->errors[] = 'El precio base debe ser un valor válido mayor o igual a 0.';
        }

        if ($this->formData['estado'] === '') {
            $this->errors[] = 'Debes seleccionar un estado para el vuelo.';
        }
    }
}
