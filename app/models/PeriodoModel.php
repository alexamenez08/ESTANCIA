<?php
    class PeriodoModel {
        private $connection;

        public function __construct($connection) {
            $this->connection = $connection;
        }

        public Function insertarPeriodo($nombre, $fecha_inicio, $fecha_fin){
            // Corregí el bind_param: sss (string, string, string) para nombre, fecha_inicio, fecha_fin.
            $sql_statement = "INSERT INTO periodo (nombre, fecha_inicio, fecha_fin) VALUES (?, ?, ?)";
            
            $statement = $this -> connection -> prepare($sql_statement);
            
            $statement -> bind_param("sss",$nombre, $fecha_inicio, $fecha_fin);

            return $statement -> execute();
        }

        //* Obtiene todos los períodos disponibles
        public function consultarTodos() {
            $sql = "SELECT id_periodo, nombre, fecha_inicio, fecha_fin FROM periodo ORDER BY id_periodo DESC";
            $result = $this->connection->query($sql);
            
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        // * NUEVO: Obtiene un período por su ID
        public function consultarPorID($id_periodo) {
            $sql = "SELECT id_periodo, nombre, fecha_inicio, fecha_fin FROM periodo WHERE id_periodo = ?";
            $stmt = $this->connection->prepare($sql);
            
            if (!$stmt) {
                error_log("Error al preparar consultarPorID: " . $this->connection->error);
                return false;
            }
            
            $stmt->bind_param("i", $id_periodo);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_assoc();
        }

        // * NUEVO: Actualiza un período
        public function actualizarPeriodo($id_periodo, $nombre, $fecha_inicio, $fecha_fin) {
            $sql = "UPDATE periodo SET nombre = ?, fecha_inicio = ?, fecha_fin = ? WHERE id_periodo = ?";
            $stmt = $this->connection->prepare($sql);
            
            if (!$stmt) {
                error_log("Error al preparar actualizarPeriodo: " . $this->connection->error);
                return false;
            }
            
            // Tipos de datos: sssi (string, string, string, integer)
            $stmt->bind_param("sssi", $nombre, $fecha_inicio, $fecha_fin, $id_periodo);
            
            return $stmt->execute();
        }

        // * NUEVO: Elimina un período
        public function eliminarPeriodo($id_periodo) {
            $sql = "DELETE FROM periodo WHERE id_periodo = ?";
            $stmt = $this->connection->prepare($sql);
            
            if (!$stmt) {
                error_log("Error al preparar eliminarPeriodo: " . $this->connection->error);
                return false;
            }
            
            $stmt->bind_param("i", $id_periodo);
            
            return $stmt->execute();
        }
    }
?>