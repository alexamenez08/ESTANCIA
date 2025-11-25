<?php

    session_start();

    // 1. Incluir SOLO la conexión a la BD y dependencias globales (FPDF)
    require_once "config/db_connection.php";
    include_once "public/libraries/fpdf/fpdf.php";

    // 2. Definir el controlador y la acción solicitados
    $controladorSolicitado = $_GET['controlador'] ?? 'acceso'; // 'acceso' es el default
    $accionSolicitada = $_GET['accion'] ?? 'paginaInicioSesion'; // 'paginaInicioSesion' es el default

    // 3. Definir las rutas públicas (a las que se puede acceder SIN iniciar sesión)
    $rutasPublicas = [
        'acceso' => ['paginaInicioSesion', 'iniciarSesion']
    ];

    // 4. Comprobar si el usuario NO está logueado
    if (!isset($_SESSION['rol_usuario'])) {
        
        // 5. Comprobar si la ruta solicitada NO es pública
        $esRutaPublica = false;
        if (isset($rutasPublicas[$controladorSolicitado])) {
            if (in_array($accionSolicitada, $rutasPublicas[$controladorSolicitado])) {
                $esRutaPublica = true;
            }
        }

        // 6. Si NO está logueado y la ruta NO es pública -> REDIRIGIR A INICIO
        if (!$esRutaPublica) {
            // Redirigir a la página de inicio de sesión
            header("Location: index.php?controlador=acceso&accion=paginaInicioSesion&error=no_auth");
            exit(); 
        }
    }

    // 7. Si llegamos aquí, el usuario ESTÁ logueado O está solicitando una página pública.

    $nombreControlador = ucfirst($controladorSolicitado) . 'Controller';
    $nombreAccion = $accionSolicitada;

    $archivoControlador = "app/controllers/" . $nombreControlador . ".php";

    // Comprobar si el archivo del controlador existe
    if (file_exists($archivoControlador)) {
        
        // Cargar SOLO el controlador necesario
        require_once $archivoControlador;

        // Comprobar si la clase existe
        if (class_exists($nombreControlador)) {
            
            // Crear la instancia (pasando la conexión desde db_connection.php)
            $controlador = new $nombreControlador($connection); 
            
            // Comprobar si el método (acción) existe
            if (method_exists($controlador, $nombreAccion)) {
                $controlador->$nombreAccion();
            } else {
                die("Error: La acción '{$nombreAccion}' no existe en el controlador '{$nombreControlador}'.");
            }
        } else {
            die("Error: La clase '{$nombreControlador}' no se encontró en el archivo '{$archivoControlador}'.");
        }
    } else {
        // Si el archivo no existe
        die("Error: El controlador '{$nombreControlador}' no existe en la ruta '{$archivoControlador}'. (Revisa mayúsculas/minúsculas)");
    }


?>