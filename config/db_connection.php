<?php

    $server = "localhost";
    $user = "root";
    $password = "";
    $db = "sistema_academico";

    //* Función para la conexión a la BD
    $connection = new mysqli($server, $user, $password, $db);

    if($connection -> connect_errno){
        //* Conexión fallida
        die("Error en la conexión" . $connection -> connect_errno);    
    }else{
        //* Conexión exitosa
        //echo "Conexión exitosa :)";
    }
