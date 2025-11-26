<?php
// Obtener datos de sesión
$rol = $_SESSION['rol_usuario'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Instrumento</title>
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
                <li><a href="index.php?controlador=instrumento&accion=consultarInstrumentos" class="sidebar-link active">Instrumentos</a></li>
            </ul>
        </nav>

        <div class="main-content">
            
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Gestión de Instrumentos</h1>
                    <p>Creación de nuevos formularios de evaluación.</p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol); ?></span>
                    <a href="index.php?controlador=acceso&accion=cerrarSesion" class="logout-link">Cerrar Sesión</a>
                </div>
            </header>

            <form action="index.php?controlador=instrumento&accion=insertarInstrumento" method="POST" class="form-card">
                
                <h2>Creación de Instrumento</h2>
                <p class="form-subtitle">Ingrese el nombre y una breve descripción del nuevo instrumento.</p>

                <div class="form-grid one-column"> 
                    
                    <div class="form-field">
                        <label for="nombre">Nombre del Instrumento: *</label>
                        <input type="text" id="nombre" name="nombre" required placeholder="Ej. Guía de Observación del Desempeño">
                    </div>
                </div>

                <div class="form-field full-width">
                    <label for="descripcion">Descripción del Instrumento: *</label>
                    <textarea id="descripcion" name="descripcion" rows="4" required placeholder="Detalle el objetivo y el alcance del instrumento."></textarea>
                </div>
                
                <div class="button-group">
                    <input type="submit" name="enviar" value="Crear Instrumento" class="button-primary">
                    
                    <a href="index.php?controlador=instrumento&accion=consultarInstrumentos" class="button-secondary">
                        Volver a la Lista
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>