<?php
// Obtener el rol actual para las restricciones de seguridad (si aplica)
$rol_actual = $_SESSION['rol_usuario'] ?? 'Usuario';

// Definimos las clases de los botones de acción para simplificar la vista
$clase_editar = 'btn-edit btn-table-action-small'; // Usando la clase de edición (púrpura/azul)
$clase_eliminar = 'btn-delete btn-table-action-small btn-delete-periodo'; // Usando la clase de alerta (rojo/rosa)
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Períodos</title>
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
</head>

<body>

    <div class="main-container">

        <nav class="sidebar">
            <div class="sidebar-header">
                <span class="logo">UPEMOR</span>
                <span class="logo-sub">Gestión Académica</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php?controlador=periodo&accion=consultarPeriodos" class="sidebar-link active">Períodos</a></li>
            </ul>
        </nav>

        <div class="main-content">

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

            <div class="form-card">

                <div class="button-group-header">
                    <h2>Consulta de Períodos Académicos</h2>
                    <a href="index.php?controlador=periodo&accion=insertarPeriodo" class="button-primary btn-register-periodo">
                        + Registrar Nuevo Período
                    </a>
                </div>

                <?php
                if (isset($_GET['exito_update'])) {
                    echo '<p class="form-alert success">✅ Período actualizado correctamente.</p>';
                }
                if (isset($_GET['exito_delete'])) {
                    echo '<p class="form-alert success">✅ Período eliminado correctamente.</p>';
                }
                if (isset($_GET['error_delete'])) {
                    echo '<p class="form-alert error"> Error al eliminar el período. Puede que esté vinculado a una Evaluación o Materia.</p>';
                }
                ?>

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
                                <th class="text-center" style="width: 20%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($periodos as $periodo): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($periodo['id_periodo']); ?></td>
                                    <td class="periodo-name"><?php echo htmlspecialchars($periodo['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($periodo['fecha_inicio']); ?></td>
                                    <td><?php echo htmlspecialchars($periodo['fecha_fin']); ?></td>
                                    <td class="text-center">

                                        <a href="index.php?controlador=periodo&accion=editarPeriodo&id=<?php echo $periodo['id_periodo']; ?>"
                                            class="<?php echo $clase_editar; ?>">
                                            Editar
                                        </a>

                                        <a href="index.php?controlador=periodo&accion=eliminarPeriodo&id=<?php echo $periodo['id_periodo']; ?>"
                                            class="btn-delete btn-table-action-small btn-delete-periodo"
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
            <a href="index.php?controlador=acceso&accion=panelPrincipal" class="back-to-panel">
                &larr; Volver al Panel Principal
            </a>

        </div>
    </div>
</body>

</html>