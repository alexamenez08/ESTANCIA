<?php
include_once "app/models/UserModel.php";
include_once "app/models/MateriaModel.php"; 
include_once "config/db_connection.php";

class ProfesorPanelController {
    private $userModel;
    private $materiaModel;

    public function __construct($connection) {
        $this->userModel = new UserModel($connection);
        $this->materiaModel = new MateriaModel($connection); 
    }
    
    // VER PERFIL PROPIO
    public function verPerfil() {
        // Obtenemos el ID del profesor logueado
        $id_profesor_logueado = $_SESSION['id_profesor'] ?? 0; 
        
        if ($id_profesor_logueado === 0) {
            // Esto solo ocurre si la sesión no está bien configurada
            die("Error: No se encontró la sesión del profesor. Por favor, vuelva a iniciar sesión.");
        }

        // Obtener los datos del profesor logueado
        $profesor_data = $this->userModel->consultarPorID($id_profesor_logueado);
        
        // Obtener la carga académica (materias)
        $materias_asignadas = $this->materiaModel->consultarMateriasPorProfesor($id_profesor_logueado); 
        
        // Cargar la vista de perfil (solo lectura)
        include_once "app/views/profesor/perfil.php";
    }

    // SEGUIMIENTO DE EVALUACIONES
    public function seguimientoEvaluaciones() {
        $id_profesor_logueado = $_SESSION['id_profesor'] ?? 0; // Si no existe, es 0
        if ($id_profesor_logueado === 0) {
            die("Error: Sesión inválida."); 
        }

        //la función consultarEvaluacionesPropias está en UserModel
        $evaluaciones = $this->userModel->consultarEvaluacionesPropias($id_profesor_logueado);

        include_once "app/views/profesor/seguimiento.php";
    }
}