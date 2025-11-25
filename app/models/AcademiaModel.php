<?php

    //! Clase Modelo para interactuar con la tabla 'academia' de la base de datos.
    //! Contiene métodos para el CRUD (Crear, Consultar, Actualizar y Eliminar) de academias.
    
    class AcademiaModel {
        private $connection;


        public function __construct($connection) {
            $this->connection = $connection;
        }


        //* Método para insertar un nuevo registro de academia.
        public function insertarAcademia($nombre, $siglas) {
            //? Sentencia SQL para la inserción
            $sql_statement = "INSERT INTO academia (nombre, siglas) VALUES (?, ?)";

            //? Prepara la sentencia
            $statement = $this->connection->prepare($sql_statement);

            //? Vincula los parámetros (dos strings)
            $statement->bind_param("ss", $nombre, $siglas);

            //? Ejecuta la sentencia y retorna el resultado
            return $statement->execute();
        }

        //* Método para consultar todos los registros de academias.
        public function consultarAcademias() {
            //? Sentencia SQL para obtener todos los campos ordenados por nombre
            $sql_statement = "SELECT id_academia, nombre, siglas FROM academia ORDER BY nombre";
            
            //? Ejecuta la consulta (no preparada, ya que no hay variables)
            $return = $this->connection->query($sql_statement);
            
            //? Retorna el objeto mysqli_result
            return $return;
        }

        //* Método para buscar una academia por su ID.
        public function buscarPorId($id_browser){
            //? Sentencia SQL con marcador de posición (?)
            $sql_statement = "SELECT id_academia, nombre, siglas FROM academia WHERE id_academia = ?";

            //? Prepara la sentencia
            $statement = $this -> connection -> prepare($sql_statement);
            
            //? Vincula el parámetro (entero)
            $statement -> bind_param("i", $id_browser); 

            //? Ejecuta la sentencia
            $statement -> execute();

            //? Obtiene el resultado
            $result = $statement -> get_result();

            //? Devuelve el resultado como un array asociativo (una sola fila)
            return $result -> fetch_assoc();
        }

        //* Método para actualizar el nombre y las siglas de una academia.
        public function editarAcademia($id,$nombre,$siglas){
            //? Sentencia SQL para actualizar, buscando por ID
            $sql_statement = "UPDATE academia SET nombre = ?, siglas = ? WHERE id_academia = ?";

            //? Prepara la sentencia
            $statement = $this -> connection -> prepare($sql_statement);

            //? Vincula los parámetros (dos strings y un entero)
            $statement -> bind_param("ssi",$nombre,$siglas,$id);

            //? Ejecuta la sentencia y retorna el resultado
            return $statement -> execute();
        }

        //* Método para eliminar una academia por su ID.
        public function eliminarAcademia($id_academia){
            //? Sentencia SQL para eliminar un registro
            $sql_statement = "DELETE FROM academia WHERE id_academia = ?";

            //? Prepara la sentencia
            $statement = $this -> connection -> prepare($sql_statement);

            //? Vincula el parámetro (entero)
            $statement -> bind_param("i",$id_academia);

            //? Ejecuta la sentencia y retorna el resultado
            return $statement -> execute();
        }
    }