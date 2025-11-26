<?php
    //? Incluir el modelo de Profesor que contiene la lógica de autenticación
    include_once "app/models/ProfesorModel.php";
    include_once "config/db_connection.php";

    //! Clase controladora para gestionar el acceso de usuarios (Inicio de Sesión y Cierre de Sesión),
    //! y la navegación principal (Panel Principal).
    class AccesoController {
        private $modeloProfesor;

        public function __construct($conexion) {
            //? Inicializa el modelo para buscar profesores
            $this->modeloProfesor = new ProfesorModel($conexion);
        }

        //* Método para mostrar la página de inicio de sesión.
        public function paginaInicioSesion() {
            //? Si el usuario ya está logueado, redirige al panel principal
            if (isset($_SESSION['rol_usuario'])) {
                header("Location: index.php?controlador=acceso&accion=panelPrincipal");
                exit;
            }
            //? Carga la vista del formulario de login
            include "app/views/inicio_sesion.php";
        }

        //* Método para procesar el formulario e iniciar la sesión del usuario.
        public function iniciarSesion() {
            //? Verifica que se hayan recibido la matrícula y la clave
            if (isset($_POST['matricula']) && isset($_POST['clave'])) {
                
                //? Captura y sanea los datos de entrada
                $matricula_ingresada = trim($_POST['matricula']); 
                $clave_ingresada = $_POST['clave'];

                //? Busca al profesor por matrícula en la base de datos
                $profesor = $this->modeloProfesor->buscarPorMatricula($matricula_ingresada); 
                
                //? Verifica si el profesor existe Y si la clave coincide (usando password_verify para claves hasheadas)
                if ($profesor && password_verify($clave_ingresada, $profesor['pass'])) { 
                    
                    //? Si la autenticación es exitosa, establece las variables de sesión
                    $_SESSION['id_usuario'] = $profesor['id_profesor'];
                    $_SESSION['rol_usuario'] = $profesor['rol'];
                    $_SESSION['id_profesor'] = $profesor['id_profesor'];
                    
                    //? Almacena el nombre completo para la interfaz
                    $_SESSION['nombre_usuario'] = $profesor['nombre'] . ' ' . $profesor['apellido_pa'] . ' ' . $profesor['apellido_ma'];
                    
                    //? Redirige al panel principal
                    header("Location: index.php?controlador=acceso&accion=panelPrincipal");
                    exit;

                } else {
                    //? Si falla la validación, redirige con un mensaje de error
                    header("Location: index.php?controlador=acceso&accion=paginaInicioSesion&error=1");
                    exit;
                }
            } else {
                //? Si faltan datos en el formulario (acceso directo sin datos), redirige al login
                header("Location: index.php?controlador=acceso&accion=paginaInicioSesion");
                exit;
            }
        }

        //* Método para mostrar el panel principal de la aplicación.
        public function panelPrincipal() {
            //? Si no hay una sesión activa, redirige al login
            if (!isset($_SESSION['rol_usuario'])) {
                header("Location: index.php?controlador=acceso&accion=paginaInicioSesion");
                exit;
            }
            //? Carga la vista del panel principal
            include "app/views/panel_principal.php";
        }

        //* Método para cerrar la sesión del usuario.
        public function cerrarSesion() {
            //? Elimina todas las variables de sesión
            session_unset();
            //? Destruye la sesión
            session_destroy();
            //? Redirige a la página de inicio de sesión
            header("Location: index.php?controlador=acceso&accion=paginaInicioSesion");
            exit;
        }

    }