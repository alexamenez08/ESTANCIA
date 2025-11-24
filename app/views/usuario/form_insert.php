<?php
    $rol = $_SESSION['rol_usuario'] ?? 'Administrador';
    $nombre = $_SESSION['nombre_usuario'] ?? 'Usuario';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>"> <!-- (Opcional: si quieres el fondo y layout) -->
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
    <title>Registrar Usuario</title>
</head>
<body>

    <!-- Contenedor principal (Sidebar + Contenido) -->
    <div class="main-container">
        
        <!-- ===== MENÚ LATERAL (SIDEBAR) FIJO ===== -->
        <!-- (Este es el mismo sidebar que usamos en panel_principal.php) -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <span class="logo">UPEMOR</span>
                <span class="logo-sub">Gestión de Usuarios</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php?controlador=user&accion=consultarUsuarios" class="sidebar-link active">Usuarios</a></li>
                <li><a href="index.php?controlador=materia&accion=consultarMaterias" class="sidebar-link">Materias</a></li>
                <li><a href="index.php?controlador=academia&accion=consultarAcademias" class="sidebar-link">Academias</a></li>
                <li><a href="index.php?controlador=instrumento&accion=consultarInstrumentos" class="sidebar-link">Instrumentos</a></li>
                <li><a href="index.php?controlador=periodo&accion=consultarPeriodos" class="sidebar-link">Periodos</a></li>
                <li><a href="index.php?controlador=user&accion=consultarPorAcademia" class="sidebar-link">Consultas y Reportes</a></li>
            </ul>
        </nav>

        <!-- ===== CONTENIDO PRINCIPAL ===== -->
        <div class="main-content">
            
            <!-- Cabecera del módulo -->
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Gestión de Usuarios</h1>
                    <p>Crear, consultar, editar y eliminar usuarios del sistema.</p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol); ?></span>
                    <a href="index.php?controlador=acceso&accion=cerrarSesion" class="logout-link">Cerrar Sesión</a>
                </div>
            </header>

            <!-- Tarjeta de Formulario de Registro -->
            <form action="index.php?controlador=user&accion=insertarUsuario" method="POST" class="form-card">
                <h2>Registro de usuario</h2>
                <p class="form-subtitle">Los campos marcados con * son obligatorios.</p>

                <div class="form-grid">
                    
                    <!-- Campo Matrícula -->
                    <div class="form-field">
                        <label for="matricula">Matrícula : *</label>
                        <input type="text" id="matricula" name="matricula" required>
                    </div>
                    
                    <!-- Campo Rol -->
                    <div class="form-field">
                        <label for="rol">Rol del Usuario: *</label>
                        <select id="rol" name="rol" required>
                            <option value="">-- Seleccione un Rol --</option>
                            <option value="Profesor">Profesor</option>
                            <option value="Administrador">Administrador</option>
                            <option value="Coordinador">Coordinador</option>
                        </select>
                    </div>

                    <!-- Campo Nombre -->
                    <div class="form-field">
                        <label for="nombre">Nombre(s): *</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    
                    <!-- Campo Apellido Paterno -->
                    <div class="form-field">
                        <label for="apellido_pa">Apellido Paterno: *</label>
                        <input type="text" id="apellido_pa" name="apellido_pa" required>
                    </div>

                    <!-- Campo Apellido Materno -->
                    <div class="form-field">
                        <label for="apellido_ma">Apellido Materno: </label>
                        <input type="text" id="apellido_ma" name="apellido_ma">
                    </div>

                    <!-- Campo Sexo -->
                    <div class="form-field">
                        <label for="sexo">Sexo: *</label>
                        <select id="sexo" name="sexo" required>
                            <option value="">-- Seleccione el Sexo --</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                            <option value="O">Otro</option>
                        </select>
                    </div>

                    <!-- Campo Grado Académico -->
                    <div class="form-field">
                        <label for="grado_academico">Grado Académico: *</label>
                        <input type="text" id="grado_academico" name="grado_academico" required>
                    </div>

                    <!-- Campo Contraseña -->
                    <div class="form-field">
                        <label for="pass">Contraseña: *</label>
                        <input type="password" id="pass" name="pass" required>
                    </div>

                </div> <!-- fin de .form-grid -->
                
                <div class="button-group">
                    <input type="submit" name="enviar" value="Registrar Usuario" class="button-primary">
                    <a href="index.php?controlador=user&accion=consultarUsuarios" class="button-secondary">
                        Ver Lista de Usuarios
                    </a>
                </div>
            </form>
            

        </div> <!-- fin de .main-content -->
    </div> <!-- fin de .main-container -->

    
    
</body>
</html>