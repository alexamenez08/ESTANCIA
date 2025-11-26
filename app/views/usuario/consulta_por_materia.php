<?php
// Obtener datos de sesión para el header
$rol = $_SESSION['rol_usuario'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Profesores por Materia</title>
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
</head>
<body>

    <div class="main-container">
        
        <nav class="sidebar">
            <div class="sidebar-header">
                <span class="logo">UPEMOR</span>
                <span class="logo-sub">Consulta</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php?controlador=user&accion=consultarPorMateria" class="sidebar-link active">Consulta</a></li>
            </ul>
        </nav>

        <div class="main-content">
            
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

            <div class="form-card">
                <h3>Consulta: Profesores por Materia</h3>
                
                <form action="index.php" method="GET" class="form-filter">
                    <input type="hidden" name="controlador" value="user">
                    <input type="hidden" name="accion" value="consultarPorMateria">

                    <div class="form-grid">
                        <div class="form-field full-width">
                            <label for="id_materia">Seleccionar Materia:</label>
                            <select name="id_materia" id="id_materia">
                                <option value="">-- Seleccione una Materia --</option>
                                <?php 
                                // Reiniciamos el puntero para la impresión si fuera necesario
                                if (isset($materias_lista) && $materias_lista instanceof mysqli_result) $materias_lista->data_seek(0);
                                while($materia = (isset($materias_lista) && $materias_lista instanceof mysqli_result) ? $materias_lista->fetch_assoc() : null):
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
            
            <div class="form-card">
                <h3>Resultados de la Consulta</h3>

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
                        $conteo = 0; // Inicializar conteo para el resumen final
                        if (isset($profesores) && $profesores->num_rows > 0):
                            while($prof = $profesores->fetch_assoc()): 
                                $conteo++;
                        ?>
                            <tr>
                                <td class="profesor-name-strong"><?php echo htmlspecialchars($prof['nombre'] . ' ' . $prof['apellido_pa'] . ' ' . $prof['apellido_ma']); ?></td>
                                <td><?php echo htmlspecialchars($prof['nombre_materia']); ?></td>
                                <td>
                                    <span class="badge-academia">
                                        <?php echo htmlspecialchars($prof['nombre_academia'] ?? '— N/A —'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php 
                            endwhile; 
                        else:
                        ?>
                            <tr>
                                <td colspan="3" class="no-data-cell-small-padding">
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
                    <p class="result-summary">
                        <?php echo $conteo; ?> resultado(s) encontrado(s).
                    </p>
                <?php endif; ?>
            </div>
            
            <br>
            <a href="index.php?controlador=acceso&accion=panelPrincipal" class="back-to-panel">
                &larr; Volver al Panel Principal
            </a>

        </div> </div> </body>
</html>