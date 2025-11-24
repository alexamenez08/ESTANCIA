<?php 

    class ReportModel{

        private $connection;

        public function __construct($connection){
            $this -> connection = $connection;
        }
        

        //* Método para consultar los usuarios
        public function consultarUsuarios(){

            $sql_statement = "SELECT * FROM lista";

            $result = $this -> connection -> query($sql_statement);

            return $result;
        }

        /**
         * Obtiene todos los profesores para llenar el dropdown de selección.
         */
        public function consultarTodosProfesores(){
            $sql = "SELECT id_profesor, nombre, apellido_pa, apellido_ma 
                    FROM profesor 
                    ORDER BY apellido_pa, apellido_ma";
            
            $result = $this -> connection -> query($sql);
            return $result; // Devuelve mysqli_result
        }

        /**
         * Obtiene los datos de un profesor específico por ID.
         */
        public function consultarProfesorPorID($id_profesor){
            $sql = "SELECT id_profesor, matricula, nombre, apellido_pa, apellido_ma, rol, grado_academico 
                    FROM profesor 
                    WHERE id_profesor = ?";
            
            $stmt = $this -> connection -> prepare($sql);
            $stmt -> bind_param("i", $id_profesor);
            $stmt -> execute();
            $result = $stmt -> get_result();
            return $result -> fetch_assoc(); // Devuelve una fila
        }

        /**
         * Obtiene las materias que imparte un profesor específico.
         */
        public function consultarMateriasPorProfesor($id_profesor){
            $sql = "SELECT m.nombre_materia, m.clave, a.nombre AS nombre_academia
                    FROM asignacionmateria am
                    JOIN materia m ON am.id_materia = m.id_materia
                    LEFT JOIN academia a ON m.id_academia = a.id_academia
                    WHERE am.id_profesor = ?
                    ORDER BY m.nombre_materia";

            $stmt = $this -> connection -> prepare($sql);
            $stmt -> bind_param("i", $id_profesor);
            $stmt -> execute();
            return $stmt -> get_result(); // Devuelve mysqli_result
        }

 
        public function getEstadisticasPorAcademia() {
            
            $sql = "SELECT
                        a.id_academia,
                        a.nombre AS academia_nombre,
                        
                        -- Contar todas las aplicaciones únicas asignadas a profesores de esta academia
                        COUNT(DISTINCT ai.id_aplicacion) AS total_asignadas,
                        
                        -- Contar solo las aplicaciones únicas completadas
                        COUNT(DISTINCT CASE 
                            WHEN ai.estado = 'completado' THEN ai.id_aplicacion 
                            ELSE NULL 
                        END) AS total_completadas
                    
                    FROM
                        academia a
                    -- Usamos LEFT JOIN para incluir academias aunque no tengan profesores o aplicaciones
                    LEFT JOIN
                        materia m ON a.id_academia = m.id_academia
                    LEFT JOIN
                        asignacionmateria am ON m.id_materia = am.id_materia
                    LEFT JOIN
                        profesor p ON am.id_profesor = p.id_profesor
                    LEFT JOIN
                        -- Unimos con aplicaciones donde el profesor es el EVALUADO
                        aplicacioninstrumento ai ON p.id_profesor = ai.id_profesor
                    
                    GROUP BY
                        a.id_academia, a.nombre
                    ORDER BY
                        a.nombre";

            $result = $this -> connection -> query($sql);
            return $result; // Devuelve mysqli_result
        }

        /**
         * Consulta el conteo de aplicaciones agrupadas por estado (pendiente/completado).
         */
        public function consultarAplicacionesPorEstado(){
            // Cuenta los estados de las aplicaciones en la tabla principal
            $sql_statement = "SELECT estado, COUNT(id_aplicacion) AS total 
                            FROM aplicacioninstrumento 
                            GROUP BY estado";
            $result = $this -> connection -> query($sql_statement);

            $data = [];

            while($row = $result -> fetch_assoc()){
                // PHPlot necesita un formato [Etiqueta, Valor]
                $estado_decodificado = ($row['estado'] == 'pendiente') ? 'PENDIENTE' : 'COMPLETADO';
                
                $data[] = [$estado_decodificado, (int) $row['total']];
            }

            return $data;
        }

    }