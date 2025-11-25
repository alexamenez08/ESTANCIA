<?php
    //! Clase Modelo para interactuar con las tablas 'materia', 'academia', 'profesor' y 'asignacionmateria'.
    //! Contiene la lógica de datos para el CRUD de materias y sus relaciones.
    class MateriaModel {
        public $connection;

        
        public function __construct($connection){
            $this->connection = $connection;
        }

        /* Listas para selects */
        //* Método para obtener la lista de academias para un SELECT.
        public function listarAcademias(){
            //? Consulta SQL para obtener ID, nombre y siglas de academias
            $sql = "SELECT id_academia, nombre, siglas FROM academia ORDER BY nombre";
            //? Ejecuta la consulta y retorna el resultado
            return $this->connection->query($sql);
        }
        
        //* Método para obtener la lista de profesores para un SELECT.
        public function listarProfesores(){
            //? Consulta SQL para obtener nombre completo de profesores
            $sql = "SELECT id_profesor, nombre, apellido_pa, apellido_ma 
                    FROM profesor ORDER BY nombre, apellido_pa, apellido_ma";
            //? Ejecuta la consulta y retorna el resultado
            return $this->connection->query($sql);
        }

        /* CRUD Materia */
        //* Método para insertar una nueva materia y asignarla a uno o varios profesores.
        public function insertarMateria($nombre_materia, $clave, $creditos, $id_academia, array $ids_profesores){
            
            //? Sentencia SQL para insertar en la tabla 'materia'
            $sql = "INSERT INTO materia (nombre_materia, clave, creditos, id_academia)
                    VALUES (?, ?, ?, ?)";
            $st = $this->connection->prepare($sql);
            
            //? 1. Ejecutar INSERT en MATERIA (ssii: string, string, integer, integer)
            $st->bind_param("ssii", $nombre_materia, $clave, $creditos, $id_academia);
            //? Si la inserción de la materia falla, retorna falso
            if(!$st->execute()) return false;
            
            //? Obtener el ID generado para vincular en la tabla de asignación
            $new_id_materia = $this->connection->insert_id; 

            //* Vincula docentes en asignacionmateria (tabla pivote)
            if (!empty($ids_profesores)) {
                //? Sentencia SQL para insertar en 'asignacionmateria'
                $sql2 = "INSERT INTO asignacionmateria (nombre_materia, id_materia, id_profesor)
                        VALUES (?, ?, ?)";
                $st2 = $this->connection->prepare($sql2);
                
                //? Itera sobre los profesores seleccionados
                foreach($ids_profesores as $idp){
                    $idp = (int)$idp;
                    //? 2. Usar el nuevo ID autoincremental (iii: integer, integer, integer)
                    $st2->bind_param("iii", $new_id_materia, $new_id_materia, $idp);
                    if(!$st2->execute()) {
                        //? Registra el error en el log si la asignación falla
                        error_log("Fallo al asignar profesor {$idp} a materia {$new_id_materia}: " . $st2->error);
                    }
                }
            }
            //? Retorna éxito si la materia se insertó correctamente
            return true;
        }

        //* Método para obtener todas las materias con su academia y lista de docentes.
        public function consultarMaterias(){
            /* Muestra academia y docentes concatenados usando GROUP_CONCAT */
            $sql = "SELECT 
                        m.id_materia,
                        m.nombre_materia,
                        m.clave,
                        m.creditos,
                        a.nombre AS academia,
                        GROUP_CONCAT(CONCAT(p.nombre,' ',p.apellido_pa,' ',p.apellido_ma)
                                    ORDER BY p.nombre SEPARATOR ', ') AS docentes
                    FROM materia m
                    LEFT JOIN academia a ON a.id_academia = m.id_academia
                    LEFT JOIN asignacionmateria am ON am.id_materia = m.id_materia
                    LEFT JOIN profesor p ON p.id_profesor = am.id_profesor
                    GROUP BY m.id_materia, m.nombre_materia, m.clave, m.creditos, a.nombre
                    ORDER BY m.nombre_materia";
            //? Ejecuta la consulta y retorna el resultado (objeto mysqli_result)
            return $this->connection->query($sql);
        }

        //* Método para obtener los datos de una materia por su ID.
        public function obtenerMateria($id_materia){
            //? Consulta SQL para obtener los campos principales de la materia
            $sql = "SELECT id_materia, nombre_materia, clave, creditos, id_academia 
                    FROM materia WHERE id_materia = ?";
            $st = $this->connection->prepare($sql);
            //? Vincula el ID (integer)
            $st->bind_param("i", $id_materia);
            $st->execute();
            $res = $st->get_result();
            //? Retorna la fila como array asociativo
            return $res->fetch_assoc();
        }

        //* Método para obtener la lista de IDs de profesores asignados a una materia.
        public function profesoresDeMateria($id_materia){
            //? Consulta SQL para obtener los ID de los profesores en la tabla pivote
            $sql = "SELECT id_profesor FROM asignacionmateria WHERE id_materia = ?";
            $st = $this->connection->prepare($sql);
            //? Vincula el ID (integer)
            $st->bind_param("i", $id_materia);
            $st->execute();
            $res = $st->get_result();
            $ids = [];
            //? Almacena los IDs en un array simple
            while($r = $res->fetch_assoc()) $ids[] = (int)$r['id_profesor'];
            return $ids;
        }

        //* Método para actualizar los datos principales de una materia y sincronizar sus profesores.
        public function actualizarMateria($id_materia, $nombre_materia, $clave, $creditos, $id_academia, array $ids_profesores){
            //? 1. Actualizar la tabla 'materia'
            $sql = "UPDATE materia 
                    SET nombre_materia = ?, clave = ?, creditos = ?, id_academia = ?
                    WHERE id_materia = ?";
            $st = $this->connection->prepare($sql);
            //? Vincula los parámetros (ssiii: string, string, int, int, int)
            $st->bind_param("ssiii", $nombre_materia, $clave, $creditos, $id_academia, $id_materia);
            if(!$st->execute()) return false;

            /* 2. Sincroniza docentes: borra asignaciones antiguas y re-inserta las nuevas */
            //? Elimina todas las asignaciones de la materia
            $del = $this->connection->prepare("DELETE FROM asignacionmateria WHERE id_materia = ?");
            $del->bind_param("i", $id_materia);
            $del->execute();

            //? Inserta las nuevas asignaciones
            if (!empty($ids_profesores)) {
                $ins = $this->connection->prepare(
                    "INSERT INTO asignacionmateria (nombre_materia, id_materia, id_profesor) VALUES (?, ?, ?)"
                );
                foreach($ids_profesores as $idp){
                    $idp = (int)$idp;
                    //? El nombre_materia aquí parece ser el ID de la materia, basado en el código
                    $ins->bind_param("iii", $id_materia, $id_materia, $idp);
                    $ins->execute();
                }
            }
            //? Retorna éxito
            return true;
        }

        //* Método para eliminar una materia y limpiar sus referencias en la tabla de asignación.
        public function eliminarMateria($id_materia){
            /* 1. Primero limpia relaciones para evitar huérfanos */
            //? Elimina las asignaciones de profesores de esta materia
            $del = $this->connection->prepare("DELETE FROM asignacionmateria WHERE id_materia = ?");
            $del->bind_param("i", $id_materia);
            $del->execute();

            //? 2. Elimina la materia de la tabla principal
            $sql = "DELETE FROM materia WHERE id_materia = ?";
            $st  = $this->connection->prepare($sql);
            $st->bind_param("i", $id_materia);
            return $st->execute();
        }

        //* Método para consultar la lista simple de todas las materias. (Usado en AplicacionController)
        public function consultarTodasMaterias() {
            $sql = "SELECT id_materia, nombre_materia FROM materia ORDER BY nombre_materia";
            //? Ejecuta la consulta y retorna el resultado
            return $this->connection->query($sql);
        }

        //* Método para obtener las materias que imparte un profesor específico.
        public function consultarMateriasPorProfesor($id_profesor_logueado){
            //? Consulta las materias asignadas a un profesor por su ID
            $sql = "SELECT m.nombre_materia, m.clave, a.nombre AS nombre_academia
                    FROM asignacionmateria am
                    JOIN materia m ON am.id_materia = m.id_materia
                    LEFT JOIN academia a ON m.id_academia = a.id_academia
                    WHERE am.id_profesor = ?
                    ORDER BY m.nombre_materia";

            $st = $this->connection->prepare($sql);
            //? Vincula el ID del profesor (integer)
            $st->bind_param("i", $id_profesor_logueado);
            $st->execute();
            //? Retorna el objeto mysqli_result
            return $st->get_result(); 
        }
        
    }