<?php
// Obtener datos de sesión para el header
$rol = $_SESSION['rol_usuario'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Periodo</title>
    <!-- Enlazamos los estilos del panel y del CRUD -->
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
</head>
<body>

    <div class="main-container">
        
        <!-- ===== SIDEBAR ===== -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <span class="logo">UPEMOR</span>
                <span class="logo-sub">Gestión Académica</span>
            </div>
            <ul class="sidebar-menu">
                <!-- ... otros enlaces ... -->
                <!-- Marcamos Periodos como activo -->
                <li><a href="index.php?controlador=periodo&accion=consultarPeriodos" class="sidebar-link active">Periodos</a></li>
                <!-- ... otros enlaces ... -->
            </ul>
        </nav>

        <!-- ===== CONTENIDO ===== -->
        <div class="main-content">
            
            <!-- Cabecera -->
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Gestión de Periodos</h1>
                    <p>Registro y administración de los ciclos académicos.</p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol); ?></span>
                    <a href="index.php?controlador=acceso&accion=cerrarSesion" class="logout-link">Cerrar Sesión</a>
                </div>
            </header>

            <!-- Tarjeta del Formulario -->
            <form action="index.php?controlador=periodo&accion=insertarPeriodo" method="POST" class="form-card">
                <h2>Registrar Nuevo Período</h2>
                <p class="form-subtitle">Ingrese el nombre y las fechas de inicio y fin del ciclo.</p>

                <!-- Mensajes de Éxito/Error -->
                <?php 
                if (isset($exito_mensaje)) { echo '<div class="form-alert success">'.htmlspecialchars($exito_mensaje).'</div>'; }
                if (isset($error_mensaje)) { echo '<div class="form-alert error">'.htmlspecialchars($error_mensaje).'</div>'; }
                ?>

                <!-- Rejilla del formulario -->
                <div class="form-grid" style="grid-template-columns: 1fr 1fr 1fr;"> 
                    
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

                </div> <!-- fin form-grid -->
                
                <div class="button-group">
                    <input type="submit" name="enviar_periodo" value="Registrar Período" class="button-primary">
                    
                    <a href="index.php?controlador=periodo&accion=consultarPeriodos" class="button-secondary">
                        Ver Periodos
                    </a>
                </div>
            </form>

        </div> <!-- fin main-content -->
    </div> <!-- fin main-container -->
    
</body>
</html>