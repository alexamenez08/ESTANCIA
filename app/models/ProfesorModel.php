<?php
    //! Clase Modelo para interactuar con la tabla 'profesor' y gestionar la información
    //! de los docentes, incluyendo la autenticación por matrícula.
    class ProfesorModel { 

        private $conexion;

        public function __construct($conexion) {
            $this->conexion = $conexion;
        }

        //* Método para buscar un profesor por su matrícula, utilizado en el proceso de login.
        public function buscarPorMatricula($matricula) {

            //? Sentencia SQL para obtener los datos necesarios para el login (incluyendo la clave hasheada y el rol)
            $consulta = "SELECT 
                            id_profesor, nombre, apellido_pa, apellido_ma, matricula, pass, rol 
                        FROM profesor 
                        WHERE matricula = ?";
            
            //? Prepara la sentencia para prevenir inyección SQL
            $declaracion = $this->conexion->prepare($consulta);
            
            //? Vincula la matrícula (string)
            $declaracion->bind_param("s", $matricula); 
            $declaracion->execute();
            $resultado = $declaracion->get_result();

            //? Si se encuentra exactamente un registro, devuelve los datos
            if($resultado->num_rows == 1){
                return $resultado->fetch_assoc();
            }else{
                //? Si no se encuentra o hay duplicados, retorna falso
                return false;
            }
        }
        
    }