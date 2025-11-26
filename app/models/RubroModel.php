<?php
    //! Clase Modelo para interactuar con la tabla 'rubro', gestionando el CRUD de los aspectos
    //! a observar dentro de un instrumento de evaluación.
    class RubroModel {
        private $connection;

        //* Constructor para recibir la conexión a la base de datos.
        public function __construct($connection) {
            //? Inicializa la conexión
            $this->connection = $connection;
        }

        //* Método para obtener todos los rubros de un instrumento específico, ordenados por secuencia.

        public function getRubrosPorInstrumento($id_instrumento) {
            //? Sentencia SQL para seleccionar rubros por ID de instrumento y ordenar por 'orden'
            $sql = "SELECT id_rubro, texto_aspecto, orden 
                    FROM rubro 
                    WHERE id_instrumento = ? 
                    ORDER BY orden ASC";
            
            $stmt = $this->connection->prepare($sql);
            //? Vincula el ID de instrumento (integer)
            $stmt->bind_param("i", $id_instrumento);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            //? Devuelve un array con todos los rubros
            return $resultado->fetch_all(MYSQLI_ASSOC); 
        }

        //* Método para crear un nuevo rubro asociado a un instrumento.
        public function crearRubro($id_instrumento, $texto, $orden) {
            //? Sentencia SQL para la inserción
            $sql = "INSERT INTO rubro (id_instrumento, texto_aspecto, orden) VALUES (?, ?, ?)";
            $stmt = $this->connection->prepare($sql);
            //? Vincula los parámetros (integer, string, integer)
            $stmt->bind_param("isi", $id_instrumento, $texto, $orden);
            //? Ejecuta y retorna el resultado (true/false)
            return $stmt->execute();
        }

        //* Método para actualizar el texto y el orden de un rubro existente.
        public function actualizarRubro($id_rubro, $texto, $orden) {
            //? Sentencia SQL para actualizar, buscando por ID de rubro
            $sql = "UPDATE rubro SET texto_aspecto = ?, orden = ? WHERE id_rubro = ?";
            $stmt = $this->connection->prepare($sql);
            //? Vincula los parámetros (string, integer, integer)
            $stmt->bind_param("sii", $texto, $orden, $id_rubro);
            //? Ejecuta y retorna el resultado (true/false)
            return $stmt->execute();
        }
        
        //* Método para eliminar un rubro mediante su ID.
        public function eliminarRubro($id_rubro) {
            //? Sentencia SQL para eliminar un rubro
            $sql = "DELETE FROM rubro WHERE id_rubro = ?";
            $stmt = $this->connection->prepare($sql);
            //? Vincula el ID de rubro (integer)
            $stmt->bind_param("i", $id_rubro);
            //? Ejecuta y retorna el resultado (true/false)
            return $stmt->execute();
        }

    }