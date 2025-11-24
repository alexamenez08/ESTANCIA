<?php
class MateriaModel {
    public $connection;

    public function __construct($connection){
        $this->connection = $connection;
    }

    /* Listas para selects */
    public function listarAcademias(){
        $sql = "SELECT id_academia, nombre, siglas FROM academia ORDER BY nombre";
        return $this->connection->query($sql);
    }
    public function listarProfesores(){
        $sql = "SELECT id_profesor, nombre, apellido_pa, apellido_ma 
                FROM profesor ORDER BY nombre, apellido_pa, apellido_ma";
        return $this->connection->query($sql);
    }

    /* CRUD Materia */
    public function insertarMateria($nombre_materia, $clave, $creditos, $id_academia, array $ids_profesores){
        
        // Asumo que id_materia es AUTO_INCREMENTAL, por lo tanto, no se pasa.
        $sql = "INSERT INTO materia (nombre_materia, clave, creditos, id_academia)
                VALUES (?, ?, ?, ?)";
        $st = $this->connection->prepare($sql);
        
        // 1. Ejecutar INSERT en MATERIA
        // Tipos de datos: ssii (string, string, integer, integer)
        $st->bind_param("ssii", $nombre_materia, $clave, $creditos, $id_academia);
        if(!$st->execute()) return false;
        
        // 🔑 CORRECCIÓN CLAVE: Obtener el ID que MySQL acaba de generar
        $new_id_materia = $this->connection->insert_id; 

        /* Vincula docentes en asignacionmateria */
        if (!empty($ids_profesores)) {
            $sql2 = "INSERT INTO asignacionmateria (nombre_materia, id_materia, id_profesor)
                     VALUES (?, ?, ?)";
            $st2 = $this->connection->prepare($sql2);
            
            foreach($ids_profesores as $idp){
                $idp = (int)$idp;
                // 2. Usar el nuevo ID autoincremental
                $st2->bind_param("iii", $new_id_materia, $new_id_materia, $idp);
                if(!$st2->execute()) {
                    error_log("Fallo al asignar profesor {$idp} a materia {$new_id_materia}: " . $st2->error);
                }
            }
        }
        return true;
    }

    public function consultarMaterias(){
        /* Muestra academia y docentes concatenados */
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
        return $this->connection->query($sql);
    }

    public function obtenerMateria($id_materia){
        $sql = "SELECT id_materia, nombre_materia, clave, creditos, id_academia 
                FROM materia WHERE id_materia = ?";
        $st = $this->connection->prepare($sql);
        $st->bind_param("i", $id_materia);
        $st->execute();
        $res = $st->get_result();
        return $res->fetch_assoc();
    }

    public function profesoresDeMateria($id_materia){
        $sql = "SELECT id_profesor FROM asignacionmateria WHERE id_materia = ?";
        $st = $this->connection->prepare($sql);
        $st->bind_param("i", $id_materia);
        $st->execute();
        $res = $st->get_result();
        $ids = [];
        while($r = $res->fetch_assoc()) $ids[] = (int)$r['id_profesor'];
        return $ids;
    }

    public function actualizarMateria($id_materia, $nombre_materia, $clave, $creditos, $id_academia, array $ids_profesores){
        $sql = "UPDATE materia 
                SET nombre_materia = ?, clave = ?, creditos = ?, id_academia = ?
                WHERE id_materia = ?";
        $st = $this->connection->prepare($sql);
        $st->bind_param("ssiii", $nombre_materia, $clave, $creditos, $id_academia, $id_materia);
        if(!$st->execute()) return false;

        /* Sincroniza docentes: borra y re-inserta */
        $del = $this->connection->prepare("DELETE FROM asignacionmateria WHERE id_materia = ?");
        $del->bind_param("i", $id_materia);
        $del->execute();

        if (!empty($ids_profesores)) {
            $ins = $this->connection->prepare(
                "INSERT INTO asignacionmateria (nombre_materia, id_materia, id_profesor) VALUES (?, ?, ?)"
            );
            foreach($ids_profesores as $idp){
                $idp = (int)$idp;
                $ins->bind_param("iii", $id_materia, $id_materia, $idp);
                $ins->execute();
            }
        }
        return true;
    }

    public function eliminarMateria($id_materia){
        /* Primero limpia relaciones para evitar huérfanos */
        $del = $this->connection->prepare("DELETE FROM asignacionmateria WHERE id_materia = ?");
        $del->bind_param("i", $id_materia);
        $del->execute();

        $sql = "DELETE FROM materia WHERE id_materia = ?";
        $st  = $this->connection->prepare($sql);
        $st->bind_param("i", $id_materia);
        return $st->execute();
    }

    public function consultarTodasMaterias() {
        $sql = "SELECT id_materia, nombre_materia FROM materia ORDER BY nombre_materia";
        return $this->connection->query($sql);
    }

    public function consultarMateriasPorProfesor($id_profesor_logueado){
        $sql = "SELECT m.nombre_materia, m.clave, a.nombre AS nombre_academia
                FROM asignacionmateria am
                JOIN materia m ON am.id_materia = m.id_materia
                LEFT JOIN academia a ON m.id_academia = a.id_academia
                WHERE am.id_profesor = ?
                ORDER BY m.nombre_materia";

        $st = $this->connection->prepare($sql);
        $st->bind_param("i", $id_profesor_logueado);
        $st->execute();
        return $st->get_result(); // Devuelve mysqli_result
    }
}
