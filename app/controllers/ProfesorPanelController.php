<?php
    //? Inclusión de los Modelos y la conexión a la base de datos
    include_once "app/models/UserModel.php";
    include_once "app/models/MateriaModel.php"; 
    include_once "config/db_connection.php";

    //! Clase controladora para gestionar las acciones y vistas disponibles para el usuario
    //! con rol de Profesor (Ver Perfil y Seguimiento de sus evaluaciones).
    
    class ProfesorPanelController {
        private $userModel;
        private $materiaModel;

        public function __construct($connection) {
            //? Inicializa el modelo para acceder a datos de usuario/profesor
            $this->userModel = new UserModel($connection);
            //? Inicializa el modelo para acceder a datos de materias
            $this->materiaModel = new MateriaModel($connection); 
        }
        
        //* Método para obtener y mostrar el perfil y la carga académica del profesor logueado.
        public function verPerfil() {
            //? Obtenemos el ID del profesor logueado desde la variable de sesión
            $id_profesor_logueado = $_SESSION['id_profesor'] ?? 0; 
            
            //? Validación de seguridad de la sesión
            if ($id_profesor_logueado === 0) {
                die("Error: No se encontró la sesión del profesor. Por favor, vuelva a iniciar sesión.");
            }

            //? Obtener los datos personales del profesor logueado
            $profesor_data = $this->userModel->consultarPorID($id_profesor_logueado);
            
            //? Obtener la carga académica (materias asignadas) del profesor
            $materias_asignadas = $this->materiaModel->consultarMateriasPorProfesor($id_profesor_logueado); 
            
            //? Cargar la vista de perfil (solo lectura)
            include_once "app/views/profesor/perfil.php";
        }

        //* Método para obtener y mostrar el historial de evaluaciones asignadas al profesor.
        public function seguimientoEvaluaciones() {
            //? Obtenemos el ID del profesor logueado desde la variable de sesión
            $id_profesor_logueado = $_SESSION['id_profesor'] ?? 0;
            
            //? Validación de seguridad de la sesión
            if ($id_profesor_logueado === 0) {
                die("Error: Sesión inválida."); 
            }

            //? Llama al modelo de usuario para obtener las evaluaciones donde el profesor es el evaluado
            $evaluaciones = $this->userModel->consultarEvaluacionesPropias($id_profesor_logueado);

            //? Carga la vista de seguimiento
            include_once "app/views/profesor/seguimiento.php";
        }

    }