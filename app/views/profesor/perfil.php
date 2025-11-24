<?php 
    // $profesor_data y $materias_asignadas vienen del controlador
    $profesor = $profesor_data;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="main-container">
        <div class="main-content">
            <header class="module-header">
                <h1>Mi Perfil: <?php echo htmlspecialchars($profesor['nombre'] . ' ' . $profesor['apellido_pa']); ?></h1>
            </header>

            <div class="form-card">
                <h2>Datos Personales (Solo Lectura)</h2>
                <table class="detalle-table">
                    <tr><th>Matrícula</th><td><?php echo htmlspecialchars($profesor['matricula']); ?></td></tr>
                    <tr><th>Grado Académico</th><td><?php echo htmlspecialchars($profesor['grado_academico']); ?></td></tr>
                    <tr><th>Rol en Sistema</th><td><?php echo htmlspecialchars($profesor['rol']); ?></td></tr>
                    <tr><th>Sexo</th><td><?php echo htmlspecialchars($profesor['sexo']); ?></td></tr>
                </table>
            </div>

            <div class="form-card">
                <h2>Materias que Imparto</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Clave</th>
                            <th>Academia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($materias_asignadas && $materias_asignadas->num_rows > 0): ?>
                            <?php while($materia = $materias_asignadas->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($materia['nombre_materia']); ?></td>
                                    <td><?php echo htmlspecialchars($materia['clave']); ?></td>
                                    <td><?php echo htmlspecialchars($materia['nombre_academia'] ?? 'N/A'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center no-data">No tienes materias asignadas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <a href="index.php?controlador=acceso&accion=panelPrincipal" class="button-secondary">&larr; Volver</a>
        </div>
    </div>
</body>
</html>