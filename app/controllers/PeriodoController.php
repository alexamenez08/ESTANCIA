<?php

    include_once "app/models/PeriodoModel.php";
    include_once "config/db_connection.php";

    //!  Clase controladora para gestionar las acciones relacionadas con los Períodos académicos (Consultar, Insertar, Editar y Eliminar).
    class PeriodoController{

        private $model;

        public function __construct($connection){
            //? Inicializa la instancia del modelo de Periodo
            $this -> model = new PeriodoModel($connection);
        }

        //* Método para obtener y mostrar todos los períodos.
        public function consultarPeriodos() {
            //? Obtener todos los períodos de la base de datos
            $periodos = $this->model->consultarTodos(); 

            //? Cargar la vista de consulta
            include_once "app/views/periodo/consultar_periodos.php";
        }
        
        //* Método para procesar la inserción de un nuevo período.
        public function insertarPeriodo(){
            //? Verificar si se envió el formulario (botón 'enviar_periodo' presionado)
            if(isset($_POST['enviar_periodo'])){
                //? Capturar y sanear los datos del formulario
                $nombre = trim($_POST['nombre']);
                $fecha_inicio = trim($_POST['fecha_inicio']); 
                $fecha_fin = trim($_POST['fecha_fin']); 
                
                //? Validar que la Fecha de finalización no sea anterior a la de inicio (FN. 7)
                if (strtotime($fecha_fin) < strtotime($fecha_inicio)) {
                    $error_mensaje = "La Fecha de finalización no puede ser anterior a la Fecha de inicio.";
                } else {
                    //? Llamar al modelo para intentar la inserción
                    $insert = $this -> model -> insertarPeriodo($nombre, $fecha_inicio, $fecha_fin);
                    
                    //? Verificar el resultado de la operación
                    if($insert){
                        $exito_mensaje = "Periodo {$nombre} registrado correctamente.";
                    }else{
                        $error_mensaje = "Error al registrar el periodo. Revise logs de MySQL.";
                    }
                }
            }

            //? Incluir la vista del formulario de inserción
            include_once "app/views/periodo/insertar_periodo.php";
        }

        //* Método para obtener datos del período o procesar su actualización.
        public function editarPeriodo() {
            //? Validar que se haya pasado un ID por URL
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                die("Error: Se requiere el ID del período para editar.");
            }
            $id_periodo = (int)$_GET['id'];

            //? Verificar si se ha enviado el formulario de actualización
            if (isset($_POST['actualizar_periodo'])) {
                //? Capturar datos para la actualización
                $nombre = trim($_POST['nombre']);
                $fecha_inicio = trim($_POST['fecha_inicio']);
                $fecha_fin = trim($_POST['fecha_fin']);

                //? Validar que la Fecha de finalización no sea anterior a la de inicio
                if (strtotime($fecha_fin) < strtotime($fecha_inicio)) {
                    $error_mensaje = "La Fecha de finalización no puede ser anterior a la Fecha de inicio.";
                } else {
                    //? Llamar al modelo para actualizar
                    $update = $this->model->actualizarPeriodo($id_periodo, $nombre, $fecha_inicio, $fecha_fin);
                    
                    //? Redirigir si la actualización fue exitosa
                    if ($update) {
                        header("Location: index.php?controlador=periodo&accion=consultarPeriodos&exito_update=1");
                        exit();
                    } else {
                        $error_mensaje = "Error al actualizar el período.";
                    }
                }
            }

            //? Obtener los datos del período para precargar el formulario de edición
            $periodo_data = $this->model->consultarPorID($id_periodo);

            //? Verificar si el período existe
            if (!$periodo_data) {
                die("Error: Período no encontrado.");
            }

            //? Incluir la vista de edición, enviando los datos obtenidos
            include_once "app/views/periodo/editar_periodo.php";
        }

        //* Método para eliminar un período de la base de datos.
        public function eliminarPeriodo() {
            //? Validar que se haya pasado un ID por URL
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                die("Error: Se requiere el ID del período para eliminar.");
            }
            
            $id_periodo = (int)$_GET['id'];
            
            //? Llama al modelo para intentar la eliminación
            $delete = $this->model->eliminarPeriodo($id_periodo);

            if ($delete) {
                //? Éxito: Redirigir a la consulta con mensaje
                header("Location: index.php?controlador=periodo&accion=consultarPeriodos&exito_delete=1");
                exit();
            } else {
                //? Fallo: Redirigir con mensaje de error (posiblemente por llaves foráneas)
                header("Location: index.php?controlador=periodo&accion=consultarPeriodos&error_delete=1");
                exit();
            }
        }
        
    }