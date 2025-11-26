<?php
    $rol = $_SESSION['rol_usuario'] ?? 'Administrador';
    $nombre = $_SESSION['nombre_usuario'] ?? 'Usuario';

    // Definimos las clases de los botones de acción para simplificar la vista
    $clase_editar = 'btn-edit btn-small-link'; 
    $clase_eliminar = 'btn-delete btn-small-link btn-delete-alert'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios</title>
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
</head>
<body>

    <div class="main-container">
        
        <nav class="sidebar">
            <div class="sidebar-header">
                <span class="logo">UPEMOR</span>
                <span class="logo-sub">Gestión de Usuarios</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php?controlador=user&accion=consultarUsuarios" class="sidebar-link active">Usuarios</a></li>
            </ul>
        </nav>

        <div class="main-content">
            
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Gestión de Usuarios</h1>
                    <p>Consulta y administración de cuentas de usuario.</p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol); ?></span>
                    <a href="index.php?controlador=acceso&accion=cerrarSesion" class="logout-link">Cerrar Sesión</a>
                </div>
            </header>

            <div class="form-card">
                <div class="button-group-header">
                    <h2>Consulta de usuarios</h2>
                    <a href="index.php?controlador=user&accion=insertarUsuario" class="button-primary btn-new-user">
                        + Nuevo Usuario
                    </a>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Matrícula</th>
                            <th>Rol</th>
                            <th>Grado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($usuarios && $usuarios->num_rows > 0):
                            while($row = $usuarios->fetch_assoc()): 
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id_profesor']); ?></td>
                                
                                <td class="user-name-cell">
                                    <?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido_pa'] . ' ' . $row['apellido_ma']); ?>
                                </td>
                                
                                <td><?php echo htmlspecialchars($row['matricula']); ?></td>
                                
                                <td>
                                    <span class="role-badge">
                                        <?php echo htmlspecialchars($row['rol']); ?>
                                    </span>
                                </td>
                                
                                <td><?php echo htmlspecialchars($row['grado_academico']); ?></td>
                                
                                <td class="text-center">
                                    
                                    <a href="index.php?controlador=user&accion=actualizarUsuario&id=<?php echo $row['id_profesor']; ?>" 
                                        class="<?php echo $clase_editar; ?>">
                                        Editar
                                    </a>

                                    <?php if (isset($_SESSION['rol_usuario']) && $_SESSION['rol_usuario'] == 'Administrador'): ?>
                                        <a href="index.php?controlador=user&accion=eliminarUsuario&id=<?php echo $row['id_profesor']; ?>" 
                                            class="<?php echo $clase_eliminar; ?>"
                                            onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">
                                            Eliminar
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php 
                            endwhile;
                        else: 
                        ?>
                            <tr>
                                <td colspan="6" class="no-data-user-cell">
                                    No se encontraron usuarios registrados.
                                </td>
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