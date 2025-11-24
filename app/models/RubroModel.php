<?php
//* Modelo para la tabla 'rubro'
class RubroModel {
    private $connection;

    //* Constructor para recibir la conexión
    public function __construct($connection) {
        $this->connection = $connection;
    }

    //* Método para obtener todos los rubros de un instrumento
    public function getRubrosPorInstrumento($id_instrumento) {
        $sql = "SELECT id_rubro, texto_aspecto, orden 
                FROM rubro 
                WHERE id_instrumento = ? 
                ORDER BY orden ASC";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id_instrumento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        return $resultado->fetch_all(MYSQLI_ASSOC); // Devuelve un array
    }

    //* Método para crear un nuevo rubro
    public function crearRubro($id_instrumento, $texto, $orden) {
        $sql = "INSERT INTO rubro (id_instrumento, texto_aspecto, orden) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("isi", $id_instrumento, $texto, $orden);
        return $stmt->execute();
    }

    //* Método para actualizar un rubro
    public function actualizarRubro($id_rubro, $texto, $orden) {
        $sql = "UPDATE rubro SET texto_aspecto = ?, orden = ? WHERE id_rubro = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("sii", $texto, $orden, $id_rubro);
        return $stmt->execute();
    }
    
    //* Método para eliminar un rubro
    public function eliminarRubro($id_rubro) {
        $sql = "DELETE FROM rubro WHERE id_rubro = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id_rubro);
        return $stmt->execute();
    }

    // (También necesitarás un InstrumentoModel para getInstrumento($id), etc.)
}
?>