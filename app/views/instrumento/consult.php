<?php
    // Obtener el rol actual para las restricciones de seguridad (si aplica)
    $rol_actual = $_SESSION['rol_usuario'] ?? 'Usuario'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Instrumentos</title>
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
</head>
<body>

    <!-- Contenedor principal -->
    <div class="main-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <span class="logo">UPEMOR</span>
                <span class="logo-sub">Gestión Académica</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php?controlador=instrumento&accion=consultarInstrumentos" class="sidebar-link active">Instrumentos</a></li>
            </ul>
        </nav>

        <!-- ===== CONTENIDO ===== -->
        <div class="main-content">
            
            <!-- Cabecera del módulo -->
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Gestión de Instrumentos</h1>
                    <p>Creación y edición de formatos de evaluación docente.</p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol_actual); ?></span>
                    <a href="index.php?controlador=acceso&accion=cerrarSesion" class="logout-link">Cerrar Sesión</a>
                </div>
            </header>

            <!-- Tarjeta de Consulta (Tabla) -->
            <div class="form-card">
                
                <div class="button-group-header">
                    <h2>Instrumentos Disponibles</h2>
                    <!-- Botón para ir a registrar nuevo -->
                    <a href="index.php?controlador=instrumento&accion=crearInstrumento" class="button-primary">
                        + Crear Nuevo Instrumento
                    </a>
                </div>

                <!-- TABLA ESTILIZADA -->
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="col-id">ID</th>
                            <th class="col-nombre">Nombre</th>
                            <th class="col-desc">Descripción</th>
                            <th class="col-acciones">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($instrumentos)): ?>
                            <tr>
                                <td colspan="4" class="text-center no-data">No hay instrumentos registrados.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($instrumentos as $inst): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($inst['id_instrumento']); ?></td>
                                    <td><?php echo htmlspecialchars($inst['nombre']); ?></td>
                                    <td class="text-sm"><?php echo htmlspecialchars(substr($inst['descripcion'], 0, 80)) . '...'; ?></td>
                                    <td class="text-center">
                                        
                                        <!-- Botón Editar Rubros -->
                                        <a href="index.php?controlador=instrumento&accion=editar&id=<?php echo $inst['id_instrumento']; ?>"
                                           class="button-secondary btn-table-action">
                                            Editar Rubros
                                        </a>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <br>
            <a href="index.php?controlador=acceso&accion=panelPrincipal" class="button-secondary">
                Volver al Panel Principal
            </a>

        </div> <!-- fin main-content -->
    </div> <!-- fin main-container -->
</body>
</html>