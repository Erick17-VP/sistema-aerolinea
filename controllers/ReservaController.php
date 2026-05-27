<?php
require_once __DIR__ . '/../modelos/Reserva.php';
require_once __DIR__ . '/../modelos/Vuelo.php';
require_once __DIR__ . '/../modelos/ClaseVuelo.php';

class ReservaController
{
    public $reservaModel;
    public $vueloModel;
    public $claseModel;
    public $listaReservas = [];
    public $listaVuelos = [];
    public $listaClases = [];
    public $formData = [
        'numero_reserva' => '',
        'id_vuelo' => '',
        'id_clase' => '',
        'nombre_pasajero' => '',
        'apellido_pasajero' => '',
        'numero_cedula' => '',
        'email' => '',
        'telefono' => '',
        'numero_asiento' => '',
        'precio_total' => '0.00',
        'estado' => 'Pendiente'
    ];
    public $errors = [];
    public $modalOpen = false;

    public function __construct()
    {
        $this->reservaModel = new Reserva();
        $this->vueloModel = new Vuelo();
        $this->claseModel = new ClaseVuelo();

        $this->listaReservas = $this->reservaModel->obtenerTodas();
        $this->listaVuelos = $this->vueloModel->obtenerTodos();
        $this->listaClases = $this->claseModel->obtenerTodas();

        $this->processRequest();
        $this->modalOpen = $this->shouldOpenModal();
    }

    private function shouldOpenModal()
    {
        return isset($_GET['modal_abierto']) && $_GET['modal_abierto'] === 'reserva';
    }

    private function processRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($this->modalOpen) {
                $this->ensureReservationCode();
            }
            return;
        }

        if (empty($_POST['accion'])) {
            return;
        }

        $this->loadFormDataFromPost();

        if ($_POST['accion'] === 'calcular_total') {
            $this->computeTotal();
            $this->modalOpen = true;
            return;
        }

        if ($_POST['accion'] === 'crear') {
            $this->computeTotal();
            $this->validateForm();

            if (empty($this->errors)) {
                $this->reservaModel->numero_reserva = $this->formData['numero_reserva'];
                $this->reservaModel->id_vuelo = $this->formData['id_vuelo'];
                $this->reservaModel->id_clase = $this->formData['id_clase'];
                $this->reservaModel->nombre_pasajero = $this->formData['nombre_pasajero'];
                $this->reservaModel->apellido_pasajero = $this->formData['apellido_pasajero'];
                $this->reservaModel->numero_cedula = $this->formData['numero_cedula'];
                $this->reservaModel->email = $this->formData['email'];
                $this->reservaModel->telefono = $this->formData['telefono'];
                $this->reservaModel->numero_asiento = $this->formData['numero_asiento'];
                $this->reservaModel->precio_total = $this->formData['precio_total'];
                $this->reservaModel->estado = $this->formData['estado'];

                if ($this->reservaModel->crear()) {
                    header('Location: index.php?message=reserva_creada');
                    exit();
                }

                $this->errors[] = 'No se pudo registrar la reserva. Intenta nuevamente.';
            }

            $this->modalOpen = true;
        }
    }

    private function loadFormDataFromPost()
    {
        $this->formData['numero_reserva'] = trim($_POST['numero_reserva'] ?? '');
        $this->formData['id_vuelo'] = trim($_POST['id_vuelo'] ?? '');
        $this->formData['id_clase'] = trim($_POST['id_clase'] ?? '');
        $this->formData['nombre_pasajero'] = trim($_POST['nombre_pasajero'] ?? '');
        $this->formData['apellido_pasajero'] = trim($_POST['apellido_pasajero'] ?? '');
        $this->formData['numero_cedula'] = trim($_POST['numero_cedula'] ?? '');
        $this->formData['email'] = trim($_POST['email'] ?? '');
        $this->formData['telefono'] = trim($_POST['telefono'] ?? '');
        $this->formData['numero_asiento'] = trim($_POST['numero_asiento'] ?? '');
        $this->formData['precio_total'] = trim($_POST['precio_total'] ?? '0.00');
        $this->formData['estado'] = trim($_POST['estado'] ?? 'Pendiente');

        if ($this->formData['numero_reserva'] === '') {
            $this->formData['numero_reserva'] = $this->generateReservationCode();
        }
    }

    private function generateReservationCode()
    {
        return 'RES-' . strtoupper(substr(md5(uniqid((string) microtime(true), true)), 0, 6));
    }

    private function ensureReservationCode()
    {
        if ($this->formData['numero_reserva'] === '') {
            $this->formData['numero_reserva'] = $this->generateReservationCode();
        }
    }

    public function computeTotal()
    {
        $precioVuelo = 0.00;
        $cargoClase = 0.00;

        if ($this->formData['id_vuelo'] !== '') {
            $vuelo = $this->vueloModel->obtenerPorId($this->formData['id_vuelo']);
            if ($vuelo && isset($vuelo['precio_base'])) {
                $precioVuelo = (float)$vuelo['precio_base'];
            }
        }

        if ($this->formData['id_clase'] !== '') {
            $clase = $this->claseModel->obtenerPorId($this->formData['id_clase']);
            if ($clase && isset($clase['precio_base'])) {
                $cargoClase = (float)$clase['precio_base'];
            }
        }

        $this->formData['precio_total'] = number_format($precioVuelo + $cargoClase, 2, '.', '');
    }

    private function validateForm()
    {
        if ($this->formData['id_vuelo'] === '') {
            $this->errors[] = 'Debes seleccionar un vuelo programado.';
        }

        if ($this->formData['id_clase'] === '') {
            $this->errors[] = 'Debes seleccionar una clase de vuelo.';
        }

        if ($this->formData['nombre_pasajero'] === '') {
            $this->errors[] = 'El nombre del pasajero es obligatorio.';
        }

        if ($this->formData['apellido_pasajero'] === '') {
            $this->errors[] = 'El apellido del pasajero es obligatorio.';
        }

        if ($this->formData['numero_cedula'] === '') {
            $this->errors[] = 'El documento o cédula es obligatorio.';
        }

        if ($this->formData['email'] === '') {
            $this->errors[] = 'El correo electrónico es obligatorio.';
        }

        if ($this->formData['numero_asiento'] === '') {
            $this->errors[] = 'Debes seleccionar un asiento asignado.';
        }

        if ($this->formData['precio_total'] === '' || !is_numeric($this->formData['precio_total']) || (float)$this->formData['precio_total'] <= 0) {
            $this->errors[] = 'El precio total debe calcularse correctamente antes de registrar la reserva.';
        }

        if ($this->formData['estado'] === '') {
            $this->errors[] = 'Debes seleccionar un estado para la reserva.';
        }

        if ($this->formData['id_vuelo'] !== '' && $this->formData['numero_asiento'] !== '') {
            if (!$this->reservaModel->asientoDisponible($this->formData['id_vuelo'], $this->formData['numero_asiento'])) {
                $this->errors[] = 'El asiento seleccionado ya está reservado en este vuelo. Escoge otro asiento.';
            }
        }
    }

    public function obtenerAsientosDisponibles()
    {
        if ($this->formData['id_vuelo'] === '' || $this->formData['id_clase'] === '') {
            return [];
        }

        $clase = $this->claseModel->obtenerPorId($this->formData['id_clase']);
        if (!$clase) {
            return [];
        }

        $reservas = $this->reservaModel->obtenerPorVuelo($this->formData['id_vuelo']);
        $asientosOcupados = array_column($reservas, 'numero_asiento');

        $letras = ['A', 'B', 'C', 'D', 'E', 'F'];
        if (stripos($clase['nombre'], 'Ejecutiva') !== false || stripos($clase['nombre'], 'Business') !== false) {
            $filas = range(1, 4);
        } elseif (stripos($clase['nombre'], 'Premium') !== false) {
            $filas = range(5, 10);
        } else {
            $filas = range(11, 30);
        }

        $disponibles = [];
        foreach ($filas as $fila) {
            foreach ($letras as $letra) {
                $asiento = $fila . $letra;
                if (!in_array($asiento, $asientosOcupados, true)) {
                    $disponibles[] = $asiento;
                }
            }
        }

        return $disponibles;
    }
}
