<?php

    //? Incluir el modelo de Respaldo
    include_once "app/models/RespaldoModel.php";
    //? Incluir la conexión a la base de datos
    include_once "config/db_connection.php";

    //! Clase controladora para gestionar las acciones de Respaldo (Backup) y Restauración (Restore)
    //! de la base de datos.
    class RespaldoController{
        private $model;

        public function __construct($connection){
            //? Inicializa la instancia del modelo de Respaldo
            $this -> model = new RespaldoModel($connection);
        }

        //* Método para ejecutar el proceso de respaldo de la base de datos y forzar la descarga del archivo SQL.
        public function realizarRespaldoBD(){
            //? Parámetros de conexión de la BD (Recomendado: obtener de un archivo de configuración)
            $server = "localhost";
            $user = "root";
            $password = "";
            $db = "sistema_academico";

            //? Llama al modelo para generar el contenido del backup (el archivo se guarda dentro del modelo)
            $backup = $this -> model -> backup_tables($server, $user, $password, $db);

            //? Este echo es redundante si el método backup_tables ya guarda el archivo, pero se mantiene.
            echo $backup;

            $fecha = date("Y-m-d");

            //? Nombre del archivo SQL a descargar
            header("Content-disposition: attachment; filename=db-backup-".$fecha.".sql");
            //? Establece el tipo de contenido para forzar la descarga binaria
            header("Content-type: MIME");
            //? Lee el archivo generado y lo envía al navegador para la descarga
            readfile("config/backups/db-backup-".$fecha.".sql");
            
        }

        //* Método para ejecutar el proceso de restauración de la base de datos.
        public function restaurarBD(){
            $fecha = date("Y-m-d");

            //? Define la ruta del archivo de respaldo a usar (se asume el del día actual)
            $ruta = "config/backups/db-backup-".$fecha.".sql";

            //? Llama al modelo para ejecutar la restauración
            $restore = $this -> model -> restaurarBD($ruta);

            //? Incluye la vista principal (donde se puede mostrar el mensaje $restore)
            include_once "app/views/panel_principal.php";
        }

    }