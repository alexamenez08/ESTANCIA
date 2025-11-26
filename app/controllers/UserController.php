<?php
    //? Incluir el modelo de Usuario/Profesor
    include_once "app/models/UserModel.php";
    //? Incluir el modelo de Materia (puede que no se use directamente, pero se mantiene)
    include_once "app/models/MateriaModel.php";
    include_once "config/db_connection.php";
    

    //! Clase controladora para gestionar el CRUD y las consultas de usuarios/profesores (Administrativo).
    class UserController{

        private $model;

        //* Constructor de la clase
        public function __construct($connection){

            //? Inicializa el modelo de Usuario
            $this -> model = new UserModel($connection);

        }

        //* Método para obtener la información del formulario e insertar un nuevo profesor.
        public function insertarUsuario(){

            //* Válidar que se haya enviado el formulario (botón 'enviar')
            if(isset($_POST['enviar'])){
                //? Captura y sanea los campos necesarios del formulario
                $matricula = trim($_POST['matricula']); 
                $nombre = trim($_POST['nombre']);
                $apellido_pa = trim($_POST['apellido_pa']); 
                $apellido_ma = trim($_POST['apellido_ma']); 
                $sexo = trim($_POST['sexo']);
                //? Hashea la clave usando BCRYPT para seguridad
                $pass = password_hash($_POST['pass'], PASSWORD_BCRYPT);
                $rol = trim($_POST['rol']); 
                $grado_academico = trim($_POST['grado_academico']); 
                

                //* Llamar al método del modelo para la inserción
                $insert = $this -> model -> insertarProfesor($matricula, $nombre, $apellido_pa, 
                    $apellido_ma, $sexo, $pass,$rol,$grado_academico);
                
                //* Verificar el resultado
                if($insert){
                    echo "<br>Registro exitoso para {$nombre} {$apellido_pa} {$apellido_ma}";
                }else{
                    
                    echo"<br>Error al registrar. Revisa que el ID de profesor y la Matrícula sean únicos.";
                }

            }

            //* Incluir la vista del formulario
            include_once "app/views/usuario/form_insert.php";

        }

        //* Método para obtener y mostrar la lista de todos los usuarios/profesores.
        public function consultarUsuarios(){
            //? Obtiene todos los profesores del modelo
            $usuarios = $this -> model -> consultarProfesores();
            //? Carga la vista de consulta
            include "app/views/usuario/consult.php";

        }

        //* Método para precargar datos y procesar la actualización de un usuario/profesor.
        public function actualizarUsuario(){

            //* Bloque para procesar el formulario de actualización (POST)
            if(isset($_POST['guardar_cambios'])){ 
                
                $id = (int)($_POST['id']); 

                //? Captura los datos actualizados
                $matricula = trim($_POST['matricula']);
                $nombre = trim($_POST['nombre']);
                $apellido_pa = trim($_POST['apellido_pa']);
                $apellido_ma = trim($_POST['apellido_ma']);
                $sexo = trim($_POST['sexo']);
                $grado_academico = trim($_POST['grado_academico']);
                
                //? Llama al modelo para ejecutar la actualización
                $update = $this -> model -> actualizarUsuario($id,$matricula,$nombre,$apellido_pa,$apellido_ma,$sexo,$grado_academico);

                //? Redirige según el resultado
                if($update){
                    header("Location: index.php?controlador=user&accion=consultarUsuarios");
                    exit();
                }else{
                    die("Error al actualizar");
                }
            }
            //* Bloque para precargar los datos para el formulario de edición (GET)
            if(isset($_GET['id'])){
                $id_browser = $_GET['id'];

                //? Consulta los datos del profesor por ID
                $row = $this -> model -> consultarPorID($id_browser);

                if(!$row){
                    die ("Error. Usuario no encontrado");
                }
            }

            //? Carga la vista de edición
            include_once "app/views/usuario/edit.php";

            return;
        }

        //* Método para eliminar un usuario/profesor.
        public function eliminarUsuario(){
            
            //? SEGURIDAD DE BACKEND: Validar que solo un Administrador pueda eliminar
            if ($_SESSION['rol_usuario'] != 'Administrador') {
                //? Mensaje de acceso denegado y redirección
                echo "<script>alert('Acceso denegado: No tienes permisos para eliminar usuarios.'); window.location.href='index.php?controlador=user&accion=consultarUsuarios';</script>";
                exit(); 
            }

            //? Si pasa la validación, procedemos con la eliminación
            if(isset($_GET['id'])){
                $id_eliminar = (int)$_GET['id'];

                //? Llama al modelo para eliminar el profesor
                $delete = $this -> model -> eliminarProfesor($id_eliminar);

                //? Redirige a la consulta de usuarios con mensaje de resultado
                if($delete){
                    header("Location: index.php?controlador=user&accion=consultarUsuarios&msg=eliminado");
                    exit();
                }else{
                    header("Location: index.php?controlador=user&accion=consultarUsuarios&msg=error");
                    exit();
                }
            }
        }
        
        //* Método para mostrar la vista de consulta de profesores con filtros por Academia.
        public function consultarPorAcademia() {
            
            //? 1. Obtener todas las academias para el <select> de filtros
            $academias = $this->model->consultarAcademias(); 

            //? 2. Obtener los filtros de la URL (si se enviaron)
            $filtro_academia_id = $_GET['academia'] ?? null;
            $filtro_termino = $_GET['termino'] ?? null;

            //? 3. Obtener los profesores filtrados usando la consulta del modelo
            $profesores = $this->model->consultarProfesoresPorFiltro($filtro_academia_id, $filtro_termino);

            //? 4. Cargar la vista y pasarle los datos
            include "app/views/usuario/consulta_por_academia.php";
        }

        //* Método para mostrar la vista de consulta de profesores con filtro por Materia.
        public function consultarPorMateria() {
            
            //? Cargar el modelo de Materia (asumiendo que tiene la conexión)
            $materiaModel = new MateriaModel($this->model->connection);
            
            //? 1. Obtener la lista de todas las materias para el SELECT de filtros
            $materias_lista = $materiaModel->consultarTodasMaterias();
            
            //? 2. Obtener el filtro de la URL
            $filtro_id_materia = $_GET['id_materia'] ?? null;

            //? 3. Obtener los profesores filtrados
            $profesores = $this->model->consultarProfesoresPorMateria($filtro_id_materia);

            //? 4. Cargar la vista y pasarle los datos
            include "app/views/usuario/consulta_por_materia.php";
        }
        
    }
