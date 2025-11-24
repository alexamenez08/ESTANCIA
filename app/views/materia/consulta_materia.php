<?php
// Obtener el rol para las restricciones de seguridad (Eliminar)
$rol_actual = $_SESSION['rol_usuario'] ?? 'Profesor'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Materias</title>
    <!-- Enlazamos los estilos del panel y del CRUD -->
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
</head>
<body>

    <!-- Contenedor principal (Sidebar + Contenido) -->
    <div class="main-container">
        
        <!-- ===== SIDEBAR ===== -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <span class="logo">UPEMOR</span>
                <span class="logo-sub">Gestión Académica</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php?controlador=materia&accion=consultarMaterias" class="sidebar-link active">Materias</a></li>
            </ul>
        </nav>

        <!-- ===== CONTENIDO PRINCIPAL ===== -->
        <div class="main-content">
            
            <!-- Cabecera del módulo -->
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

            <!-- Tarjeta de Consulta (Tabla) -->
            <div class="form-card">
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>Listado de Materias Registradas</h2>
                    <!-- Botón para ir a registrar nueva -->
                    <a href="index.php?controlador=materia&accion=insertarMateria" class="button-primary" style="padding: 10px 20px; font-size: 0.95em;">
                        + Registrar Materia
                    </a>
                </div>

                <!-- TABLA ESTILIZADA -->
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Materia</th>
                            <th>Clave</th>
                            <th>Créditos</th>
                            <th>Academia</th>
                            <th>Docentes Asignados</th>
                            <th style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(isset($materias) && $materias->num_rows > 0): ?>
                        <?php while($r = $materias->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['id_materia']); ?></td>
                            <td style="font-weight: 600; color: var(--primary-color);"><?php echo htmlspecialchars($r['nombre_materia']); ?></td>
                            <td><?php echo htmlspecialchars($r['clave']); ?></td>
                            <td><?php echo (int)$r['creditos']; ?></td>
                            
                            <!-- Academia -->
                            <td>
                                <span style="font-weight: 500;">
                                    <?php echo htmlspecialchars($r['academia'] ?? '— Sin asignar —'); ?>
                                </span>
                            </td>

                            <!-- Docentes (Usamos un div para que envuelva el texto largo si existe) -->
                            <td style="font-size: 0.9em; max-width: 250px;">
                                <div style="white-space: pre-wrap; word-wrap: break-word;">
                                    <?php echo htmlspecialchars($r['docentes'] ?? '— Ninguno —'); ?>
                                </div>
                            </td>

                            <td style="text-align: center;">
                                
                                <!-- 🔑 BOTÓN EDITAR CORREGIDO -->
                                <a href="index.php?controlador=materia&accion=editar&id=<?php echo $r['id_materia']; ?>" 
                                   class="button-secondary"
                                   style="padding: 5px 10px; font-size: 0.85em; margin-right: 5px; text-decoration: none;">
                                    Editar
                                </a>

                                <!-- 🔑 BOTÓN ELIMINAR CORREGIDO (Restricción opcional si el rol no es Admin) -->
                                <?php if ($rol_actual == 'Administrador'): ?>
                                    <a href="index.php?controlador=materia&accion=eliminar&id=<?php echo $r['id_materia']; ?>"
                                       class="button-secondary"
                                       style="padding: 5px 10px; font-size: 0.85em; color: #d9534f; background-color: #fdf2f2; border-color: #f5c6cb;"
                                       onclick="return confirm('¿Eliminar la materia <?php echo htmlspecialchars($r['nombre_materia']); ?>?');">
                                        Eliminar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center; padding: 30px; color: #777;">No hay materias registradas.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <br>
            <a href="index.php?controlador=acceso&accion=panelPrincipal" style="text-decoration: none; color: #666; font-size: 0.9em;">
                &larr; Volver al Panel Principal
            </a>

        </div> <!-- fin .main-content -->
    </div> <!-- fin .main-container -->
</body>
</html>