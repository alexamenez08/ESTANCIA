<?php
include_once "app/models/MateriaModel.php";

class MateriaController {
    private $model;

    public function __construct($connection){
        $this->model = new MateriaModel($connection);
    }

    public function consultarMaterias(){
        $materias = $this->model->consultarMaterias();
        include "app/views/materia/consulta_materia.php";
    }

    public function insertarMateria(){
        $error = ""; 

        if(isset($_POST['registrar_materia'])){
            // 1. Captura de datos
            $nombre_materia = trim($_POST['nombre_materia']);
            $clave          = trim($_POST['clave']);
            $creditos       = (int)$_POST['creditos'];
            $id_academia    = $_POST['id_academia']; // Capturamos como string para validar
            $profesores     = isset($_POST['profesores']) ? $_POST['profesores'] : [];

            // --- 🚨 VALIDACIONES ---
            
            // 1. Validar que se seleccionó al menos un docente
            if (empty($profesores)) {
                $error = "Debe seleccionar al menos un docente para impartir esta materia.";
            } 
            
            // 2. 🔑 VALIDACIÓN: Validar que se seleccionó una academia
            elseif ($id_academia === '') { 
                 $error = "Debe seleccionar una academia válida para esta materia.";
            }

            // 3. Si no hay errores, proceder con el registro
            if (empty($error)) {
                // Convertimos a INT aquí, ya que sabemos que no es una cadena vacía
                $id_academia_db = (int)$id_academia; 

                // Llama al modelo sin el id_materia, asumiendo AUTO_INCREMENT
                $ok = $this->model->insertarMateria($nombre_materia, $clave, $creditos, $id_academia_db, $profesores);
                
                if($ok){
                    header("Location: index.php?controlador=materia&accion=consultarMaterias&exito=registro");
                    exit;
                } else {
                    $error = "No se pudo registrar la materia (Error en la base de datos).";
                }
            }
        }
        
        // 4. Recargar listas para la vista
        $academias  = $this->model->listarAcademias();
        $profesores = $this->model->listarProfesores();
        include "app/views/materia/insertar_materia.php";
    }

    public function editar(){
        if(isset($_POST['guardar_cambios'])){
            $id_materia     = (int)$_POST['id_materia'];
            $nombre_materia = trim($_POST['nombre_materia']);
            $clave          = trim($_POST['clave']);
            $creditos       = (int)$_POST['creditos'];
            $id_academia    = ($_POST['id_academia'] !== '') ? (int)$_POST['id_academia'] : null;
            $profesores     = isset($_POST['profesores']) ? $_POST['profesores'] : [];

            if (empty($profesores)) {
                $error = "Debe seleccionar al menos un docente para impartir esta materia.";
            } 
            
            // 2. Validar que se seleccionó una academia (si la regla lo requiere)
            // Si el valor es una cadena vacía, significa que se eligió "-- Sin academia --"
            elseif ($id_academia === '') {
                 $error = "Debe seleccionar una academia válida para esta materia.";
            }

            // 3. Si no hay errores, proceder con la actualización
            if (empty($error)) {
                $id_academia_db = ($id_academia !== '') ? (int)$id_academia : null;

                $ok = $this->model->actualizarMateria($id_materia, $nombre_materia, $clave, $creditos, $id_academia_db, $profesores);
                
                if($ok){
                    header("Location: index.php?controlador=materia&accion=consultarMaterias&exito=actualizado");
                    exit;
                } else {
                    $error = "No se pudo actualizar la materia.";
                }
            }
        
        }

        if(!isset($_GET['id'])) {
            // Si no hay ID en GET, pero sí venimos de un POST fallido, usamos el ID del POST
            $id_materia = isset($_POST['id_materia']) ? (int)$_POST['id_materia'] : 0;
            if ($id_materia === 0) die("Falta id de materia.");
        } else {
            $id_materia = (int)$_GET['id'];
        }

        $row = $this->model->obtenerMateria($id_materia);
        if(!$row) die("Materia no encontrada.");
        
        // Si la validación falla (POST), $row['id_academia'] aún tiene el valor anterior,
        // pero cargaremos las listas de nuevo para la vista.

        $academias = $this->model->listarAcademias();
        $profesores = $this->model->listarProfesores();
        $profesoresDeEsta = $this->model->profesoresDeMateria($id_materia);

        include "app/views/materia/actualizar_materia.php";
    }

    public function eliminar(){
        if(isset($_GET['id'])){
            $this->model->eliminarMateria((int)$_GET['id']);
        }
        header("Location: index.php?controlador=materia&accion=consultarMaterias");
        exit;
    }
}
