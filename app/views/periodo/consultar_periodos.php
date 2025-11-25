<?php
// Obtener el rol actual para las restricciones de seguridad (si aplica)
$rol_actual = $_SESSION['rol_usuario'] ?? 'Usuario'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Períodos</title>
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
                <span class="logo-sub">Gestión Académica</span>
            </div>
            <ul class="sidebar-menu">
                <!-- ... otros enlaces ... -->
                <li><a href="index.php?controlador=periodo&accion=consultarPeriodos" class="sidebar-link active">Períodos</a></li>
                <!-- ... otros enlaces ... -->
            </ul>
        </nav>

        <!-- ===== CONTENIDO ===== -->
        <div class="main-content">
            
            <!-- Cabecera -->
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Gestión de Períodos</h1>
                    <p>Consulta y administración de los ciclos académicos.</p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol_actual); ?></span>
                    <a href="index.php?controlador=acceso&accion=cerrarSesion" class="logout-link">Cerrar Sesión</a>
                </div>
            </header>

            <!-- Tarjeta de Consulta -->
            <div class="form-card">
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>Consulta de Períodos Académicos</h2>
                    <!-- Botón para ir a registrar nueva -->
                    <a href="index.php?controlador=periodo&accion=insertarPeriodo" class="button-primary" style="padding: 10px 20px; font-size: 0.95em;">
                        + Registrar Nuevo Período
                    </a>
                </div>

                <!-- Mensajes de Éxito/Error (Usando clases CSS definidas) -->
                <?php 
                if (isset($_GET['exito_update'])) { echo '<p class="form-alert success">✅ Período actualizado correctamente.</p>'; }
                if (isset($_GET['exito_delete'])) { echo '<p class="form-alert success">✅ Período eliminado correctamente.</p>'; }
                if (isset($_GET['error_delete'])) { echo '<p class="form-alert error">❌ Error al eliminar el período. Puede que esté vinculado a una Evaluación o Materia.</p>'; }
                ?>

                <!-- TABLA ESTILIZADA -->
                <?php if (empty($periodos)): ?>
                    <p class="form-subtitle">No hay períodos registrados.</p>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 30%;">Nombre</th>
                                <th style="width: 20%;">Inicio</th>
                                <th style="width: 20%;">Fin</th>
                                <th style="width: 20%; text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($periodos as $periodo): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($periodo['id_periodo']); ?></td>
                                    <td style="font-weight: 500; color: var(--primary-color);"><?php echo htmlspecialchars($periodo['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($periodo['fecha_inicio']); ?></td>
                                    <td><?php echo htmlspecialchars($periodo['fecha_fin']); ?></td>
                                    <td style="text-align: center;">
                                        
                                        <!-- Botón Editar -->
                                        <a href="index.php?controlador=periodo&accion=editarPeriodo&id=<?php echo $periodo['id_periodo']; ?>" 
                                           class="button-secondary"
                                           style="padding: 5px 10px; font-size: 0.85em; margin-right: 5px; text-decoration: none;">
                                            Editar
                                        </a>
                                        
                                        <!-- Botón Eliminar (Con confirmación) -->
                                        <a href="index.php?controlador=periodo&accion=eliminarPeriodo&id=<?php echo $periodo['id_periodo']; ?>" 
                                           class="button-secondary"
                                           style="padding: 5px 10px; font-size: 0.85em; color: var(--danger-text); background-color: var(--danger-color); border-color: var(--danger-color);"
                                           onclick="return confirm('¿Está seguro de eliminar el período <?php echo htmlspecialchars($periodo['nombre']); ?>? Esta acción es irreversible y podría causar errores si está vinculado a evaluaciones.');">
                                            Eliminar
                                        </a>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            
            <br>
            <a href="index.php?controlador=acceso&accion=panelPrincipal" style="text-decoration: none; color: #666; font-size: 0.9em;">
                &larr; Volver al Panel Principal
            </a>

        </div> <!-- fin main-content -->
    </div> <!-- fin main-container -->
    
    <!-- 
      El CSS para .success, .error, y .form-alert fue añadido al final de crud_style.css 
      en una conversación anterior, así que solo necesitamos asegurarnos de que esas clases existan allí.
    -->
</body>
</html>