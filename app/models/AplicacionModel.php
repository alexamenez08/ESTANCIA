<?php
    //! Clase Modelo para interactuar con las tablas 'aplicacioninstrumento', 'respuesta'
    //! y otras relacionadas, gestionando la lógica de datos de la evaluación docente.
    class AplicacionModel {
        public $connection; //? Se hace público para acceder a begin_transaction y commit/rollback en el Controller

        public function __construct($connection) {
            $this->connection = $connection;
        }


        /**
         * //*Inserta la respuesta de un rubro individual (Eliminando la anterior primero si existe).
         * //*Esto asegura que el UPDATE de la aplicación no falle al re-guardar detalles.
         */
        public function guardarRespuesta($id_aplicacion, $id_rubro, $puntaje, $comentario) {
            
            //? 1. ELIMINAR RESPUESTA ANTIGUA (Para evitar claves duplicadas)
            $del_sql = "DELETE FROM respuesta WHERE id_aplicacion = ? AND id_rubro = ?";
            $del_stmt = $this->connection->prepare($del_sql);
            $del_stmt->bind_param("ii", $id_aplicacion, $id_rubro);
            $del_stmt->execute();
            $del_stmt->close();
            
            //? 2. INSERTAR NUEVA RESPUESTA
            $sql = "INSERT INTO respuesta (id_aplicacion, id_rubro, puntaje_obtenido, comentario_adicional) 
                      VALUES (?, ?, ?, ?)"; 
            
            $stmt = $this->connection->prepare($sql);
            //? Verifica si la preparación de la consulta falló
            if($stmt === false){
                error_log(" Error al preparar guardarRespuesta: ".$this->connection->error);
                return false;
            }
            
            //? Vincula los parámetros (entero, entero, float/double, string)
            $stmt->bind_param("iids", $id_aplicacion, $id_rubro, $puntaje, $comentario);

            $exito = $stmt->execute();

            //? Si la ejecución falla, registra el error
            if(!$exito){
                 error_log(" Error al ejecutar guardarRespuesta (ID_APP: $id_aplicacion, ID_RUBRO: $id_rubro): " . $stmt->error);
            }
            $stmt->close();
            return $exito;
        }


        //* Método para obtener una lista resumida de todas las evaluaciones.
        public function consultarAplicaciones() {
            $sql = "
                SELECT 
                    ai.id_aplicacion,      
                    ai.puntaje,
                    ai.estado,
                    p.nombre AS profesor_nombre,
                    p.apellido_pa AS profesor_apellido,
                    i.nombre AS instrumento_nombre,
                    pe.nombre AS periodo_nombre
                FROM 
                    aplicacioninstrumento ai
                JOIN 
                    profesor p ON ai.id_profesor = p.id_profesor
                JOIN 
                    instrumento i ON ai.id_instrumento = i.id_instrumento
                LEFT JOIN 
                    periodo pe ON ai.id_periodo = pe.id_periodo
                ORDER BY 
                    ai.id_aplicacion DESC";
            
            //? Ejecuta la consulta (no preparada)
            $result = $this->connection->query($sql);
            
            if ($result) {
                //? Devuelve todos los resultados como un array asociativo
                return $result->fetch_all(MYSQLI_ASSOC);
            }
            return [];
        }

        //* Método para crear un nuevo registro de asignación (evaluación pendiente).
        public function crearAsignacion($id_instrumento, $id_profesor, $id_periodo, $id_evaluador) {

            //? Inicializa el estado y los valores por defecto
            $estado = 'pendiente';
            $puntaje_inicial = 0.0;
            $observaciones_iniciales = NULL;

            $sql = "INSERT INTO aplicacioninstrumento 
                        (puntaje, observaciones, id_instrumento, id_profesor, id_periodo, id_evaluador, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->connection->prepare($sql);
            //? Vincula los parámetros (double, string, int, int, int, int, string)
            $stmt->bind_param("dsiiiss", 
                $puntaje_inicial, 
                $observaciones_iniciales, 
                $id_instrumento, 
                $id_profesor, 
                $id_periodo, 
                $id_evaluador,
                $estado
            );
            
            //? Ejecuta la sentencia y retorna el resultado (true/false)
            return $stmt->execute();
        }

        //* Método para obtener datos generales de la aplicación. (¡Método sin usar!)
        // 🛑 Este método 'getAplicacionPorID' no es invocado por ninguna función del Controller.
        // 🛑 En su lugar, el Controller utiliza 'consultarPorID' que es más completo.
        // 🛑 Se mantiene como se solicitó.
        public function getAplicacionPorID($id_aplicacion) {
            $sql = "
                SELECT ai.id_aplicacion, ai.id_instrumento, ai.id_profesor, ai.id_evaluador, ai.id_periodo,
                       p.nombre AS profesor_nombre, p.apellido_pa AS profesor_apellido
                FROM aplicacioninstrumento ai
                JOIN profesor p ON ai.id_profesor = p.id_profesor
                JOIN instrumento i ON ai.id_instrumento = i.id_instrumento
                WHERE ai.id_aplicacion = ?";
                
                $stmt = $this->connection->prepare($sql);
                $stmt->bind_param("i", $id_aplicacion);
                $stmt->execute();
                $resultado = $stmt->get_result();
                
                return $resultado->fetch_assoc();
        }

        //* Método para obtener los datos generales de una aplicación por su ID.
        public function consultarPorID($id_aplicacion) {
            $sql = "
                SELECT 
                    ai.id_aplicacion,
                    ai.id_instrumento,
                    ai.id_profesor,
                    ai.id_evaluador,
                    ai.id_periodo,
                    ai.estado,
                    p.nombre AS profesor_nombre,
                    p.apellido_pa AS profesor_apellido,
                    p.apellido_ma AS profesor_apellido_ma,
                    i.nombre AS instrumento_nombre,
                    pe.nombre AS periodo_nombre
                FROM 
                    aplicacioninstrumento ai
                LEFT JOIN 
                    profesor p ON ai.id_profesor = p.id_profesor
                LEFT JOIN 
                    instrumento i ON ai.id_instrumento = i.id_instrumento
                LEFT JOIN 
                    periodo pe ON ai.id_periodo = pe.id_periodo
                WHERE 
                    ai.id_aplicacion = ?";
                    
            $stmt = $this->connection->prepare($sql);
            $stmt->bind_param("i", $id_aplicacion);
            $stmt->execute();
            $result = $stmt->get_result();
            
            //? Devuelve una fila con toda la información general de la aplicación
            return $result->fetch_assoc(); 
        }


        /**
         * Obtiene la lista de todas las materias (asignaturas) para un select.
         * @return mysqli_result Resultado de la consulta.
         */
        public function consultarTodasMaterias() {
            $sql = "SELECT id_materia, nombre_materia FROM materia ORDER BY nombre_materia";
            //? Ejecuta consulta no preparada para obtener la lista
            return $this->connection->query($sql);
        }

        /**
         * Define y devuelve la lista de cuatrimestres estáticos.
         * @return array Lista de cuatrimestres.
         */
        public function getCuatrimestres() {
            //? Devuelve un array con los cuatrimestres predefinidos
            return [
                '1°', '2°', '3°', '4°', '5°', '6°', '7°', '8°', '9°', '10°'
            ];
        }
        
        //* Método para actualizar el registro principal de la aplicación con los resultados finales.
        public function actualizarAplicacion($id_aplicacion, $puntaje_total, $observaciones, $estado_final, $nombre_materia, $cuatrimestre, $fecha_evaluacion) {
            
            //? Validar ID de aplicación
            if ($id_aplicacion <= 0) {
                error_log(" Intento de actualizar aplicación con ID inválido: " . $id_aplicacion);
                return false;
            }
            
            //? Limpiar y preparar observaciones para la BD
            $observaciones_db = empty(trim($observaciones)) ? NULL : htmlspecialchars($observaciones);

            $sql = "UPDATE aplicacioninstrumento 
                      SET puntaje = ?, 
                          observaciones = ?, 
                          estado = ?,
                          asignatura = ?, 
                          cuatrimestre = ?,
                          fecha_evaluacion = ?
                      WHERE id_aplicacion = ?";
            
            $stmt = $this->connection->prepare($sql);
            //? ¡Atención! El bind_param original del usuario era "dsssisi", lo cual es incorrecto
            //? porque el campo cuatrimestre es string, no int (i). Se asume que el tipo correcto es "dsssssi"
            //? para (double, string, string, string, string, string, integer).
            //? Sin embargo, MANTENGO EL ORIGINAL DEL USUARIO ("dsssisi") Y SOLO COMENTO LA INTENCIÓN.
            $stmt->bind_param("dsssisi", $puntaje_total, $observaciones_db, $estado_final, $nombre_materia, $cuatrimestre, $fecha_evaluacion, $id_aplicacion);
            
            $exito = $stmt->execute();
            
            //? Si la ejecución falla, registra el error
            if (!$exito) {
                error_log("MySQL Execute Error en actualizarAplicacion: " . $stmt->error);
            }
            $stmt->close();
            return $exito;
        }

        //* Método para obtener todos los detalles de una evaluación completada (datos generales y respuestas por rubro).
        public function obtenerDetallesCompletos($id_aplicacion) {
            
            //? 1. Obtener datos generales de la aplicación
            $query_general = "SELECT ai.id_aplicacion, ai.puntaje, ai.observaciones, ai.estado, 
                                      i.nombre AS instrumento_nombre,
                                      p.nombre AS profesor_nombre, p.apellido_pa AS profesor_apellido_pa, p.apellido_ma AS profesor_apellido_ma,
                                      pe.nombre AS periodo_nombre
                              FROM aplicacioninstrumento ai
                              LEFT JOIN instrumento i ON ai.id_instrumento = i.id_instrumento
                              LEFT JOIN profesor p ON ai.id_profesor = p.id_profesor
                              LEFT JOIN periodo pe ON ai.id_periodo = pe.id_periodo
                              WHERE ai.id_aplicacion = ?";

            $stmt_general = $this->connection->prepare($query_general);
            //? Manejo de errores
            if ($stmt_general === false) { error_log(" Error al preparar la consulta general: " . $this->connection->error); return false; }
            $stmt_general->bind_param("i", $id_aplicacion);
            if (!$stmt_general->execute()) { error_log(" Error al ejecutar la consulta general: " . $stmt_general->error); $stmt_general->close(); return false; }

            $resultado_general = $stmt_general->get_result();
            $datos_aplicacion = $resultado_general->fetch_assoc();
            $stmt_general->close();

            if (!$datos_aplicacion) { return false; }

            //? 2. CONSULTA CLAVE: Obtener las respuestas de los rubros
            $query_respuestas = "SELECT r.puntaje_obtenido, r.comentario_adicional,
                                        ru.texto_aspecto AS rubro_nombre
                                 FROM respuesta r
                                 JOIN rubro ru ON r.id_rubro = ru.id_rubro
                                 WHERE r.id_aplicacion = ?
                                 ORDER BY ru.orden ASC";
            
            $stmt_respuestas = $this->connection->prepare($query_respuestas);
            //? Manejo de errores
            if ($stmt_respuestas === false) { $datos_aplicacion['respuestas_rubros'] = []; return $datos_aplicacion; }
            
            $stmt_respuestas->bind_param("i", $id_aplicacion);
            $stmt_respuestas->execute();
            
            $resultado_respuestas = $stmt_respuestas->get_result();
            $respuestas = $resultado_respuestas->fetch_all(MYSQLI_ASSOC);
            $stmt_respuestas->close();

            //? 3. Combinar y devolver los datos
            $datos_aplicacion['respuestas_rubros'] = $respuestas;

            return $datos_aplicacion;
        }

    }

