<?php
    
    //! Clase Modelo para manejar la lógica de datos de Respaldo (dump) y Restauración (restore)
    //! de la base de datos MySQL, utilizando consultas directas.
    class RespaldoModel{

        private $conexion;
        
        public function __construct($conexion) {
            $this->conexion = $conexion;
        }

        //* Método para generar el contenido del respaldo de la base de datos.
        public function backup_tables($host,$user,$pass,$name,$tables = '*'){
            $return='';
            //? Se crea una nueva conexión para la operación de respaldo
            $link = new mysqli($host,$user,$pass,$name);
            
            //? Se obtienen los nombres de las tablas de datos si se eligen todas
            if($tables == '*')
            {
                $tables = array();
                $result = $link->query('SHOW TABLES');
                //? Guardar tablas de la base de datos en un array
                while($row = mysqli_fetch_row($result))
                {
                    $tables[] = $row[0];
                }
            }
            else
            {
                $tables = is_array($tables) ? $tables : explode(',',$tables);
            }
            
            //? Obtener los registros y estructura para cada tabla
            foreach($tables as $table)
            {
                //? 1. Obtiene todos los datos de la tabla
                $result = $link->query('SELECT * FROM '.$table);
                $num_fields = mysqli_num_fields($result);

                
                //? 2. Obtiene la sentencia CREATE TABLE
                $row2 = mysqli_fetch_row($link->query('SHOW CREATE TABLE '.$table));

                //? Añade la instrucción DROP TABLE IF EXISTS
                $return .= "\n\nDROP TABLE IF EXISTS `$table`;\n";

                //? Añade la sentencia CREATE TABLE completa
                $return.= "\n\n".$row2[1].";\n\n";
                
                //? 3. Genera las sentencias INSERT INTO
                for ($i = 0; $i < $num_fields; $i++)
                {
                    while($row = mysqli_fetch_row($result))
                    {
                        $return.= 'INSERT INTO '.$table.' VALUES(';
                        for($j=0; $j<$num_fields; $j++) 
                        {
                        //? Escapa caracteres especiales (slashes y saltos de línea)
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = preg_replace("/\n/","\\n",$row[$j]);
                        //? Formatea el valor (con comillas o cadena vacía)
                        if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                        if ($j<($num_fields-1)) { $return.= ','; }
                        }
                        $return.= ");\n";
                    }
                }
                $return.="\n\n\n";
            }

            //? Guardar el archivo SQL en disco
            $fecha=date("Y-m-d");
            //? Abre/crea el archivo para escribir las consultas.
            $handle = fopen('config/backups/db-backup-'.$fecha.'.sql','w+');
                fwrite($handle,$return);
                fclose($handle);
        }

        //* Método para restaurar la base de datos desde un archivo SQL.
        public function restaurarBD($ruta){
            //? 1. Desactivar la verificación de llaves foráneas para permitir restauraciones desordenadas.
            $desactivar_fk = "SET FOREIGN_KEY_CHECKS = 0;";
            $this -> conexion -> query($desactivar_fk); //? Ejecutar inmediatamente

            //? 2. Obtener el contenido del archivo .sql
            $query_archivo = file_get_contents($ruta);

            //? 3. Preparar el query completo: contenido del archivo + reactivación de llaves foráneas.
            $activar_fk = "SET FOREIGN_KEY_CHECKS = 1;";
            $query_completo = $query_archivo . $activar_fk; //? Concatenar la reactivación al final

            //? Ejecuta todos los queries usando multi_query
            if($this -> conexion -> multi_query($query_completo)){
                do{
                    //? Limpia los resultados de la consulta (necesario para multi_query)
                    if($result = $this -> conexion -> store_result()){
                        $result -> free();
                    }
                    //? Continúa al siguiente query
                }while($this -> conexion -> more_results() && $this -> conexion -> next_result());

                return "Restauración exitosa :)";
            }else{
                //? Si falla, es bueno intentar reactivar las FK para no dejar la BD inconsistente.
                $this -> conexion -> query($activar_fk); 
                return "Error en la restauración :(";
            }
        }
        

    }