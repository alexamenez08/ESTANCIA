<?php
    // Obtener el rol para las restricciones de seguridad (Eliminar)
    $rol_actual = $_SESSION['rol_usuario'] ?? 'Profesor'; 
    // Definimos las clases de los botones de acción
    $clase_editar = 'btn-edit btn-small-link'; // Usamos el estilo púrpura/azul de edición
    $clase_eliminar = 'btn-delete btn-small-link btn-delete-alert'; // Usamos el estilo de alerta rojo/rosa
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Materias</title>
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
                <li><a href="index.php?controlador=materia&accion=consultarMaterias" class="sidebar-link active">Materias</a></li>
            </ul>
        </nav>

        <div class="main-content">
            
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Gestión de Materias</h1>
                    <p>Consulta y administración de asignaturas.</p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol_actual); ?></span>
                    <a href="index.php?controlador=acceso&accion=cerrarSesion" class="logout-link">Cerrar Sesión</a>
                </div>
            </header>

            <div class="form-card">
                
                <div class="button-group-header">
                    <h2>Listado de Materias Registradas</h2>
                    <a href="index.php?controlador=materia&accion=insertarMateria" class="button-primary btn-table-action">
                        + Registrar Materia
                    </a>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Materia</th>
                            <th>Clave</th>
                            <th>Créditos</th>
                            <th>Academia</th>
                            <th>Docentes Asignados</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(isset($materias) && $materias->num_rows > 0): ?>
                        <?php while($r = $materias->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['id_materia']); ?></td>
                            <td class="materia-name"><?php echo htmlspecialchars($r['nombre_materia']); ?></td>
                            <td><?php echo htmlspecialchars($r['clave']); ?></td>
                            <td><?php echo (int)$r['creditos']; ?></td>
                            
                            <td>
                                <span class="academia-name">
                                    <?php echo htmlspecialchars($r['academia'] ?? '— Sin asignar —'); ?>
                                </span>
                            </td>

                            <td class="docentes-cell">
                                <div class="docentes-text-wrap">
                                    <?php echo htmlspecialchars($r['docentes'] ?? '— Ninguno —'); ?>
                                </div>
                            </td>

                            <td class="text-center">
                                
                                <a href="index.php?controlador=materia&accion=editar&id=<?php echo $r['id_materia']; ?>" 
                                   class="<?php echo $clase_editar; ?>">
                                    Editar
                                </a>

                                <?php if ($rol_actual == 'Administrador'): ?>
                                    <a href="index.php?controlador=materia&accion=eliminar&id=<?php echo $r['id_materia']; ?>"
                                       class="<?php echo $clase_eliminar; ?>"
                                       onclick="return confirm('¿Eliminar la materia <?php echo htmlspecialchars($r['nombre_materia']); ?>?');">
                                        Eliminar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="no-data-cell-padding">No hay materias registradas.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <br>
            <a href="index.php?controlador=acceso&accion=panelPrincipal" class="back-to-panel">
                &larr; Volver al Panel Principal
            </a>

        </div> </div> </body>
</html>