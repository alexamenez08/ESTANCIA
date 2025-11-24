<?php
    // Obtener datos de sesión para el header
    $rol = $_SESSION['rol_usuario'] ?? 'Usuario';
    $nombre = $_SESSION['nombre_usuario'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Academia</title>
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
                    <p>Administración de las áreas académicas.</p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol); ?></span>
                    <a href="index.php?controlador=acceso&accion=cerrarSesion" class="logout-link">Cerrar Sesión</a>
                </div>
            </header>

            <form action="index.php?controlador=academia&accion=insertarAcademia" method="POST" class="form-card">
                <h2>Registrar Nueva Academia</h2>
                <p class="form-subtitle">Ingrese los datos de la nueva academia.</p>

                <div class="form-grid">
                    
                    <div class="form-field">
                        <label for="nombre">Nombre de la Academia: *</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Ej. Ingeniería en Software" required>
                    </div>
                    
                    <div class="form-field">
                        <label for="siglas">Siglas (Clave): *</label>
                        <input type="text" id="siglas" name="siglas" placeholder="Ej. ISW" required>
                    </div>

                </div> <div class="button-group">
                    <input type="submit" name="registrar_academia" value="Registrar Academia" class="button-primary">
                    
                    <a href="index.php?controlador=academia&accion=consultarAcademias" class="button-secondary">
                        Cancelar / Ver Lista
                    </a>
                </div>
            </form>

        </div>
    </div>
    
</body>
</html>