<?php
    //? Inclusión de todos los modelos necesarios para las operaciones de aplicación
    include_once "app/models/AplicacionModel.php";
    include_once "app/models/RubroModel.php";
    include_once "app/models/UserModel.php"; 
    include_once "app/models/PeriodoModel.php"; 
    include_once "app/models/InstrumentoModel.php"; 
    include_once "app/models/MateriaModel.php";
    include_once "config/db_connection.php";

    //! Clase controladora encargada de gestionar el ciclo de vida de la evaluación docente:
    //! Asignación, Aplicación (llenado) y Consulta de los instrumentos.
    class AplicacionController {
        private $aplicacionModel;
        private $rubroModel;
        private $userModel;
        private $periodoModel;
        private $instrumentoModel;
        private $materiaModel;

        /**
         * Constructor del controlador. Inicializa las instancias de todos los Modelos necesarios.
         */
        public function __construct($connection) {
            $this->aplicacionModel = new AplicacionModel($connection);
            $this->rubroModel = new RubroModel($connection);
            $this->userModel = new UserModel($connection);
            $this->periodoModel = new PeriodoModel($connection);
            $this->instrumentoModel = new InstrumentoModel($connection);
            $this->materiaModel = new MateriaModel($connection);
        }


        //* Método para cargar datos y mostrar el formulario de asignación de una nueva evaluación.
        public function asignar() {
            //? 1. Cargar datos para los dropdowns
            
            //? Profesores a ser evaluados (rol 'Profesor')
            $profesores = $this->userModel->consultarProfesoresActivos(); 
            
            //? Coordinadores/Admins que pueden evaluar (Responsables)
            $evaluadores = $this->userModel->consultarEvaluadores();
            
            //? Instrumentos disponibles
            $instrumentos = $this->instrumentoModel->consultarTodos();
            
            //? Periodos disponibles
            $periodos = $this->periodoModel->consultarTodos();

            //? 2. Cargar la vista del formulario de asignación
            include_once "app/views/aplicacion/form_asignar.php";
        }

        
        //* Método para procesar el formulario y crear un registro de evaluación pendiente (asignación).
        public function guardarAsignacion() {
            //? Válidar que se haya enviado el formulario
            if (isset($_POST['asignar_evaluacion'])) {
                
                //? 1. Capturar los IDs del formulario
                $id_instrumento = (int)$_POST['id_instrumento'];
                $id_profesor_evaluado = (int)$_POST['id_profesor_evaluado'];
                $id_periodo = (int)$_POST['id_periodo'];
                $id_evaluador_responsable = (int)$_POST['id_evaluador_responsable'];

                //? 2. Llamar al modelo para registrar la asignación inicial
                $exito = $this->aplicacionModel->crearAsignacion(
                    $id_instrumento,
                    $id_profesor_evaluado,
                    $id_periodo,
                    $id_evaluador_responsable
                );

                //? 3. Redirigir según el resultado
                if ($exito) {
                    //? Redirige a la lista de aplicaciones
                    header("Location: index.php?controlador=aplicacion&accion=consultarAplicaciones&exito_asignacion=1");
                    exit();
                } else {
                    die("Error al guardar la asignación pendiente.");
                }
            } else {
                //? Si acceden directo sin POST, mostrar el formulario de asignación
                $this->asignar();
            }
        }


        
        //* Método para obtener la lista de todas las evaluaciones (pendientes y completadas).
        public function consultarAplicaciones() {
            //? 1. Obtener la lista de evaluaciones del modelo
            $evaluaciones = $this->aplicacionModel->consultarAplicaciones();
            
            include_once "app/views/aplicacion/consultarAplicaciones.php";
        }


        //* Método para mostrar el formulario de llenado (aplicación) de una evaluación específica.
        public function aplicar() {
            //? Validar que se reciba el ID de la aplicación por GET
            if (!isset($_GET['id_app'])) { die("Error: Se requiere un ID de aplicación para evaluar."); }
            $id_aplicacion = (int)$_GET['id_app'];
            
            //? Obtener los datos generales de la aplicación
            $aplicacion_data = $this->aplicacionModel->consultarPorID($id_aplicacion);
            
            //? Validar que la aplicación exista y esté pendiente de llenar
            if (!$aplicacion_data || $aplicacion_data['estado'] !== 'pendiente') {
                die("Error: Evaluación no encontrada o ya ha sido completada.");
            }
            
            //? Obtener los rubros para el instrumento asociado
            $rubros = $this->rubroModel->getRubrosPorInstrumento($aplicacion_data['id_instrumento']);
            
            //? Cargar datos del evaluador
            $evaluador_data = $this->userModel->consultarPorID($aplicacion_data['id_evaluador']); 
            
            //? Cargar listas para los SELECTS de metadatos (materia y cuatrimestre)
            $materias_disponibles = $this->materiaModel->consultarTodasMaterias(); 
            $cuatrimestres_disponibles = $this->aplicacionModel->getCuatrimestres();

            //? Arreglo de datos para pasar a la vista
            $datos = [
                'aplicacion' => $aplicacion_data,
                'rubros' => $rubros,
                'evaluador' => $evaluador_data,
                'materias_lista' => $materias_disponibles,
                'cuatrimestres_lista' => $cuatrimestres_disponibles
            ];

            include_once "app/views/aplicacion/llenar.php"; 
        }
        
        //* Método para procesar y guardar los resultados finales de la aplicación (llenado).
        public function guardarAplicacion() {
            
            //? Validar que la acción provenga del botón de guardar
            if (!isset($_POST['guardar_aplicacion'])) { header("Location: index.php?controlador=acceso&accion=panelPrincipal"); exit(); }

            $id_aplicacion = 0;
            //? Capturar ID de aplicación
            if (isset($_POST['id_aplicacion'])) { $id_aplicacion = (int)$_POST['id_aplicacion']; }
            if ($id_aplicacion === 0) { die(" Error de seguridad: ID de aplicación faltante o inválido."); }

            //? 1. Capturar datos del formulario
            $observaciones_generales = trim($_POST['observaciones_generales'] ?? '');
            
            //? Captura de metadatos (materia, cuatrimestre y fecha)
            $nombre_materia = trim($_POST['asignatura'] ?? ''); 
            $cuatrimestre = trim($_POST['cuatrimestre'] ?? '');
            $fecha_evaluacion = trim($_POST['fecha_evaluacion'] ?? '');
            //? Captura de puntajes y comentarios por rubro (arrays asociativos)
            $puntajes_por_rubro = $_POST['puntaje'] ?? []; 
            $comentarios_por_rubro = $_POST['comentarios'] ?? []; 
            

            if (empty($puntajes_por_rubro)) { die("Error: No se recibieron puntajes para guardar."); }
            
            //? 2. Obtener los IDs de rubro directamente de las claves del array
            $rubro_ids = array_keys($puntajes_por_rubro);
            $estado_final = 'completado';
            
            //? 3. Calcular Puntaje Total
            $puntaje_total = 0.0;
            foreach($puntajes_por_rubro as $p) {
                $puntaje_total += (float)$p; 
            }

            //? 4. Iniciar Transacción (Para asegurar que todo el guardado sea atómico)
            $this->aplicacionModel->connection->begin_transaction(); 

            try {
                //? A. ACTUALIZAR el encabezado (aplicacioninstrumento) con puntaje, estado y metadatos
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
                    die("FALLO DEBUG 1: La actualización del encabezado falló. Error MySQL: " . $this->aplicacionModel->connection->error); 
                }

                //? B. Guardar los detalles (respuesta por rubro)
                foreach ($rubro_ids as $id_rubro) {
                    $id_rubro_int = (int)$id_rubro;
                    
                    //? Obtener el puntaje y comentario para el rubro actual
                    $puntaje_rubro = (float)($puntajes_por_rubro[$id_rubro] ?? 0.0);
                    $comentario_rubro = trim($comentarios_por_rubro[$id_rubro] ?? '');

                    //? Llama a la función que elimina e inserta la respuesta
                    $success_respuesta = $this->aplicacionModel->guardarRespuesta($id_aplicacion, $id_rubro_int, $puntaje_rubro, $comentario_rubro);
                    
                    if (!$success_respuesta) {
                        $this->aplicacionModel->connection->rollback();
                        die("FALLO DEBUG 2: Falló al guardar el rubro ID: {$id_rubro}. Error MySQL: " . $this->aplicacionModel->connection->error); 
                    }
                }

                //? C. Commit de la transacción si todas las operaciones fueron exitosas
                $this->aplicacionModel->connection->commit();
                header("Location: index.php?controlador=aplicacion&accion=consultarAplicaciones&exito_completado=1");
                exit();

            } catch (Exception $e) {
                //? Si hay una excepción, revierte todos los cambios
                $this->aplicacionModel->connection->rollback();
                die("FALLO DEBUG 3 (EXCEPCIÓN): " . $e->getMessage()); 
            }
        }

        //* Método para obtener y mostrar la vista de detalle de una evaluación ya completada.
        public function verDetalle() {
            //? Validar que se reciba el ID de la aplicación
            if (!isset($_GET['id_app'])) { die("Error: Se requiere un ID de aplicación para ver el detalle."); }


            $id_aplicacion = (int)$_GET['id_app'];
            //? 1. Obtener todos los detalles (incluyendo respuestas y datos generales)
            $datos = $this->aplicacionModel->obtenerDetallesCompletos($id_aplicacion);
            
            //? Validar que la evaluación esté completada
            if (!$datos || $datos['estado'] !== 'completado') {
                die("Error: La evaluación no existe o no ha sido completada.");
            }
            //? 2. Cargar la vista de detalle
            include_once "app/views/aplicacion/verDetalle.php";

        }

    }