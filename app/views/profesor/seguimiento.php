<?php 
// $evaluaciones viene del controlador
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de Evaluaciones</title>
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="main-container">
        <div class="main-content">
            <header class="module-header">
                <h1>Mis Evaluaciones de Desempeño</h1>
            </header>

            <div class="form-card">
                <h2>Seguimiento de Tareas</h2>
                
                <?php if ($evaluaciones && $evaluaciones->num_rows > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID Aplicación</th>
                                <th>Instrumento</th>
                                <th>Período</th>
                                <th>Puntaje Final</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($eval = $evaluaciones->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($eval['id_aplicacion']); ?></td>
                                    <td><?php echo htmlspecialchars($eval['instrumento_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($eval['periodo_nombre'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($eval['puntaje'] ?? '—'); ?></td>
                                    <td>
                                        <span class="badge <?php echo $eval['estado'] == 'pendiente' ? 'badge-warning' : 'badge-success'; ?>">
                                            <?php echo htmlspecialchars(ucfirst($eval['estado'])); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($eval['estado'] == 'completado'): ?>
                                            <a href="index.php?controlador=aplicacion&accion=verDetalle&id_app=<?php echo $eval['id_aplicacion']; ?>"
                                               class="button-secondary btn-table-action">
                                                Ver Detalle
                                            </a>
                                        <?php else: ?>
                                            <span class="text-sm">Pendiente de aplicación</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center no-data">No tienes evaluaciones de desempeño registradas.</p>
                <?php endif; ?>
            </div>
            <a href="index.php?controlador=acceso&accion=panelPrincipal" class="button-secondary">&larr; Volver</a>
        </div>
    </div>
</body>
</html>