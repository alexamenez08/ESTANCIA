<?php

   
    include_once "app/models/AcademiaModel.php";
     include_once "config/db_connection.php";
    
    class AcademiaController {

        private $model;
        private $connection;


        public function __construct($connection) {
            $this->connection = $connection;
            $this->model = new AcademiaModel($connection);
        }

        public function insertarAcademia() {

            if (isset($_POST['registrar_academia'])) {
                $nombre = trim($_POST['nombre']);
                $siglas = trim($_POST['siglas']); 

                $insert = $this->model->insertarAcademia($nombre, $siglas);

                if ($insert) {
                    echo "Academia '{$nombre}' registrada exitosamente.";
                } else {
                    echo "Error al registrar la academia. Verifica que las siglas sean únicas.";
                }

            }

            include_once "app/views/academia/insertar_academia.php";
        }

        public function consultarAcademias() {
            $academias = $this->model->consultarAcademias();

            include_once "app/views/academia/consultar_academias.php";
        }
        
        public function editarAcademia(){
            if(isset($_POST['guardar_academia'])){ 
                
                $id = (int)($_POST['id']); 

                $nombre = trim($_POST['nombre']);
                $siglas = trim($_POST['siglas']);
                
                $update = $this -> model -> editarAcademia($id,$nombre,$siglas);

                if($update){
                    header("Location: index.php?controlador=academia&accion=consultarAcademias");
                    exit();
                }else{
                    die("Error al actualizar");
                }
            }
                if(isset($_GET['id'])){
                    $id_browser = $_GET['id'];

                    $row = $this -> model -> buscarPorId($id_browser);

                    if(!$row){
                        die ("Error. Usuario no encontrado");
                    }
                }

                include_once "app/views/academia/actualizar_academia.php";

                return;
        }

        public function eliminarAcademia(){
            if(isset($_GET['id'])){
                $id_eliminar = (int)$_GET['id'];

                $delete = $this -> model -> eliminarAcademia($id_eliminar);

                if($delete){
                    header("Location: index.php?controlador=academia&accion=consultarAcademias");
                    exit();
                }else{
                    header("Location: index.php?controlador=academia&accion=consultarAcademias");
                    exit();
                }
            }
        }

        
    }