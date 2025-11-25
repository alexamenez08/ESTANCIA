<?php

    //? Incluir el modelo de Academia y la conexión a la base de datos
    include_once "app/models/AcademiaModel.php";
    include_once "config/db_connection.php";
    
    //! Clase controladora para gestionar las acciones relacionadas con las Academias
    //! (Insertar, Consultar, Editar y Eliminar).
    class AcademiaController {

        private $model;
        private $connection; //? La conexión se almacena pero no es usada directamente, solo para el constructor.

        public function __construct($connection) {
            //? Almacena la conexión 
            $this->connection = $connection;
            //? Inicializa la instancia del modelo de Academia
            $this->model = new AcademiaModel($connection);
        }

        //* Método para procesar la inserción de una nueva academia.
        public function insertarAcademia() {
            //? Verifica si se envió el formulario (botón 'registrar_academia' presionado)
            if (isset($_POST['registrar_academia'])) {
                //? Captura y sanea los campos
                $nombre = trim($_POST['nombre']);
                $siglas = trim($_POST['siglas']); 

                //? Llama al modelo para insertar los datos
                $insert = $this->model->insertarAcademia($nombre, $siglas);

                //? Muestra el resultado de la operación
                if ($insert) {
                    echo "Academia '{$nombre}' registrada exitosamente.";
                } else {
                    echo "Error al registrar la academia. Verifica que las siglas sean únicas.";
                }
            }

            //? Carga la vista del formulario de inserción
            include_once "app/views/academia/insertar_academia.php";
        }

        //* Método para obtener y mostrar la lista de todas las academias.
        public function consultarAcademias() {
            //? Obtiene todos los registros de academias del modelo
            $academias = $this->model->consultarAcademias();

            //? Carga la vista de consulta
            include_once "app/views/academia/consultar_academias.php";
        }
        
        //* Método para obtener los datos de una academia o procesar su actualización.
        public function editarAcademia(){
            //? Bloque para procesar el formulario de actualización (si se envió el POST)
            if(isset($_POST['guardar_academia'])){ 
                
                //? Captura y convierte el ID a entero
                $id = (int)($_POST['id']); 

                //? Captura y sanea los campos a actualizar
                $nombre = trim($_POST['nombre']);
                $siglas = trim($_POST['siglas']);
                
                //? Llama al modelo para ejecutar la actualización
                $update = $this -> model -> editarAcademia($id,$nombre,$siglas);

                //? Redirige según el resultado de la operación
                if($update){
                    header("Location: index.php?controlador=academia&accion=consultarAcademias");
                    exit();
                }else{
                    die("Error al actualizar");
                }
            }
            
            //? Bloque para cargar los datos de la academia para el formulario (si se recibió el GET ID)
            if(isset($_GET['id'])){
                $id_browser = $_GET['id'];

                //? Llama al modelo para buscar la academia por su ID
                $row = $this -> model -> buscarPorId($id_browser);

                //? Verifica si se encontró la academia
                if(!$row){
                    die ("Error. Usuario no encontrado");
                }
            }

            //? Carga la vista de actualización
            include_once "app/views/academia/actualizar_academia.php";

            //? Finaliza la ejecución del método (el 'return;' original)
            return;
        }

        //* Método para eliminar una academia.
        public function eliminarAcademia(){
            //? Verifica si se pasó el ID por URL (GET)
            if(isset($_GET['id'])){
                //? Captura y convierte el ID a entero
                $id_eliminar = (int)$_GET['id'];

                //? Llama al modelo para eliminar
                $delete = $this -> model -> eliminarAcademia($id_eliminar);

                //? Redirige a la consulta de academias (con o sin éxito/error visible)
                if($delete){
                    header("Location: index.php?controlador=academia&accion=consultarAcademias");
                    exit();
                }else{
                    //? Si falla (ej. por llave foránea), se redirige igual para mostrar la lista
                    header("Location: index.php?controlador=academia&accion=consultarAcademias");
                    exit();
                }
            }
        }

    }