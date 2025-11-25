<?php
    //? Incluir el modelo de Materia
    include_once "app/models/MateriaModel.php";
    include_once "config/db_connection.php";

    //! Clase controladora para gestionar las acciones relacionadas con las Materias:
    //! Consultar, Insertar, Editar y Eliminar (CRUD), incluyendo la asignación de docentes y academia.
    class MateriaController {
        private $model;

        public function __construct($connection){
            //? Inicializa la instancia del modelo de Materia
            $this->model = new MateriaModel($connection);
        }

        //* Método para obtener y mostrar el listado de materias.
        public function consultarMaterias(){
            //? Obtiene la lista de materias con sus docentes y academia asociada
            $materias = $this->model->consultarMaterias();
            //? Carga la vista de consulta
            include "app/views/materia/consulta_materia.php";
        }

        //* Método para mostrar el formulario e insertar una nueva materia, incluyendo validaciones.
        public function insertarMateria(){
            $error = ""; 

            //? Válida que se haya enviado el formulario
            if(isset($_POST['registrar_materia'])){
                //? 1. Captura y sanea de datos
                $nombre_materia = trim($_POST['nombre_materia']);
                $clave          = trim($_POST['clave']);
                $creditos       = (int)$_POST['creditos'];
                //? Capturamos el ID de academia como string para validar si está vacío
                $id_academia    = $_POST['id_academia']; 
                //? Captura los IDs de los profesores seleccionados (puede ser un array vacío)
                $profesores     = isset($_POST['profesores']) ? $_POST['profesores'] : [];

                //? VALIDACIONES
                
                //? 1. Validar que se seleccionó al menos un docente
                if (empty($profesores)) {
                    $error = "Debe seleccionar al menos un docente para impartir esta materia.";
                } 
                
                //? 2. Validar que se seleccionó una academia
                elseif ($id_academia === '') { 
                    $error = "Debe seleccionar una academia válida para esta materia.";
                }

                //? 3. Si no hay errores, proceder con el registro
                if (empty($error)) {
                    //? Convertimos el ID de academia a INT para la base de datos
                    $id_academia_db = (int)$id_academia; 

                    //? Llama al modelo para insertar la materia y sus asignaciones de profesores
                    $ok = $this->model->insertarMateria($nombre_materia, $clave, $creditos, $id_academia_db, $profesores);
                    
                    //? Verifica el resultado y redirige
                    if($ok){
                        header("Location: index.php?controlador=materia&accion=consultarMaterias&exito=registro");
                        exit;
                    } else {
                        $error = "No se pudo registrar la materia (Error en la base de datos).";
                    }
                }
            }
            
            //? 4. Recargar listas de apoyo (Academias y Profesores) para la vista
            $academias  = $this->model->listarAcademias();
            $profesores = $this->model->listarProfesores();
            include "app/views/materia/insertar_materia.php";
        }

        //* Método para precargar datos y procesar la actualización de una materia.
        public function editar(){
            //? Bloque para procesar el formulario de actualización (si se envió el POST)
            if(isset($_POST['guardar_cambios'])){
                //? Captura de datos del formulario
                $id_materia     = (int)$_POST['id_materia'];
                $nombre_materia = trim($_POST['nombre_materia']);
                $clave          = trim($_POST['clave']);
                $creditos       = (int)$_POST['creditos'];
                //? Captura de ID de academia y lista de profesores
                $id_academia    = ($_POST['id_academia'] !== '') ? (int)$_POST['id_academia'] : null;
                $profesores     = isset($_POST['profesores']) ? $_POST['profesores'] : [];
                $error = "";

                //? 1. Validar que se seleccionó al menos un docente
                if (empty($profesores)) {
                    $error = "Debe seleccionar al menos un docente para impartir esta materia.";
                } 
                
                //? 2. Validar que se seleccionó una academia (si aplica la regla)
                elseif ($id_academia === '') {
                    $error = "Debe seleccionar una academia válida para esta materia.";
                }

                //? 3. Si no hay errores, proceder con la actualización
                if (empty($error)) {
                    $id_academia_db = ($id_academia !== '') ? (int)$id_academia : null;

                    //? Llama al modelo para actualizar la materia y sincronizar los docentes
                    $ok = $this->model->actualizarMateria($id_materia, $nombre_materia, $clave, $creditos, $id_academia_db, $profesores);
                    
                    //? Verifica el resultado y redirige
                    if($ok){
                        header("Location: index.php?controlador=materia&accion=consultarMaterias&exito=actualizado");
                        exit;
                    } else {
                        $error = "No se pudo actualizar la materia.";
                    }
                }
            
            }

            //? Bloque para obtener el ID de la materia a editar (maneja GET y POST fallido)
            if(!isset($_GET['id'])) {
                //? Si no hay ID en GET, intenta obtenerlo del POST fallido
                $id_materia = isset($_POST['id_materia']) ? (int)$_POST['id_materia'] : 0;
                if ($id_materia === 0) die("Falta id de materia.");
            } else {
                $id_materia = (int)$_GET['id'];
            }

            //? Obtiene los datos principales de la materia
            $row = $this->model->obtenerMateria($id_materia);
            if(!$row) die("Materia no encontrada.");
            
            //? Carga listas de apoyo (Academias y Profesores)
            $academias = $this->model->listarAcademias();
            $profesores = $this->model->listarProfesores();
            //? Obtiene la lista de profesores asignados a esta materia para preselección
            $profesoresDeEsta = $this->model->profesoresDeMateria($id_materia);

            //? Carga la vista de actualización
            include "app/views/materia/actualizar_materia.php";
        }

        //* Método para eliminar una materia y sus asignaciones.
        public function eliminar(){
            //? Válida que se reciba el ID por GET
            if(isset($_GET['id'])){
                //? Llama al modelo para eliminar la materia y sus relaciones
                $this->model->eliminarMateria((int)$_GET['id']);
            }
            //? Redirige a la consulta de materias
            header("Location: index.php?controlador=materia&accion=consultarMaterias");
            exit;
        }
    }