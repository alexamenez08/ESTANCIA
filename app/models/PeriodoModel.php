<?php

    //! Clase Modelo para interactuar con la tabla 'periodo' de la base de datos. Contiene métodos para el CRUD (Crear, Consultar, Actualizar y Eliminar) de períodos.

    class PeriodoModel {
        private $connection;

        public function __construct($connection) {
            $this->connection = $connection;
        }

        //* Método para insertar un nuevo período en la base de datos.
        public Function insertarPeriodo($nombre, $fecha_inicio, $fecha_fin){
            //? Sentencia SQL para la inserción
            $sql_statement = "INSERT INTO periodo (nombre, fecha_inicio, fecha_fin) VALUES (?, ?, ?)";
            
            //? Prepara la sentencia para prevenir inyecciones SQL
            $statement = $this -> connection -> prepare($sql_statement);
            
            //? Vincula los parámetros (tres strings)
            $statement -> bind_param("sss",$nombre, $fecha_inicio, $fecha_fin);

            //? Ejecuta la sentencia y retorna el resultado (true/false)
            return $statement -> execute();
        }

        //* Método para obtener todos los períodos disponibles, ordenados por ID de forma descendente.
        public function consultarTodos() {
            //? Sentencia SQL para seleccionar todos los registros
            $sql = "SELECT id_periodo, nombre, fecha_inicio, fecha_fin FROM periodo ORDER BY id_periodo DESC";
            
            //? Ejecuta la consulta
            $result = $this->connection->query($sql);
            
            //? Devuelve todos los resultados como un array asociativo
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        //* Método para obtener un período específico mediante su ID.
        public function consultarPorID($id_periodo) {
            //? Sentencia SQL con marcador de posición (?)
            $sql = "SELECT id_periodo, nombre, fecha_inicio, fecha_fin FROM periodo WHERE id_periodo = ?";
            $stmt = $this->connection->prepare($sql);
            
            //? Manejo de error si la preparación falla
            if (!$stmt) {
                error_log("Error al preparar consultarPorID: " . $this->connection->error);
                return false;
            }
            
            //? Vincula el parámetro (entero)
            $stmt->bind_param("i", $id_periodo);
            $stmt->execute();
            $result = $stmt->get_result();
            
            //? Devuelve el período como un array asociativo (una sola fila)
            return $result->fetch_assoc();
        }

        //* Método para actualizar los datos de un período existente.
        public function actualizarPeriodo($id_periodo, $nombre, $fecha_inicio, $fecha_fin) {
            //? Sentencia SQL para actualizar, buscando por ID
            $sql = "UPDATE periodo SET nombre = ?, fecha_inicio = ?, fecha_fin = ? WHERE id_periodo = ?";
            $stmt = $this->connection->prepare($sql);
            
            //? Manejo de error si la preparación falla
            if (!$stmt) {
                error_log("Error al preparar actualizarPeriodo: " . $this->connection->error);
                return false;
            }
            
            //? Vincula los parámetros (tres strings y un entero)
            $stmt->bind_param("sssi", $nombre, $fecha_inicio, $fecha_fin, $id_periodo);
            
            //? Ejecuta la sentencia y retorna el resultado
            return $stmt->execute();
        }

        //* Método para eliminar un período mediante su ID.
        public function eliminarPeriodo($id_periodo) {
            //? Sentencia SQL para eliminar un registro
            $sql = "DELETE FROM periodo WHERE id_periodo = ?";
            $stmt = $this->connection->prepare($sql);
            
            //? Manejo de error si la preparación falla
            if (!$stmt) {
                error_log("Error al preparar eliminarPeriodo: " . $this->connection->error);
                return false;
            }
            
            //? Vincula el parámetro (entero)
            $stmt->bind_param("i", $id_periodo);
            
            //? Ejecuta la sentencia y retorna el resultado
            return $stmt->execute();
        }
    }