<?php

    include_once "config/db_connection.php";
    include_once "app/models/RespaldoModel.php";

    class RespaldoController{
        private $model;

        public function __construct($connection){
            $this -> model = new RespaldoModel($connection);
        }

        //* Método para realizar el respaldo
        public function realizarRespaldoBD(){
            $server = "localhost";
            $user = "root";
            $password = "";
            $db = "sistema_academico";

            $backup = $this -> model -> backup_tables($server, $user, $password, $db);

            echo $backup;

            $fecha = date("Y-m-d");

            // Funcion que permite crear y nombrar el archivo
            header("Content-disposition: attachment; filename=db-backup-".$fecha.".sql");
            // Permitir que el archivo se descargue y no se ejecute
            header("Content-type: MIME");
            // Leer el archivo del script y mandarlo con descarga al navegador
            readfile("config/backups/db-backup-".$fecha.".sql");
            
        }

        // Método para la restauración
        public function restaurarBD(){
            $fecha = date("Y-m-d");

            $ruta = "config/backups/db-backup-".$fecha.".sql";

            $restore = $this -> model -> restaurarBD($ruta);

            include_once "app/views/panel_principal.php";
        }

    }