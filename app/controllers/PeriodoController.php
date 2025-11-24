<?php

    include_once "app/models/PeriodoModel.php";
    include_once "config/db_connection.php";

    class PeriodoController{

        private $model;

        public function __construct($connection){
            $this -> model = new PeriodoModel($connection);
        }

        // Acción: index.php?controlador=periodo&accion=consultarPeriodos
        public function consultarPeriodos() {
            // Obtener todos los períodos del modelo
            $periodos = $this->model->consultarTodos(); 

            // Cargar la vista de consulta
            include_once "app/views/periodo/consultar_periodos.php";
        }
        
        // Acción: index.php?controlador=periodo&accion=insertarPeriodo
        public function insertarPeriodo(){
            //* Válidar que el botón sea diferente de nulo
            if(isset($_POST['enviar_periodo'])){
                // Captura todos los campos necesarios del formulario
                $nombre = trim($_POST['nombre']);
                $fecha_inicio = trim($_POST['fecha_inicio']); 
                $fecha_fin = trim($_POST['fecha_fin']); 
                
                // Validación básica de fechas (Fecha fin debe ser posterior o igual a Fecha inicio)
                if (strtotime($fecha_fin) < strtotime($fecha_inicio)) {
                    $error_mensaje = "La Fecha de finalización no puede ser anterior a la Fecha de inicio.";
                } else {
                    $insert = $this -> model -> insertarPeriodo($nombre, $fecha_inicio, $fecha_fin);
                    
                    //* Verificar el resultado
                    if($insert){
                        $exito_mensaje = "Periodo {$nombre} registrado correctamente.";
                    }else{
                        $error_mensaje = "Error al registrar el periodo. Revise logs de MySQL.";
                    }
                }
            }

            //* Incluir la vista (pasando mensajes de error/éxito si existen)
            include_once "app/views/periodo/insertar_periodo.php";
        }

        // Acción: index.php?controlador=periodo&accion=editarPeriodo&id=[ID]
        public function editarPeriodo() {
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                die("Error: Se requiere el ID del período para editar.");
            }
            $id_periodo = (int)$_GET['id'];

            if (isset($_POST['actualizar_periodo'])) {
                // Procesa la actualización
                $nombre = trim($_POST['nombre']);
                $fecha_inicio = trim($_POST['fecha_inicio']);
                $fecha_fin = trim($_POST['fecha_fin']);

                // Validación básica de fechas
                if (strtotime($fecha_fin) < strtotime($fecha_inicio)) {
                    $error_mensaje = "La Fecha de finalización no puede ser anterior a la Fecha de inicio.";
                } else {
                    $update = $this->model->actualizarPeriodo($id_periodo, $nombre, $fecha_inicio, $fecha_fin);
                    if ($update) {
                        header("Location: index.php?controlador=periodo&accion=consultarPeriodos&exito_update=1");
                        exit();
                    } else {
                        $error_mensaje = "Error al actualizar el período.";
                    }
                }
            }

            // Obtener datos del período para precargar el formulario
            $periodo_data = $this->model->consultarPorID($id_periodo);

            if (!$periodo_data) {
                die("Error: Período no encontrado.");
            }

            include_once "app/views/periodo/editar_periodo.php";
        }

        // Acción: index.php?controlador=periodo&accion=eliminarPeriodo&id=[ID]
        public function eliminarPeriodo() {
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                die("Error: Se requiere el ID del período para eliminar.");
            }
            
            $id_periodo = (int)$_GET['id'];
            
            // Intenta eliminar
            $delete = $this->model->eliminarPeriodo($id_periodo);

            if ($delete) {
                // Éxito: Redirigir a la consulta con mensaje
                header("Location: index.php?controlador=periodo&accion=consultarPeriodos&exito_delete=1");
                exit();
            } else {
                // Fallo: Redirigir a la consulta con mensaje de error (puede ser una FK constraint)
                header("Location: index.php?controlador=periodo&accion=consultarPeriodos&error_delete=1");
                exit();
            }
        }
    }
?>