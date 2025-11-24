<?php
class ProfesorModel { 

    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function buscarPorMatricula($matricula) {

        $consulta = "SELECT 
                        id_profesor, nombre, apellido_pa, apellido_ma, matricula, pass, rol 
                     FROM profesor 
                     WHERE matricula = ?";
        
        $declaracion = $this->conexion->prepare($consulta);
        
        $declaracion->bind_param("s", $matricula); 
        $declaracion->execute();
        $resultado = $declaracion->get_result();

        if($resultado->num_rows == 1){
            return $resultado->fetch_assoc();
        }else{
            return false;
        }
    }
}