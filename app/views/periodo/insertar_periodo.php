<?php
// Obtener datos de sesión para el header
$rol = $_SESSION['rol_usuario'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Período</title>
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
                    <p>Registro y administración de los ciclos académicos.</p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol); ?></span>
                    <a href="index.php?controlador=acceso&accion=cerrarSesion" class="logout-link">Cerrar Sesión</a>
                </div>
            </header>

            <form action="index.php?controlador=periodo&accion=insertarPeriodo" method="POST" class="form-card">
                <h2>Registrar Nuevo Período</h2>
                <p class="form-subtitle">Ingrese el nombre y las fechas de inicio y fin del ciclo.</p>

                <?php 
                if (isset($exito_mensaje)) { echo '<div class="form-alert success">'.htmlspecialchars($exito_mensaje).'</div>'; }
                if (isset($error_mensaje)) { echo '<div class="form-alert error">'.htmlspecialchars($error_mensaje).'</div>'; }
                ?>

                <div class="form-grid three-columns"> 
                    
                    <div class="form-field full-width">
                        <label for="nombre">Nombre del Período: *</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Ej. Enero - Abril 2026" required>
                    </div>
                    
                    <div class="form-field">
                        <label for="fecha_inicio">Fecha de inicio: *</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" min="2025-09-03" max="2035-09-01" required>
                    </div>

                    <div class="form-field">
                        <label for="fecha_fin">Fecha de finalización: *</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" min="2025-09-04" max="2035-10-31" required>
                    </div>

                </div> <div class="button-group">
                    <input type="submit" name="enviar_periodo" value="Registrar Período" class="button-primary">
                    
                    <a href="index.php?controlador=periodo&accion=consultarPeriodos" class="button-secondary">
                        Ver Períodos
                    </a>
                </div>
            </form>

        </div> </div> </body>
</html>