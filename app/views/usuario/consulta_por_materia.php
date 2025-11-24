<?php
// Obtener datos de sesión para el header
$rol = $_SESSION['rol_usuario'] ?? 'Usuario';
// Asumimos que $filtro_id_materia y $materias_lista vienen del controlador
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Profesores por Materia</title>
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
                <span class="logo-sub">Consulta</span>
            </div>
            <ul class="sidebar-menu">
                <!-- ... enlaces de navegación ... -->
                <!-- Marcamos Consultas y Reportes como activo -->
                <li><a href="index.php?controlador=user&accion=consultarPorMateria" class="sidebar-link active">Consulta</a></li>
                <!-- ... otros enlaces ... -->
            </ul>
        </nav>

        <!-- ===== CONTENIDO ===== -->
        <div class="main-content">
            
            <!-- Cabecera del módulo -->
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Consultas</h1>
                    <p>Filtro de docentes por materia impartida.</p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol); ?></span>
                    <a href="index.php?controlador=acceso&accion=cerrarSesion" class="logout-link">Cerrar Sesión</a>
                </div>
            </header>

            <!-- 1. Tarjeta de Filtros (Formulario GET) -->
            <div class="form-card">
                <h3>Consulta: Profesores por Materia</h3>
                
                <form action="index.php" method="GET" style="padding-top: 10px;">
                    <!-- Campos ocultos para el controlador y acción -->
                    <input type="hidden" name="controlador" value="user">
                    <input type="hidden" name="accion" value="consultarPorMateria">

                    <div class="form-grid">
                        <div class="form-field full-width">
                            <label for="id_materia">Seleccionar Materia:</label>
                            <select name="id_materia" id="id_materia">
                                <option value="">-- Seleccione una Materia --</option>
                                <?php 
                                // Asumimos que $materias_lista es un objeto mysqli_result o un array asociativo
                                while($materia = $materias_lista->fetch_assoc()):
                                    $selected = ($filtro_id_materia == $materia['id_materia']) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo htmlspecialchars($materia['id_materia']); ?>" <?php echo $selected; ?>>
                                        <?php echo htmlspecialchars($materia['nombre_materia']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="button-group">
                        <input type="submit" value="Buscar Profesores" class="button-primary">
                        <a href="index.php?controlador=user&accion=consultarPorMateria" class="button-secondary">
                            Limpiar Filtros
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- 2. Tarjeta de Resultados -->
            <div class="form-card">
                <h3>Resultados de la Consulta</h3>

                <!-- TABLA DE RESULTADOS -->
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Profesor</th>
                            <th>Materia que imparte</th>
                            <th>Academia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (isset($profesores) && $profesores->num_rows > 0):
                            $conteo = 0;
                            while($prof = $profesores->fetch_assoc()): 
                                $conteo++;
                        ?>
                            <tr>
                                <td style="font-weight: 500;"><?php echo htmlspecialchars($prof['nombre'] . ' ' . $prof['apellido_pa'] . ' ' . $prof['apellido_ma']); ?></td>
                                <td><?php echo htmlspecialchars($prof['nombre_materia']); ?></td>
                                <td>
                                    <span style="background-color: #f4f0f8; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; color: #6a1b9a;">
                                        <?php echo htmlspecialchars($prof['nombre_academia'] ?? '— N/A —'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php 
                            endwhile; 
                        else:
                        ?>
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 20px;">
                                    <?php if (!empty($filtro_id_materia)): ?>
                                        No se encontraron profesores para la materia seleccionada.
                                    <?php else: ?>
                                        Seleccione una materia o no hay profesores asignados.
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if(isset($conteo) && $conteo > 0): ?>
                    <p style="text-align: right; margin-top: 10px; font-weight: 500; font-size: 0.9em; color: #6a1b9a;">
                        <?php echo $conteo; ?> resultado(s) encontrado(s).
                    </p>
                <?php endif; ?>
            </div>
            
            <br>
            <a href="index.php?controlador=acceso&accion=panelPrincipal" class="button-secondary">
                &larr; Volver al Panel Principal
            </a>

        </div> <!-- fin .main-content -->
    </div> <!-- fin .main-container -->
</body>
</html>