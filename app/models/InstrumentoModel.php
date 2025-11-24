<?php
class InstrumentoModel {
    private $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    /**
     * Obtiene todos los instrumentos disponibles 
     */
    public function consultarTodos() {

        $sql = "SELECT id_instrumento, nombre, descripcion FROM instrumento ORDER BY id_instrumento DESC";
        $result = $this->connection->query($sql);
        
        // Retorna un array asociativo con todos los instrumentos
        return $result->fetch_all(MYSQLI_ASSOC); 
    }

    //* Método para insertar un nuevo instrumento
    public function insertarInstrumento($id_instrumento, $nombre, $descripcion) {
        // Los campos de tu BD son: id_instrumento, nombre, descripcion
        $sql_statement = "INSERT INTO instrumento (id_instrumento, nombre, descripcion) VALUES (?, ?, ?)";
        
        $statement = $this->connection->prepare($sql_statement);
        
        // Tipos de datos: i (INT) + ss (2 strings) -> 'iss'
        $statement->bind_param("iss", $id_instrumento, $nombre, $descripcion);

        return $statement->execute();
    }


    //* Método para consultar un instrumento por ID
    public function consultarPorID($id_instrumento) {
        $sql = "SELECT id_instrumento, nombre FROM instrumento WHERE id_instrumento = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id_instrumento);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc(); // Retorna un array asociativo (los detalles del instrumento)
    }
    
// ...
}
?>