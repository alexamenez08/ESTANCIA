<?php
    // Obtener el rol para controlar permisos visuales (si aplica)
    $rol_actual = $_SESSION['rol_usuario'] ?? 'Usuario'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Academias</title>
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
                <li><a href="index.php?controlador=academia&accion=consultarAcademias" class="sidebar-link active">Academias</a></li>
            </ul>
        </nav>

        <div class="main-content">
            
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Gestión de Academias</h1>
                    <p>Administración de áreas académicas.</p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol_actual); ?></span>
                    <a href="index.php?controlador=acceso&accion=cerrarSesion" class="logout-link">Cerrar Sesión</a>
                </div>
            </header>

            <div class="form-card">
                
                <div class="button-group-header">
                    <h2>Listado de Academias Registradas</h2>
                    <a href="index.php?controlador=academia&accion=insertarAcademia" class="button-primary btn-table-action">
                        + Registrar Academia
                    </a>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 10%;">ID</th>
                            <th style="width: 50%;">Nombre</th>
                            <th style="width: 20%;">Siglas</th>
                            <th class="text-center" style="width: 20%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($academias) && $academias->num_rows > 0) {
                            while($row = $academias->fetch_assoc()){
                        ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id_academia']); ?></td>
                                    <td class="academy-name"><?php echo htmlspecialchars($row['nombre']); ?></td>
                                    <td>
                                        <span class="badge badge-success badge-siglas">
                                            <?php echo htmlspecialchars($row['siglas']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        
                                        <a href="index.php?controlador=academia&accion=editarAcademia&id=<?php echo $row['id_academia'];?>" 
                                           class="btn-edit btn-small-link">
                                            Editar
                                        </a>
                                        
                                        <a href="index.php?controlador=academia&accion=eliminarAcademia&id=<?php echo $row['id_academia'];?>" 
                                           class="button-secondary btn-delete"
                                           onclick="return confirm('¿Estás seguro de que deseas eliminar la academia: <?php echo htmlspecialchars($row['nombre']); ?>?');">
                                            Eliminar
                                        </a>

                                    </td>
                                </tr>

                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="4" class="text-center no-data no-data-cell">
                                    No hay academias registradas en el sistema.
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>

            </div>
            
            <br>
            <a href="index.php?controlador=acceso&accion=panelPrincipal" class="back-to-panel">
                &larr; Volver al Panel Principal
            </a>

        </div> 
    </div> 
</body>
</html>