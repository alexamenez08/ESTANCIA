<?php
include_once "app/models/ProfesorModel.php";

class AccesoController {
    private $modeloProfesor;

    public function __construct($conexion) {
        $this->modeloProfesor = new ProfesorModel($conexion);
    }

    public function paginaInicioSesion() {
        if (isset($_SESSION['rol_usuario'])) {
            header("Location: index.php?controlador=acceso&accion=panelPrincipal");
            exit;
        }
        include "app/views/inicio_sesion.php";
    }

    public function iniciarSesion() {
        if (isset($_POST['matricula']) && isset($_POST['clave'])) {
            
            $matricula_ingresada = trim($_POST['matricula']); 
            $clave_ingresada = $_POST['clave'];

            $profesor = $this->modeloProfesor->buscarPorMatricula($matricula_ingresada); 
            
            if ($profesor && password_verify($clave_ingresada, $profesor['pass'])) { 
                
                $_SESSION['id_usuario'] = $profesor['id_profesor'];
                $_SESSION['rol_usuario'] = $profesor['rol'];
                $_SESSION['id_profesor'] = $profesor['id_profesor'];
                
                $_SESSION['nombre_usuario'] = $profesor['nombre'] . ' ' . $profesor['apellido_pa'] . ' ' . $profesor['apellido_ma'];
                
                header("Location: index.php?controlador=acceso&accion=panelPrincipal");
                exit;

            } else {
                // Si falla la validación, redirige con el error.
                header("Location: index.php?controlador=acceso&accion=paginaInicioSesion&error=1");
                exit;
            }
        } else {
            // Si faltan datos en el formulario, redirige sin error.
            header("Location: index.php?controlador=acceso&accion=paginaInicioSesion");
            exit;
        }
    }

    public function panelPrincipal() {
        if (!isset($_SESSION['rol_usuario'])) {
            header("Location: index.php?controlador=acceso&accion=paginaInicioSesion");
            exit;
        }
        include "app/views/panel_principal.php";
    }

    public function cerrarSesion() {
        session_unset();
        session_destroy();
        header("Location: index.php?controlador=acceso&accion=paginaInicioSesion");
        exit;
    }

}