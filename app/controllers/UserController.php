<?php
    //* Incluir el modelo y la conexión a la BD
    include_once "app/models/UserModel.php";
    include_once "app/models/MateriaModel.php";
    include_once "config/db_connection.php";
    

    //* Clase del controlador
    class UserController{

        private $model;

        //* Constructor de la clase
        public function __construct($connection){

            $this -> model = new UserModel($connection);

        }

        //* Método para obtener la información del formulario e insertar
        public function insertarUsuario(){

            //* Válidar que el botón sea diferente de nulo
            if(isset($_POST['enviar'])){
                // Captura todos los campos necesarios del formulario
                $matricula = trim($_POST['matricula']); 
                $nombre = trim($_POST['nombre']);
                $apellido_pa = trim($_POST['apellido_pa']); 
                $apellido_ma = trim($_POST['apellido_ma']); 
                $sexo = trim($_POST['sexo']);  
                $pass = password_hash($_POST['pass'], PASSWORD_BCRYPT);
                $rol = trim($_POST['rol']); 
                $grado_academico = trim($_POST['grado_academico']); 
                

                //* Llamar al método del modelo. Ahora pasamos 9 campos, incluido id_profesor.
                $insert = $this -> model -> insertarProfesor($matricula, $nombre, $apellido_pa, 
                    $apellido_ma,  $sexo, $pass,$rol,$grado_academico);
                
                //* Verificar el resultado
                if($insert){
                    echo "<br>Registro exitoso para {$nombre} {$apellido_pa} {$apellido_ma}";
                }else{
                    
                    echo"<br>Error al registrar. Revisa que el ID de profesor y la Matrícula sean únicos.";
                }

            }

            //* Incluir la vista
            include_once "app/views/usuario/form_insert.php";

        }

        //* Método para consultar profesores
        public function consultarUsuarios(){
            $usuarios = $this -> model -> consultarProfesores();
            include "app/views/usuario/consult.php";

        }

        //* Método para consultar por ID (Matrícula)
        public function actualizarUsuario(){

            if(isset($_POST['guardar_cambios'])){ 
                
                $id = (int)($_POST['id']); 

                $matricula = trim($_POST['matricula']);
                $nombre = trim($_POST['nombre']);
                $apellido_pa = trim($_POST['apellido_pa']);
                $apellido_ma = trim($_POST['apellido_ma']);
                $sexo = trim($_POST['sexo']);
                $grado_academico = trim($_POST['grado_academico']);
                
                $update = $this -> model -> actualizarUsuario($id,$matricula,$nombre,$apellido_pa,$apellido_ma,$sexo,$grado_academico);

                if($update){
                    header("Location: index.php?controlador=user&accion=consultarUsuarios");
                    exit();
                }else{
                    die("Error al actualizar");
                }
            }
                if(isset($_GET['id'])){
                    $id_browser = $_GET['id'];

                    $row = $this -> model -> consultarPorID($id_browser);

                    if(!$row){
                        die ("Error. Usuario no encontrado");
                    }
                }

                include_once "app/views/usuario/edit.php";

                return;
        }

        public function eliminarUsuario(){
            
            // SEGURIDAD DE BACKEND: Validar Rol
            // Si NO es administrador, lo expulsamos de esta función.
            if ($_SESSION['rol_usuario'] != 'Administrador') {
                // Opcional: Puedes mandar un mensaje de error
                echo "<script>alert('Acceso denegado: No tienes permisos para eliminar usuarios.'); window.location.href='index.php?controlador=user&accion=consultarUsuarios';</script>";
                exit(); // Detenemos el script aquí
            }

            // Si pasa la validación, procedemos con la eliminación normal
            if(isset($_GET['id'])){
                $id_eliminar = (int)$_GET['id'];

                $delete = $this -> model -> eliminarProfesor($id_eliminar);

                if($delete){
                    header("Location: index.php?controlador=user&accion=consultarUsuarios&msg=eliminado");
                    exit();
                }else{
                    header("Location: index.php?controlador=user&accion=consultarUsuarios&msg=error");
                    exit();
                }
            }
        }
    
    /** Consulta de profesores por academia.
     * Muestra la vista con filtros y resultados. */
    public function consultarPorAcademia() {
        
        // 1. Obtener todas las academias para el <select>
        // (Llamando a la función que acabamos de añadir al UserModel)
        $academias = $this->model->consultarAcademias(); 

        // 3. Obtener los filtros de la URL (si se enviaron)
        $filtro_academia_id = $_GET['academia'] ?? null;
        $filtro_termino = $_GET['termino'] ?? null;

        // 4. Obtener los profesores filtrados usando la nueva consulta (M-N)
        $profesores = $this->model->consultarProfesoresPorFiltro($filtro_academia_id, $filtro_termino);

        // 5. Cargar la vista y pasarle los datos
        // (Asegúrate de que la vista exista en esta ruta)
        include "app/views/usuario/consulta_por_academia.php";
    }

    /** Consulta de profesores por materia.
     */
    public function consultarPorMateria() {
        
        // Cargar el modelo de Materia para obtener la lista de todas las materias
        $materiaModel = new MateriaModel($this->model->connection);
        
        // 1. Obtener la lista de todas las materias para el SELECT
        $materias_lista = $materiaModel->consultarTodasMaterias(); // Asumo este método
        
        // 2. Obtener el filtro de la URL (buscamos 'id_materia' en lugar de 'materia')
        $filtro_id_materia = $_GET['id_materia'] ?? null;

        // 3. Obtener los profesores filtrados
        $profesores = $this->model->consultarProfesoresPorMateria($filtro_id_materia);

        // 4. Cargar la vista y pasarle los datos
        // Pasamos la lista completa de materias a la vista
        include "app/views/usuario/consulta_por_materia.php";
    }



    }
