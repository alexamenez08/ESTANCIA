<?php 
    //! Clase Modelo para acceder a datos estadísticos y de reporteo.
    //! Se utiliza para generar listas de profesores, estadísticas de avance y gráficas.
    class ReportModel{

        private $connection;

        public function __construct($connection){
            $this -> connection = $connection;
        }

        //* Método para obtener todos los profesores para llenar el dropdown de selección.
        public function consultarTodosProfesores(){
            $sql = "SELECT id_profesor, nombre, apellido_pa, apellido_ma 
                    FROM profesor 
                    ORDER BY apellido_pa, apellido_ma";
            
            //? Ejecuta la consulta
            $result = $this -> connection -> query($sql);
            return $result; //? Devuelve mysqli_result
        }

        //* Método para obtener los datos detallados de un profesor por ID.
        public function consultarProfesorPorID($id_profesor){
            $sql = "SELECT id_profesor, matricula, nombre, apellido_pa, apellido_ma, rol, grado_academico 
                    FROM profesor 
                    WHERE id_profesor = ?";
            
            //? Prepara la sentencia
            $stmt = $this -> connection -> prepare($sql);
            //? Vincula el ID (integer)
            $stmt -> bind_param("i", $id_profesor);
            $stmt -> execute();
            $result = $stmt -> get_result();
            //? Devuelve una fila
            return $result -> fetch_assoc(); 
        }

        //* Método para obtener las materias que imparte un profesor específico.
        public function consultarMateriasPorProfesor($id_profesor){
            $sql = "SELECT m.nombre_materia, m.clave, a.nombre AS nombre_academia
                    FROM asignacionmateria am
                    JOIN materia m ON am.id_materia = m.id_materia
                    LEFT JOIN academia a ON m.id_academia = a.id_academia
                    WHERE am.id_profesor = ?
                    ORDER BY m.nombre_materia";

            //? Prepara la sentencia
            $stmt = $this -> connection -> prepare($sql);
            //? Vincula el ID (integer)
            $stmt -> bind_param("i", $id_profesor);
            $stmt -> execute();
            return $stmt -> get_result(); //? Devuelve mysqli_result
        }

        //* Método para obtener estadísticas de avance de aplicaciones agrupadas por Academia.
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

            //? Ejecuta la consulta no preparada
            $result = $this -> connection -> query($sql);
            return $result; //? Devuelve mysqli_result
        }

        //* Método para consultar el conteo de aplicaciones agrupadas por estado (pendiente/completado).
        public function consultarAplicacionesPorEstado(){
            //? Sentencia SQL para contar los estados de las aplicaciones
            $sql_statement = "SELECT estado, COUNT(id_aplicacion) AS total 
                              FROM aplicacioninstrumento 
                              GROUP BY estado";
            $result = $this -> connection -> query($sql_statement);

            $data = [];

            //? Formatea los resultados al formato [Etiqueta, Valor] requerido por PHPlot
            while($row = $result -> fetch_assoc()){
                //? Decodifica el estado a un texto más legible
                $estado_decodificado = ($row['estado'] == 'pendiente') ? 'PENDIENTE' : 'COMPLETADO';
                
                $data[] = [$estado_decodificado, (int) $row['total']];
            }

            return $data;
        }

    }