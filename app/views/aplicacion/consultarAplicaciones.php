<?php
// Obtener el rol actual para las restricciones de seguridad
$rol_actual = $_SESSION['rol_usuario'] ?? 'Usuario'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Evaluaciones</title>
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
                <span class="logo-sub">Evaluación y Seguimiento</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php?controlador=aplicacion&accion=consultarAplicaciones" class="sidebar-link active">Aplicar/Consultar Evaluaciones</a></li>
                <!-- ... otros enlaces ... -->
            </ul>
        </nav>

        <!-- ===== CONTENIDO ===== -->
        <div class="main-content">
            
            <!-- Cabecera del módulo -->
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Evaluación y Seguimiento</h1>
                    <p>Consulta de Tareas Pendientes y Evaluaciones Finalizadas.</p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol_actual); ?></span>
                </div>
            </header>

            <!-- Tarjeta de Consulta (Tabla) -->
            <div class="form-card">
                
                <div class="button-group-header">
                    <h2>Consulta de Evaluaciones de Desempeño Docente</h2>
                    <!-- Botón para ir a crear nueva asignación -->
                    <a href="index.php?controlador=aplicacion&accion=asignar" class="button-primary">
                        + Asignar Nueva Evaluación
                    </a>
                </div>

                <!-- Mensajes de Éxito/Error (Si vienes de la redirección) -->
                <?php 
                if (isset($_GET['exito_completado'])) { echo '<div class="form-alert success"> Evaluación finalizada y guardada correctamente.</div>'; }
                if (isset($_GET['exito_asignacion'])) { echo '<div class="form-alert success"> Tarea de evaluación asignada correctamente.</div>'; }
                ?>

                <?php if (empty($evaluaciones)): ?>
                    <p class="form-subtitle text-center">No se han encontrado evaluaciones de instrumentos realizadas o pendientes.</p>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Instrumento</th>
                                <th>Profesor Evaluado</th>
                                <th>Período</th>
                                <th>Puntaje Total</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($evaluaciones as $evaluacion): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($evaluacion['id_aplicacion']); ?></td>
                                    <td class="text-strong"><?php echo htmlspecialchars($evaluacion['instrumento_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($evaluacion['profesor_nombre'] . ' ' . $evaluacion['profesor_apellido']); ?></td>
                                    <td><?php echo htmlspecialchars($evaluacion['periodo_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($evaluacion['puntaje']); ?></td>
                                    
                                    <!-- Columna de Estado (Badge) -->
                                    <td>
                                        <span class="badge 
                                            <?php echo $evaluacion['estado'] == 'pendiente' ? 'badge-warning' : 'badge-success'; ?>">
                                            <?php echo htmlspecialchars(ucfirst($evaluacion['estado'])); ?>
                                        </span>
                                    </td>
                                    
                                    <td class="text-center">
                                        <?php if ($evaluacion['estado'] == 'pendiente'): ?>
                                            <!-- Botón Aplicar (Acción principal) -->
                                            <a href="index.php?controlador=aplicacion&accion=aplicar&id_app=<?php echo $evaluacion['id_aplicacion']; ?>"
                                               class="button-primary btn-table-action btn-pending">
                                                Aplicar Evaluación
                                            </a>
                                        <?php else: ?>
                                            <!-- Botón Ver Detalle (Acción de solo lectura) -->
                                            <a href="index.php?controlador=aplicacion&accion=verDetalle&id_app=<?php echo $evaluacion['id_aplicacion']; ?>"
                                               class="button-secondary btn-table-action">
                                                Ver Detalle
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            
            <br>
            <a href="index.php?controlador=acceso&accion=panelPrincipal" class="button-secondary">
                &larr; Volver al Panel Principal
            </a>

        </div> <!-- fin main-content -->
    </div> <!-- fin main-container -->
</body>
</html>