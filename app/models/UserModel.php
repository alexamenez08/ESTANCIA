<?php

    //* Crear una clase del modelo
    class UserModel{
        public $connection;

        //* Crear constructor para recibir la conexión
        public Function __construct($connection){
            $this -> connection = $connection;
        }

        //* Método para insertar en la base de datos (Profesor)

        public Function insertarProfesor($matricula, $nombre, $apellido_pa, $apellido_ma, $sexo, $pass, $rol, $grado_academico){
            
            // Los campos de la BD son (9 campos): id_profesor, matricula, nombre, apellido_pa, apellido_ma, sexo, pass, rol, grado_academico
            $sql_statement = "INSERT INTO profesor (matricula, nombre, apellido_pa, apellido_ma, sexo, pass, rol, grado_academico) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            //* Preparar el statement
            $statement = $this -> connection -> prepare($sql_statement);
            
            // Tipos de datos: i (INT) + sssssssss (8 strings) -> 'issssssss'
            $statement -> bind_param("ssssssss",$matricula, $nombre, $apellido_pa, $apellido_ma, $sexo, $pass, $rol, $grado_academico);

            //* Mandar el resultado de la inserción
            return $statement -> execute();

        }
        
        //* Método para consultar los profesores
        public function consultarProfesores(){

            $sql_statement = "SELECT id_profesor, matricula, nombre, apellido_pa, apellido_ma, sexo, rol, grado_academico FROM profesor";

            //* Guardar los datos de la consulta
            $result = $this -> connection -> query($sql_statement);

            return $result;
        }

        //* Método para consultar por ID (Matrícula)
        public function consultarPorID($id_browser){

            $sql_statement = "SELECT id_profesor, matricula, nombre, apellido_pa, apellido_ma, sexo, pass, rol, grado_academico FROM profesor WHERE id_profesor = ?";

            $statement = $this -> connection -> prepare($sql_statement);
            $statement -> bind_param("i", $id_browser); 

            $statement -> execute();

            $result = $statement -> get_result();

            return $result -> fetch_assoc();
        }

        public function actualizarUsuario($id, $matricula, $nombre, $apellido_pa, $apellido_ma, $sexo, $grado_academico){
            $sql_statement = "UPDATE profesor SET matricula = ?, nombre = ?, apellido_pa = ?, apellido_ma = ?, sexo = ?, grado_academico = ? WHERE id_profesor = ?";

            $statement = $this -> connection -> prepare($sql_statement);

            $statement -> bind_param("ssssssi",$matricula,$nombre,$apellido_pa,$apellido_ma,$sexo,$grado_academico,$id);

            return $statement -> execute();
        }

        public function eliminarProfesor($id_profesor){
            $sql_statement = "DELETE FROM profesor WHERE id_profesor = ?";

            $statement = $this -> connection -> prepare($sql_statement);

            $statement -> bind_param("i",$id_profesor);

            return $statement -> execute();
        }

       
        //* Método para consultar profesores con rol 'Profesor'
        public function consultarProfesoresActivos(){

            $sql_statement = "SELECT id_profesor, nombre, apellido_pa, apellido_ma FROM profesor WHERE rol = 'Profesor' ORDER BY apellido_pa";

            //* Guardar los datos de la consulta
            $result = $this -> connection -> query($sql_statement);

            return $result; // Retorna el objeto mysqli_result
        }

        //* Método para consultar Coordinadores/Admins (los evaluadores)
        public function consultarEvaluadores(){
            $sql_statement = "SELECT id_profesor, nombre, apellido_pa 
                            FROM profesor 
                            WHERE rol = 'Coordinador' OR rol = 'Administrador' 
                            ORDER BY apellido_pa";
            $result = $this -> connection -> query($sql_statement);
            return $result; // Retorna el objeto mysqli_result
        }



    /**
     * Consulta profesores filtrados por academia (a través de sus materias) y/o término de búsqueda.
     * Se une con asignacionmateria, materia y academia.
     */
    public function consultarProfesoresPorFiltro($id_academia, $termino) {
        
        // La consulta base ahora incluye una agregación (GROUP_CONCAT)
        $sql = "SELECT DISTINCT p.id_profesor, p.matricula, p.nombre, p.apellido_pa, p.apellido_ma, p.rol, 
                       a.nombre AS nombre_academia,
                       -- 🔑 CLAVE: Concatenamos todas las materias impartidas por el profesor en esta academia
                       GROUP_CONCAT(DISTINCT m.nombre_materia ORDER BY m.nombre_materia SEPARATOR ', ') AS materias_impartidas
                FROM profesor p
                JOIN asignacionmateria am ON p.id_profesor = am.id_profesor
                JOIN materia m ON am.id_materia = m.id_materia
                LEFT JOIN academia a ON m.id_academia = a.id_academia
                WHERE 1=1";

        $params = [];
        $types = "";

        // Filtro por Academia
        if (!empty($id_academia)) {
            $sql .= " AND a.id_academia = ?";
            $params[] = $id_academia;
            $types .= "i";
        }

        // Filtro por Término de Búsqueda
        if (!empty($termino)) {
            $sql .= " AND (p.nombre LIKE ? OR p.apellido_pa LIKE ? OR p.apellido_ma LIKE ?)";
            $like_termino = "%" . $termino . "%";
            $params[] = $like_termino;
            $params[] = $like_termino;
            $params[] = $like_termino;
            $types .= "sss";
        }

        // 🔑 CLAVE: Agregamos GROUP BY para que GROUP_CONCAT funcione correctamente
        $sql .= " GROUP BY p.id_profesor, p.matricula, p.nombre, p.apellido_pa, p.apellido_ma, p.rol, a.nombre
                  ORDER BY p.apellido_pa, p.apellido_ma";

        // Preparar y ejecutar la consulta
        $statement = $this->connection->prepare($sql);
        
        if (!empty($types)) {
            $statement->bind_param($types, ...$params);
        }
        
        $statement->execute();
        return $statement->get_result();
    }

    /* Obtiene la lista de todas las academias (para el dropdown). */
    public function consultarAcademias() {
        $sql = "SELECT id_academia, nombre FROM academia ORDER BY nombre";
        $result = $this->connection->query($sql);
        return $result; // Devolvemos el mysqli_result
    }


    // --- FUNCIÓN CONSULTAR PROFESORES POR MATERIA EN UserModel.php (Modificada) ---
    
    /**
     * Consulta profesores que imparten una materia específica (FN. 9).
     * Ahora filtra por ID de materia.
     */
        public function consultarProfesoresPorMateria($id_materia) { // Aceptar ID
            
            $sql = "SELECT p.nombre, p.apellido_pa, p.apellido_ma,
                        m.nombre_materia, a.nombre AS nombre_academia
                    FROM profesor p
                    JOIN asignacionmateria am ON p.id_profesor = am.id_profesor
                    JOIN materia m ON am.id_materia = m.id_materia
                    LEFT JOIN academia a ON m.id_academia = a.id_academia
                    WHERE 1=1";

            $params = [];
            $types = "";

            // Filtro por ID de materia
            if (!empty($id_materia)) {
                $sql .= " AND m.id_materia = ?"; // Filtramos por el ID exacto
                $params[] = (int)$id_materia;
                $types .= "i"; // 'i' para integer
            }

            $sql .= " ORDER BY p.apellido_pa";

            $statement = $this -> connection -> prepare($sql);
            
            if (!empty($types)) {
                $statement -> bind_param($types, ...$params);
            }
            
            $statement -> execute();
            return $statement -> get_result();
        }


        /**
         * Obtiene los detalles de la evaluación SOLO para el profesor cuyo ID se proporciona.
         * Se usa para restringir la vista de seguimiento (FN. Profesor).
         */
        public function consultarEvaluacionesPropias($id_profesor_logueado) {
            $sql = "
                SELECT 
                    ai.id_aplicacion, ai.puntaje, ai.estado,
                    i.nombre AS instrumento_nombre,
                    pe.nombre AS periodo_nombre
                FROM 
                    aplicacioninstrumento ai
                JOIN 
                    instrumento i ON ai.id_instrumento = i.id_instrumento
                LEFT JOIN 
                    periodo pe ON ai.id_periodo = pe.id_periodo
                WHERE 
                    ai.id_profesor = ? -- 🔑 CLAVE: Filtra solo por el ID del usuario logueado
                ORDER BY 
                    ai.id_aplicacion DESC";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bind_param("i", $id_profesor_logueado);
            $stmt->execute();
            
            return $stmt->get_result(); // Devolvemos el mysqli_result
        }

    }