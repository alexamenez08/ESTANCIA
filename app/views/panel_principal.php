<?php
$rol = $_SESSION['rol_usuario'];
$nombre = $_SESSION['nombre_usuario'];
$id_profesor = $_SESSION['id_profesor'] ?? null; 

// Determinar qué enlace del sidebar está activo (basado en el ancla)
// Esto es opcional, pero mejora la UI. Requeriría JS para hacerlo dinámico al scrollear.
$seccion_activa = $_GET['seccion'] ?? 'inicio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal</title>
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
</head>
<body>

    <div class="main-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <span class="logo">UPEMOR</span>
                <span class="logo-sub">Panel Principal</span>
            </div>
            <ul class="sidebar-menu">
                
                <!-- 
                  Enlaces del Sidebar (ahora apuntan a IDs en la página)
                  Estos enlaces se mostrarán según el ROL
                -->
                
                <?php if($rol == 'Administrador'): ?>
                    <li><a href="#gestion-sistema" class="sidebar-link <?php echo ($seccion_activa == 'gestion-sistema') ? 'active' : ''; ?>">Gestión del Sistema</a></li>
                    <li><a href="#evaluacion-seguimiento" class="sidebar-link <?php echo ($seccion_activa == 'evaluacion-seguimiento') ? 'active' : ''; ?>">Evaluación y Seguimiento</a></li>
                    <li><a href="#consultas-reportes" class="sidebar-link <?php echo ($seccion_activa == 'consultas-reportes') ? 'active' : ''; ?>">Consultas y Reportes</a></li>
                    <li><a href="#base-de-datos" class="sidebar-link <?php echo ($seccion_activa == 'base-de-datos') ? 'active' : ''; ?>">Base de Datos</a></li>

                <?php elseif($rol == 'Coordinador'): ?>
                    <li><a href="#gestion-academica" class="sidebar-link active">Gestión Académica</a></li>
                    <!-- Añadir más enlaces de ancla para el Coordinador si tiene más tarjetas -->

                <?php else: // Profesor ?>
                    <li><a href="#perfil" class="sidebar-link active">Mi perfil</a></li>
                <?php endif; ?>
                
            </ul>
        </nav>

        <!-- ===== CONTENIDO PRINCIPAL ===== -->
        <div class="main-content">

            <!-- Cabecera del módulo -->
            <header class="module-header">
                <div class="header-title">
                    <h1 id="inicio">Panel Principal</h1>
                    <p>Bienvenido(a), <?php echo htmlspecialchars($nombre); ?></p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol); ?></span>
                    <a href="index.php?controlador=acceso&accion=cerrarSesion" class="logout-link">Cerrar Sesión</a>
                </div>
            </header>

            <!-- Contenido (Tus tarjetas de rol) -->
            <main class="dashboard-content">

                <?php if($rol == 'Administrador'): ?>
                    <p class="role-message role-admin">Acceso de Administrador</p>

                    <!-- Tarjeta de Gestión (Añadido ID) -->
                    <section class="card" id="gestion-sistema">
                        <h2>Gestión del Sistema</h2>
                        <div class="module-grid">
                            <a href="index.php?controlador=user&accion=consultarUsuarios" class="button-link">Gestión de Usuarios</a>
                            <a href="index.php?controlador=academia&accion=consultarAcademias" class="button-link">Gestión de Academias</a>
                            <a href="index.php?controlador=materia&accion=consultarMaterias" class="button-link">Gestión de Materias</a>
                            <a href="index.php?controlador=periodo&accion=consultarPeriodos" class="button-link">Gestión de Periodos</a>
                        </div>
                    </section>

                    <!-- Tarjeta de Evaluación (Añadido ID) -->
                    <section class="card" id="evaluacion-seguimiento">
                        <h2>Evaluación y Seguimiento</h2>
                        <div class="module-grid">
                            <a href="index.php?controlador=instrumento&accion=consultarInstrumentos" class="button-link">Gestión de Instrumentos</a>
                            <a href="index.php?controlador=aplicacion&accion=asignar" class="button-link">Asignar Evaluación</a>
                            <a href="index.php?controlador=aplicacion&accion=consultarAplicaciones" class="button-link">Aplicar/Consultar Evaluaciones</a>
                        </div>
                    </section>

                    <!-- Tarjeta de Consultas y Reportes (Añadido ID) -->
                    <section class="card" id="consultas-reportes">
                        <h2>Consultas y Reportes</h2>
                        <div class="module-grid">
                            <a href="index.php?controlador=user&accion=consultarPorAcademia" class="button-link">Consulta (Profesores por Academia)</a>
                            <a href="index.php?controlador=user&accion=consultarPorMateria" class="button-link">Consulta (Profesores por Materia)</a>
                            <a href="index.php?controlador=report&accion=vistaReporteProfesor" class="button-link">Reporte (Individual por Profesor)</a>
                            <a href="index.php?controlador=report&accion=reportePorAcademia" class="button-link">Reporte (Avance por Academia)</a>
                            <a href="index.php?controlador=report&accion=generarReporteEstadoGrafico" class="button-link">Reporte Gráfico: Avance de Evaluaciones</a>
                        </div>
                    </section>

                    <!-- Tarjeta de Base de Datos (Añadido ID) -->
                    <section class="card" id="base-de-datos">
                        <h2>Base de Datos</h2>
                        <div class="module-grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
                            <a href="index.php?controlador=respaldo&accion=realizarRespaldoBD" class="button-link button-alt">Respaldos</a>
                            <a href="index.php?controlador=respaldo&accion=restaurarBD" class="button-link button-alt">Restaurar</a>
                        </div>
                    </section>

                <?php elseif($rol == 'Coordinador'): ?>
                    <p class="role-message role-coord">Acceso de Coordinador</p>
                    
                    <section class="card" id="gestion-academica">
                        <h2>Gestión Académica</h2>
                        <div class="module-grid">
                            <a href="index.php?controlador=user&accion=consultarUsuarios" class="button-link">Gestión de Usuarios</a>
                            <a href="index.php?controlador=academia&accion=consultarAcademias" class="button-link">Gestionar Academias</a>
                            <a href="index.php?controlador=materia&accion=consultarMaterias" class="button-link">Gestionar Materias</a>
                            <!-- Añade aquí los otros enlaces a los que el coordinador debe tener acceso -->
                        </div>
                    </section>

                    <!-- El coordinador también gestiona instrumentos y valida estados -->
                        <section class="card" id="evaluacion-seguimiento">
                            <h2>Evaluación y Seguimiento</h2>
                            <div class="module-grid">
                                <!-- FN.4 – Gestión de instrumento: crear y editar formularios -->
                                <a href="index.php?controlador=instrumento&accion=consultarInstrumentos" class="button-link">
                                    Gestión de Instrumentos
                                </a>
                                <!-- Asignar evaluaciones (FN relacionado) -->
                                <a href="index.php?controlador=aplicacion&accion=asignar" class="button-link">
                                    Asignar Evaluación
                                </a>
                                <!-- Validar estado de aplicación: pendiente / en proceso / completado -->
                                <a href="index.php?controlador=aplicacion&accion=consultarAplicaciones" class="button-link">
                                    Aplicar/Consultar Evaluaciones
                                </a>
                            </div>
                        </section>

                        <!-- Reutilizar también las consultas y reportes del admin -->
                        <section class="card" id="consultas-reportes">
                            <h2>Consultas y Reportes</h2>
                            <div class="module-grid">
                                <a href="index.php?controlador=user&accion=consultarPorAcademia" class="button-link">
                                    Consulta (Profesores por Academia)
                                </a>
                                <a href="index.php?controlador=user&accion=consultarPorMateria" class="button-link">
                                    Consulta (Profesores por Materia)
                                </a>
                                <a href="index.php?controlador=report&accion=vistaReporteProfesor"
                                    class="button-link">
                                    Reporte (Individual por Profesor)
                                </a>

                            </div>
                        </section>

                <?php else: ?>
                    <section class="card" id="perfil">
                        <h2>Mi Cuenta y Evaluaciones</h2>
                        <div class="module-grid">
                <!-- Ver Perfil Propio -->
                 <a href="index.php?controlador=profesorpanel&accion=verPerfil" class="button-link">
                    Ver mi Perfil y Materias
                </a>
                <!-- Seguimiento de Evaluaciones -->
                 <a href="index.php?controlador=profesorpanel&accion=seguimientoEvaluaciones" class="button-link">
                    Seguimiento de Evaluaciones
                </a>
            </div>
        </section>
                <?php endif; ?>

                <!-- Mensaje de Restauración-->
                <?php if(isset($restore)): ?>
                    <div class="restore-message">
                        <?php echo $restore; ?>
                    </div>
                    <script>
                        setTimeout(function() {
                            window.location.href = "index.php?controlador=acceso&accion=panelPrincipal";
                        }, 3000);
                    </script>
                <?php endif; ?>

            </main>
        </div>
    </div>
</body>
</html>