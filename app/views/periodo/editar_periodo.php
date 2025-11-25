<?php
// Obtener datos de sesión para el header
$rol = $_SESSION['rol_usuario'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Período</title>
    <!-- Enlazamos los estilos -->
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
                <li><a href="index.php?controlador=periodo&accion=consultarPeriodos" class="sidebar-link active">Períodos</a></li>
                <!-- ... otros enlaces ... -->
            </ul>
        </nav>

        <!-- ===== CONTENIDO PRINCIPAL ===== -->
        <div class="main-content">
            
            <!-- Cabecera -->
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Gestión de Períodos</h1>
                    <p>Editando: <strong><?php echo htmlspecialchars($periodo_data['nombre']); ?></strong></p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol); ?></span>
                </div>
            </header>

            <!-- Tarjeta del Formulario -->
            <form action="index.php?controlador=periodo&accion=editarPeriodo&id=<?php echo $periodo_data['id_periodo']; ?>" method="POST" class="form-card">
                
                <h2>Actualizar Período Académico</h2>
                <p class="form-subtitle">Modifique el nombre o las fechas del ciclo.</p>

                <!-- Mensaje de Error (si existe) -->
                <?php 
                if (isset($error_mensaje)) { 
                    echo '<div class="form-alert error" style="margin-bottom: 20px;">' . htmlspecialchars($error_mensaje) . '</div>'; 
                }
                ?>

                <!-- Campo oculto ID -->
                <input type="hidden" name="id_periodo" value="<?php echo $periodo_data['id_periodo']; ?>">

                <!-- Rejilla con los campos -->
                <div class="form-grid" style="grid-template-columns: 1fr 1fr 1fr;"> 
                    
                    <div class="form-field full-width">
                        <label for="nombre">Nombre del Período: *</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($periodo_data['nombre']); ?>" required>
                    </div>
                    
                    <div class="form-field">
                        <label for="fecha_inicio">Fecha de inicio: *</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($periodo_data['fecha_inicio']); ?>" required>
                    </div>

                    <div class="form-field">
                        <label for="fecha_fin">Fecha de finalización: *</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($periodo_data['fecha_fin']); ?>" required>
                    </div>

                </div> <!-- Fin form-grid -->

                <!-- Botones -->
                <div class="button-group">
                    <input type="submit" name="actualizar_periodo" value="Actualizar Periodo" class="button-primary">
                    
                    <a href="index.php?controlador=periodo&accion=consultarPeriodos" class="button-secondary">
                        Cancelar y Volver
                    </a>
                </div>

            </form>

        </div> <!-- Fin main-content -->
    </div> <!-- Fin main-container -->
    
</body>
</html>