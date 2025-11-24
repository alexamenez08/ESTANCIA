<?php
// Obtener datos de sesión para el header
$rol = $_SESSION['rol_usuario'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Reporte Individual por Profesor</title>
    <!-- Enlazamos los estilos del panel y del CRUD -->
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
</head>
<body>

    <!-- Contenedor principal -->
    <div class="main-container">
        
        <!-- ===== SIDEBAR ===== -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <span class="logo">UPEMOR</span>
                <span class="logo-sub">Reportes</span>
            </div>
            <ul class="sidebar-menu">
                <!-- ... enlaces de navegación ... -->
                <li><a href="" class="sidebar-link active">Reporte</a></li>
            </ul>
        </nav>

        <!-- ===== CONTENIDO ===== -->
        <div class="main-content">
            
            <!-- Cabecera del módulo -->
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Reportes</h1>
                    <p>Generación de documentos PDF.</p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol); ?></span>
                    <a href="index.php?controlador=acceso&accion=cerrarSesion" class="logout-link">Cerrar Sesión</a>
                </div>
            </header>

            <!-- Tarjeta del Formulario de Selección -->
            <div class="form-card">
                <h3>Generar Reporte Individual por Profesor</h3>
                
                <p class="form-subtitle">Seleccione el profesor del cual desea generar el reporte individual de materias.</p>

                <!-- Formulario que apunta a generar el PDF -->
                <form action="index.php?controlador=report&accion=generarReporteProfesor" method="POST" target="_blank">
                    
                    <div class="form-grid">
                        <div class="form-field full-width">
                            <label for="id_profesor">Profesor:</label>
                            
                            <select name="id_profesor" id="id_profesor" required>
                                <option value="">-- Seleccione un profesor --</option>
                                <?php 
                                if (isset($profesores) && $profesores->num_rows > 0):
                                    // Debemos resetear el puntero si se usó la variable para algo más
                                    $profesores->data_seek(0); 
                                    while($prof = $profesores->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $prof['id_profesor']; ?>">
                                        <?php echo htmlspecialchars($prof['apellido_pa'] . ' ' . $prof['apellido_ma'] . ', ' . $prof['nombre']); ?>
                                    </option>
                                <?php 
                                    endwhile;
                                endif;
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="button-group">
                        <!-- Usamos input type="submit" para que se vea como button-primary -->
                        <input type="submit" value="Generar Reporte PDF" class="button-primary">
                    </div>
                </form>
            </div>
            
            <br>
            <a href="index.php?controlador=acceso&accion=panelPrincipal" class="button-secondary">
                &larr; Volver al Panel Principal
            </a>

        </div> <!-- fin main-content -->
    </div> <!-- fin main-container -->
</body>
</html>