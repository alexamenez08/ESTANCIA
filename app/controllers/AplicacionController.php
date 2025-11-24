<?php
include_once "app/models/AplicacionModel.php";
include_once "app/models/RubroModel.php";
include_once "app/models/UserModel.php"; 
include_once "app/models/PeriodoModel.php"; 
include_once "app/models/InstrumentoModel.php"; 
include_once "app/models/MateriaModel.php";
include_once "config/db_connection.php"; // Necesario para la conexión en el constructor

class AplicacionController {
    private $aplicacionModel;
    private $rubroModel;
    private $userModel;
    private $periodoModel;
    private $instrumentoModel;
    private $materiaModel;

    public function __construct($connection) {
        $this->aplicacionModel = new AplicacionModel($connection);
        $this->rubroModel = new RubroModel($connection);
        $this->userModel = new UserModel($connection);
        $this->periodoModel = new PeriodoModel($connection);
        $this->instrumentoModel = new InstrumentoModel($connection);
        $this->materiaModel = new MateriaModel($connection);
    }


    public function asignar() {
        // 1. Cargar datos para los dropdowns
        
        // Profesores a ser evaluados (rol 'Profesor')
        $profesores = $this->userModel->consultarProfesoresActivos(); 
        
        // Coordinadores/Admins que pueden evaluar (Responsables FN. 6)
        $evaluadores = $this->userModel->consultarEvaluadores();
        
        // Instrumentos disponibles
        $instrumentos = $this->instrumentoModel->consultarTodos();
        
        // Periodos disponibles
        $periodos = $this->periodoModel->consultarTodos(); // Ya lo tenías

        // 2. Cargar la nueva vista del formulario de asignación
        include_once "app/views/aplicacion/form_asignar.php";
    }

    
    public function guardarAsignacion() {
        if (isset($_POST['asignar_evaluacion'])) {
            
            // 1. Capturar los IDs del formulario
            $id_instrumento = (int)$_POST['id_instrumento'];
            $id_profesor_evaluado = (int)$_POST['id_profesor_evaluado'];
            $id_periodo = (int)$_POST['id_periodo'];
            $id_evaluador_responsable = (int)$_POST['id_evaluador_responsable'];

            // 2. Llamar a un NUEVO método en el modelo
            $exito = $this->aplicacionModel->crearAsignacion(
                $id_instrumento,
                $id_profesor_evaluado,
                $id_periodo,
                $id_evaluador_responsable
            );

            // 3. Redirigir
            if ($exito) {
                // Redirigimos a la lista de aplicaciones (donde ahora aparecerá 'pendiente')
                header("Location: index.php?controlador=aplicacion&accion=consultarAplicaciones&exito_asignacion=1");
                exit();
            } else {
                die("Error al guardar la asignación pendiente.");
            }
        } else {
            // Si acceden directo, solo mostrar el formulario
            $this->asignar();
        }
    }


    
    public function consultarAplicaciones() {
        // 1. Obtener la lista de evaluaciones del modelo
        $evaluaciones = $this->aplicacionModel->consultarAplicaciones();
        
        include_once "app/views/aplicacion/consultarAplicaciones.php";
    }


    public function aplicar() {
        if (!isset($_GET['id_app'])) { die("Error: Se requiere un ID de aplicación para evaluar."); }
        $id_aplicacion = (int)$_GET['id_app'];
        
        $aplicacion_data = $this->aplicacionModel->consultarPorID($id_aplicacion);
        
        if (!$aplicacion_data || $aplicacion_data['estado'] !== 'pendiente') {
            die("Error: Evaluación no encontrada o ya ha sido completada.");
        }
        
        $rubros = $this->rubroModel->getRubrosPorInstrumento($aplicacion_data['id_instrumento']);
        
        // Cargar datos del evaluador usando el ID recuperado del modelo
        // Esto soluciona el Warning Undefined array key "id_evaluador"
        $evaluador_data = $this->userModel->consultarPorID($aplicacion_data['id_evaluador']); 
        
        // Cargar listas para los SELECTS
        $materias_disponibles = $this->materiaModel->consultarTodasMaterias(); 
        $cuatrimestres_disponibles = $this->aplicacionModel->getCuatrimestres();

        $datos = [
            'aplicacion' => $aplicacion_data,
            'rubros' => $rubros,
            'evaluador' => $evaluador_data, // Añadido
            'materias_lista' => $materias_disponibles,
            'cuatrimestres_lista' => $cuatrimestres_disponibles
        ];

        include_once "app/views/aplicacion/llenar.php"; 
    }
    
    public function guardarAplicacion() {
        
        if (!isset($_POST['guardar_aplicacion'])) { header("Location: index.php?controlador=acceso&accion=panelPrincipal"); exit(); }

        $id_aplicacion = 0;
        if (isset($_POST['id_aplicacion'])) { $id_aplicacion = (int)$_POST['id_aplicacion']; }
        if ($id_aplicacion === 0) { die("🔴 Error de seguridad: ID de aplicación faltante o inválido."); }

        // 1. Capturar datos del formulario
        $observaciones_generales = trim($_POST['observaciones_generales'] ?? '');
        
        // 🔑 CAPTURA CORREGIDA: Aseguramos que son arrays, incluso si están vacíos
        $nombre_materia = trim($_POST['asignatura'] ?? ''); 
        $cuatrimestre = trim($_POST['cuatrimestre'] ?? '');
        $fecha_evaluacion = trim($_POST['fecha_evaluacion'] ?? '');
        $puntajes_por_rubro = $_POST['puntaje'] ?? []; 
        $comentarios_por_rubro = $_POST['comentarios'] ?? []; 
        

        if (empty($puntajes_por_rubro)) { die("Error: No se recibieron puntajes para guardar."); }
        
        // 2. Obtener los IDs de rubro directamente de las claves del array
        $rubro_ids = array_keys($puntajes_por_rubro);
        $estado_final = 'completado';
        
        // 3. Calcular Puntaje Total (Usando los valores del array capturado)
        $puntaje_total = 0.0;
        foreach($puntajes_por_rubro as $p) {
            $puntaje_total += (float)$p; 
        }

        // 4. Iniciar Transacción
        $this->aplicacionModel->connection->begin_transaction(); 

        try {
            // A. ACTUALIZAR el encabezado (aplicacioninstrumento)
            $exito_update = $this->aplicacionModel->actualizarAplicacion(
                $id_aplicacion, 
                $puntaje_total, 
                $observaciones_generales, 
                $estado_final,
                $nombre_materia, 
                $cuatrimestre,
                $fecha_evaluacion
            );

            if (!$exito_update) {
                $this->aplicacionModel->connection->rollback();
                die("🔴 FALLO DEBUG 1: La actualización del encabezado falló. Error MySQL: " . $this->aplicacionModel->connection->error); 
            }

            // B. Guardar los detalles (respuesta)
            foreach ($rubro_ids as $id_rubro) {
                $id_rubro_int = (int)$id_rubro;
                
                // 🔑 CLAVE: Obtenemos el puntaje y comentario del array asociativo usando el ID
                $puntaje_rubro = (float)($puntajes_por_rubro[$id_rubro] ?? 0.0);
                $comentario_rubro = trim($comentarios_por_rubro[$id_rubro] ?? '');

                // Llama a la función robusta (delete + insert)
                $success_respuesta = $this->aplicacionModel->guardarRespuesta($id_aplicacion, $id_rubro_int, $puntaje_rubro, $comentario_rubro);
                
                if (!$success_respuesta) {
                    $this->aplicacionModel->connection->rollback();
                    die("🔴 FALLO DEBUG 2: Falló al guardar el rubro ID: {$id_rubro}. Error MySQL: " . $this->aplicacionModel->connection->error); 
                }
            }

            // C. Commit 
            $this->aplicacionModel->connection->commit();
            header("Location: index.php?controlador=aplicacion&accion=consultarAplicaciones&exito_completado=1");
            exit();

        } catch (Exception $e) {
            $this->aplicacionModel->connection->rollback();
            die("🔴 FALLO DEBUG 3 (EXCEPCIÓN): " . $e->getMessage()); 
        }
    }

    public function verDetalle() {
        // 🔑 RESTITUIDO: El error era que borré esta función antes.
        if (!isset($_GET['id_app'])) { die("Error: Se requiere un ID de aplicación para ver el detalle."); }


        $id_aplicacion = (int)$_GET['id_app'];
        // 1. Obtener todos los detalles (incluyendo respuestas y datos generales)
        $datos = $this->aplicacionModel->obtenerDetallesCompletos($id_aplicacion);
        
        if (!$datos || $datos['estado'] !== 'completado') {
            die("Error: La evaluación no existe o no ha sido completada.");
        }
        // 2. Cargar la vista de detalle
        include_once "app/views/aplicacion/verDetalle.php";

    }

    
}
