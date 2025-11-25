<?php
    //! Clase Modelo para interactuar con la tabla 'instrumento', gestionando el CRUD de instrumentos.
    class InstrumentoModel {
        private $connection;
        
        public function __construct($connection) {
            //? Inicializa la conexión
            $this->connection = $connection;
        }

        //* Método para obtener todos los instrumentos disponibles.
        public function consultarTodos() {
            //? Sentencia SQL para obtener todos los instrumentos ordenados por ID
            $sql = "SELECT id_instrumento, nombre, descripcion FROM instrumento ORDER BY id_instrumento DESC";
            //? Ejecuta la consulta
            $result = $this->connection->query($sql);
            
            //? Retorna un array asociativo con todos los instrumentos
            return $result->fetch_all(MYSQLI_ASSOC); 
        }

        //* Método para insertar un nuevo instrumento.
        public function insertarInstrumento($id_instrumento, $nombre, $descripcion) {
            //? Sentencia SQL para la inserción (id_instrumento, nombre, descripcion)
            $sql_statement = "INSERT INTO instrumento (id_instrumento, nombre, descripcion) VALUES (?, ?, ?)";
            
            //? Prepara la sentencia
            $statement = $this->connection->prepare($sql_statement);
            
            //? Vincula los parámetros (INT, STRING, STRING)
            $statement->bind_param("iss", $id_instrumento, $nombre, $descripcion);

            //? Ejecuta la sentencia y retorna el resultado
            return $statement->execute();
        }


        //* Método para consultar un instrumento por ID.
        public function consultarPorID($id_instrumento) {
            //? Sentencia SQL con marcador de posición (?)
            $sql = "SELECT id_instrumento, nombre FROM instrumento WHERE id_instrumento = ?";
            //? Prepara la sentencia
            $stmt = $this->connection->prepare($sql);
            //? Vincula el parámetro (entero)
            $stmt->bind_param("i", $id_instrumento);
            //? Ejecuta la sentencia
            $stmt->execute();
            //? Obtiene el resultado
            $result = $stmt->get_result();
            
            //? Retorna un array asociativo (los detalles del instrumento)
            return $result->fetch_assoc(); 
        }
        
    }