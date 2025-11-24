<?php
    $rol = $_SESSION['rol_usuario'] ?? 'Administrador';
    $nombre = $_SESSION['nombre_usuario'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios</title>
    <!-- Enlazamos los estilos del panel (para el layout) y del CRUD (para la tabla) -->
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
</head>
<body>

    <!-- Contenedor principal (Sidebar + Contenido) -->
    <div class="main-container">
        
        <!-- ===== MENÚ LATERAL (SIDEBAR) ===== -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <span class="logo">UPEMOR</span>
                <span class="logo-sub">Gestión de Usuarios</span>
            </div>
            <ul class="sidebar-menu">
                <!-- El enlace activo es Usuarios -->
                <li><a href="index.php?controlador=user&accion=consultarUsuarios" class="sidebar-link active">Usuarios</a></li>
            </ul>
        </nav>

        <!-- ===== CONTENIDO PRINCIPAL ===== -->
        <div class="main-content">
            
            <!-- Cabecera del módulo -->
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

            <!-- 1. Tarjeta de Consulta (Tabla) -->
            <div class="form-card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h2>Consulta de usuarios</h2>
                    <!-- Botón para ir a registrar nuevo (Estilo secundario o primario) -->
                    <a href="index.php?controlador=user&accion=insertarUsuario" class="button-primary" style="padding: 8px 15px; font-size: 0.9em;">
                        + Nuevo Usuario
                    </a>
                </div>

                <!-- TABLA DE RESULTADOS -->
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Matrícula</th>
                            <th>Rol</th>
                            <th>Grado</th>
                            <th style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($usuarios && $usuarios->num_rows > 0):
                            while($row = $usuarios->fetch_assoc()): 
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id_profesor']); ?></td>
                                
                                <!-- Combinamos nombres para que se vea más limpio como en el boceto -->
                                <td style="font-weight: 500; color: #333;">
                                    <?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido_pa'] . ' ' . $row['apellido_ma']); ?>
                                </td>
                                
                                <td><?php echo htmlspecialchars($row['matricula']); ?></td>
                                
                                <td>
                                    <!-- Badge simple para el rol -->
                                    <span style="background-color: #f3f4f6; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; color: #555;">
                                        <?php echo htmlspecialchars($row['rol']); ?>
                                    </span>
                                </td>
                                
                                <td><?php echo htmlspecialchars($row['grado_academico']); ?></td>
                                
                                <td style="text-align: center;">
                                    <!-- Botones de Acción con clases CSS -->
                                    
                                    <!-- Editar -->
                                    <a href="index.php?controlador=user&accion=actualizarUsuario&id=<?php echo $row['id_profesor']; ?>" 
                                       class="button-secondary" 
                                       style="padding: 5px 10px; font-size: 0.85em; margin-right: 5px; text-decoration: none;">
                                        Editar
                                    </a>

                                    <!-- Eliminar (Solo Admin) -->
                                    <?php if (isset($_SESSION['rol_usuario']) && $_SESSION['rol_usuario'] == 'Administrador'): ?>
                                        <a href="index.php?controlador=user&accion=eliminarUsuario&id=<?php echo $row['id_profesor']; ?>" 
                                           class="button-secondary"
                                           style="padding: 5px 10px; font-size: 0.85em; color: #d9534f; background-color: #fdf2f2; border-color: #f5c6cb;"
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
                                <td colspan="6" style="text-align: center; padding: 20px; color: #777;">
                                    No se encontraron usuarios registrados.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Botón para volver (opcional, ya que tienes el sidebar) -->
            <br>
            <a href="index.php?controlador=acceso&accion=panelPrincipal" style="text-decoration: none; color: #666; font-size: 0.9em;">
                &larr; Volver al Panel Principal
            </a>

        </div> <!-- fin .main-content -->
    </div> <!-- fin .main-container -->
    
</body>
</html>