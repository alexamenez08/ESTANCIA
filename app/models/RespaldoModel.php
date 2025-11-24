<?php
    class RespaldoModel{

        private $conexion;
        public function __construct($conexion) {
            $this->conexion = $conexion;
        }

         //* Método para el respaldo de la BD
         public function backup_tables($host,$user,$pass,$name,$tables = '*'){
            $return='';
            $link = new mysqli($host,$user,$pass,$name);
            
            // Se obtienen los nombres de las tablas de datos si se eligen todas
            if($tables == '*')
            {
                $tables = array();
                $result = $link->query('SHOW TABLES');
                // Guardar tablas de la base de datos
                while($row = mysqli_fetch_row($result))
                {
                    $tables[] = $row[0];
                }
            }
            else
            {
                $tables = is_array($tables) ? $tables : explode(',',$tables);
            }
            
            // Obtener los registros de la tabla de datos
            foreach($tables as $table)
            {
                $result = $link->query('SELECT * FROM '.$table);
                $num_fields = mysqli_num_fields($result);

                
                $row2 = mysqli_fetch_row($link->query('SHOW CREATE TABLE '.$table));

                $return .= "\n\nDROP TABLE IF EXISTS `$table`;\n";

                $return.= "\n\n".$row2[1].";\n\n";
                
                for ($i = 0; $i < $num_fields; $i++)
                {
                    while($row = mysqli_fetch_row($result))
                    {
                        $return.= 'INSERT INTO '.$table.' VALUES(';
                        for($j=0; $j<$num_fields; $j++) 
                        {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = preg_replace("/\n/","\\n",$row[$j]);
                        if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                        if ($j<($num_fields-1)) { $return.= ','; }
                        }
                        $return.= ");\n";
                    }
                }
                $return.="\n\n\n";
            }

            // Guardar el nombre de la tabla de datos
            $fecha=date("Y-m-d");
            // Abrir el archivo para escribir las consultas. 
            $handle = fopen('config/backups/db-backup-'.$fecha.'.sql','w+');
                fwrite($handle,$return);
                fclose($handle);
        }

        public function restaurarBD($ruta){
            // 1. Desactivar la verificación de llaves foráneas para permitir restauraciones desordenadas.
            $desactivar_fk = "SET FOREIGN_KEY_CHECKS = 0;";
            $this -> conexion -> query($desactivar_fk); // Ejecutar inmediatamente

            // 2. Obtener el contenido del archivo .sql
            $query_archivo = file_get_contents($ruta);

            // 3. Preparar el query completo: contenido del archivo + reactivación de llaves foráneas.
            $activar_fk = "SET FOREIGN_KEY_CHECKS = 1;";
            $query_completo = $query_archivo . $activar_fk; // Concatenar la reactivación al final

            // Validar que existen múltiples querys y ejecutarlos
            // Ahora se ejecuta el contenido del archivo MÁS el comando de reactivación.
            if($this -> conexion -> multi_query($query_completo)){
                do{
                    // Almacenar los resultados en una variable
                    if($result = $this -> conexion -> store_result()){
                        $result -> free();
                    }
                }while($this -> conexion -> more_results() && $this -> conexion -> next_result());

                return "Restauración exitosa :)";
            }else{
                // Si falla, es bueno intentar reactivar las FK para no dejar la BD en estado inconsistente.
                $this -> conexion -> query($activar_fk); 
                return "Error en la restauración :(";
            }
        }
        
    }