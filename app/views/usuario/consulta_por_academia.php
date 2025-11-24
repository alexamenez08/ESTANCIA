<?php
    // Obtener datos de sesión para el header
    $rol = $_SESSION['rol_usuario'] ?? 'Usuario';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Profesores por Academia</title>
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
                <!-- Marcamos Consultas y Reportes como activo -->
                <li><a href="index.php?controlador=user&accion=consultarPorAcademia" class="sidebar-link active">Consulta</a></li>
            </ul>
        </nav>

        <!-- ===== CONTENIDO ===== -->
        <div class="main-content">
            
            <!-- Cabecera del módulo -->
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Consultas</h1>
                    <p>Filtro de docentes por área de conocimiento.</p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol); ?></span>
                    <a href="index.php?controlador=acceso&accion=cerrarSesion" class="logout-link">Cerrar Sesión</a>
                </div>
            </header>

            <!-- 1. Tarjeta de Filtros (Formulario GET) -->
            <div class="form-card">
                <h3>Consulta: Profesores por academia</h3>
                
                <form action="index.php" method="GET" class="form-filter">
                    <!-- Campos ocultos para el controlador y acción -->
                    <input type="hidden" name="controlador" value="user">
                    <input type="hidden" name="accion" value="consultarPorAcademia">

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="academia">Academia:</label>
                            <select name="academia" id="academia">
                                <option value="">-- Todas las Academias --</option>
                                <?php 
                                if (isset($academias) && $academias->num_rows > 0) {
                                    $academias->data_seek(0); // Aseguramos que el puntero esté al inicio
                                    while($academia = $academias->fetch_assoc()) {
                                        $selected = (isset($filtro_academia_id) && $filtro_academia_id == $academia['id_academia']) ? 'selected' : '';
                                        echo "<option value='{$academia['id_academia']}' {$selected}>" . htmlspecialchars($academia['nombre']) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="termino">Buscar profesor:</label>
                            <input type="text" name="termino" id="termino" placeholder="Nombre o apellido..." value="<?php echo htmlspecialchars($filtro_termino ?? ''); ?>">
                        </div>
                    </div>

                    <div class="button-group">
                        <input type="submit" value="Filtrar Profesores" class="button-primary">
                        <a href="index.php?controlador=user&accion=consultarPorAcademia" class="button-secondary">
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
                            <th>Matrícula</th>
                            <th>Profesor</th>
                            <th>Rol</th>
                            <th>Academia (Vínculo)</th>
                            <th>Materias Asignadas</th> 
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
                                <td><?php echo htmlspecialchars($prof['matricula']); ?></td>
                                <td class="text-strong"><?php echo htmlspecialchars($prof['nombre'] . ' ' . $prof['apellido_pa'] . ' ' . $prof['apellido_ma']); ?></td>
                                <td>
                                    <!-- Badge para Rol -->
                                    <span class="badge" >
                                        <?php echo htmlspecialchars($prof['rol']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($prof['nombre_academia'] ?? '— Varios/N/A —'); ?></td>
                                
                                <!-- 🔑 NUEVO: Columna de Materias Asignadas -->
                                <td class="text-sm">
                                    <?php echo htmlspecialchars($prof['materias_impartidas'] ?? '— Ninguna —'); ?>
                                </td>
                            </tr>
                        <?php 
                            endwhile; 
                        else:
                        ?>
                            <tr>
                                <td colspan="5" class="text-center no-data">No se encontraron profesores con esos filtros o sin asignación de materia.</td>
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
            <a href="index.php?controlador=acceso&accion=panelPrincipal" class="button-secondary">
                &larr; Volver al Panel Principal
            </a>

        </div> <!-- fin .main-content -->
    </div> <!-- fin .main-container -->
</body>
</html>