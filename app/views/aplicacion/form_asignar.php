<?php
    // Obtener datos de sesión
    $rol = $_SESSION['rol_usuario'] ?? 'Usuario';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Evaluación</title>
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
</head>
<body>
    
    <div class="main-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <span class="logo">UPEMOR</span>
                <span class="logo-sub">Evaluación y Seguimiento</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php?controlador=aplicacion&accion=asignar" class="sidebar-link active">Asignar Evaluación</a></li>
            </ul>
        </nav>

        <!-- ===== CONTENIDO ===== -->
        <div class="main-content">
            
            <!-- Cabecera -->
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Evaluación y Seguimiento</h1>
                    <p>Cree/asigne una evaluación.</p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol); ?></span>
                    <a href="index.php?controlador=acceso&accion=cerrarSesion" class="logout-link">Cerrar Sesión</a>
                </div>
            </header>

            <!-- Tarjeta del Formulario de Asignación -->
            <form action="index.php?controlador=aplicacion&accion=guardarAsignacion" method="POST" class="form-card">
                
                <h2>Asignar Evaluación Pendiente</h2>
                <p class="form-subtitle">Seleccione el docente a evaluar, el evaluador responsable y el instrumento a aplicar.</p>

                <div class="form-grid">

                    <!-- Docente a Evaluar -->
                    <div class="form-field">
                        <label for="id_profesor_evaluado">Docente a Evaluar:</label>
                        <select name="id_profesor_evaluado" id="id_profesor_evaluado" required>
                            <option value="">-- Seleccione Docente --</option>
                            <?php 
                            // Aseguramos que el puntero esté al inicio si la variable $profesores ya fue usada
                            if ($profesores) $profesores->data_seek(0); 
                            while($prof = $profesores->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $prof['id_profesor']; ?>">
                                    <?php echo htmlspecialchars("{$prof['nombre']} {$prof['apellido_pa']} {$prof['apellido_ma']}"); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Responsable (Evaluador) -->
                    <div class="form-field">
                        <label for="id_evaluador_responsable">Responsable (Evaluador):</label>
                        <select name="id_evaluador_responsable" id="id_evaluador_responsable" required>
                            <option value="">-- Seleccione Responsable --</option>
                            <?php 
                            if ($evaluadores) $evaluadores->data_seek(0);
                            while($eval = $evaluadores->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $eval['id_profesor']; ?>">
                                    <?php echo htmlspecialchars("{$eval['nombre']} {$eval['apellido_pa']}"); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Instrumento a Usar -->
                    <div class="form-field">
                        <label for="id_instrumento">Instrumento a Usar:</label>
                        <select name="id_instrumento" id="id_instrumento" required>
                            <option value="">-- Seleccione Instrumento --</option>
                            <?php 
                            if (isset($instrumentos) && is_array($instrumentos)) {
                                foreach($instrumentos as $inst): 
                            ?>
                                <option value="<?php echo $inst['id_instrumento']; ?>">
                                    <?php echo htmlspecialchars($inst['nombre']); ?>
                                </option>
                            <?php 
                                endforeach; 
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Período de Aplicación -->
                    <div class="form-field">
                        <label for="id_periodo">Período de Aplicación:</label>
                        <select name="id_periodo" id="id_periodo" required>
                            <option value="">-- Seleccione Período --</option>
                            <?php foreach($periodos as $per): ?>
                                <option value="<?php echo $per['id_periodo']; ?>">
                                    <?php echo htmlspecialchars($per['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div> <!-- Fin form-grid -->
                
                <div class="button-group">
                    <input type="submit" name="asignar_evaluacion" value="Crear Evaluacion Pendiente" class="button-primary">
                    <a href="index.php?controlador=aplicacion&accion=consultarAplicaciones" class="button-secondary">
                        Ver Evaluaciones Asignadas
                    </a>
                </div>
            </form>

            <br>
            <a href="index.php?controlador=acceso&accion=panelPrincipal" class="button-secondary">
                Volver al Panel Principal
            </a>

        </div> <!-- fin main-content -->
        
    </div> <!-- fin main-container -->

    
</body>
</html>