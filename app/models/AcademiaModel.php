<?php

    class AcademiaModel {
        private $connection;

        public function __construct($connection) {
            $this->connection = $connection;
        }


        public function insertarAcademia($nombre, $siglas) {
            $sql_statement = "INSERT INTO academia (nombre, siglas) VALUES (?, ?)";

            $statement = $this->connection->prepare($sql_statement);

            $statement->bind_param("ss", $nombre, $siglas);

            return $statement->execute();
        }

  
        public function consultarAcademias() {
            $sql_statement = "SELECT id_academia, nombre, siglas FROM academia ORDER BY nombre";
            $return = $this->connection->query($sql_statement);
            return $return;
        }

        public function buscarPorId($id_browser){
            $sql_statement = "SELECT id_academia, nombre, siglas FROM academia WHERE id_academia = ?";

            $statement = $this -> connection -> prepare($sql_statement);
            $statement -> bind_param("i", $id_browser); 

            $statement -> execute();

            $result = $statement -> get_result();

            return $result -> fetch_assoc();
        }

        public function editarAcademia($id,$nombre,$siglas){
            $sql_statement = "UPDATE academia SET nombre = ?, siglas = ? WHERE id_academia = ?";

            $statement = $this -> connection -> prepare($sql_statement);

            $statement -> bind_param("ssi",$nombre,$siglas,$id);

            return $statement -> execute();
        }

        public function eliminarAcademia($id_academia){
            $sql_statement = "DELETE FROM academia WHERE id_academia = ?";

            $statement = $this -> connection -> prepare($sql_statement);

            $statement -> bind_param("i",$id_academia);

            return $statement -> execute();
        }


    }