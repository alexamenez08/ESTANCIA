<?php
// Obtener datos de sesión para el header (si no están definidos en el controlador)
$rol = $_SESSION['rol_usuario'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Academia</title>
    <!-- Enlazamos los estilos -->
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
</head>
<body>
      

        <!-- ===== CONTENIDO PRINCIPAL ===== -->
        <div class="main-content">
            
            <!-- Cabecera -->
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Gestión de Academias</h1>
                    <p>Editando: <strong><?php echo htmlspecialchars($row['nombre']); ?></strong></p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol); ?></span>
                </div>
            </header>

            <!-- Tarjeta del Formulario -->
            <form action="index.php?controlador=academia&accion=editarAcademia" method="POST" class="form-card">
                
                <h2>Actualizar Academia</h2>
                <p class="form-subtitle">Modifique los campos necesarios y guarde los cambios.</p>

                <!-- Campo oculto ID -->
                <input type="hidden" name="id" value="<?php echo $row['id_academia']; ?>">

                <!-- Rejilla -->
                <div class="form-grid">
                    
                    <div class="form-field">
                        <label for="nombre">Nombre de academia:</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($row['nombre']); ?>" required>
                    </div>

                    <div class="form-field">
                        <label for="siglas">Siglas (Clave):</label>
                        <input type="text" id="siglas" name="siglas" value="<?php echo htmlspecialchars($row['siglas']); ?>" required>
                    </div>

                </div> <!-- Fin form-grid -->

                <!-- Botones -->
                <div class="button-group">
                    <button type="submit" name="guardar_academia" value="Guardar Cambios" class="button-primary">
                        Guardar Cambios
                    </button>

                    <a href="index.php?controlador=academia&accion=consultarAcademias" class="button-secondary">
                        Cancelar y Volver
                    </a>
                </div>

            </form>

        </div> <!-- Fin main-content -->
    </div> <!-- Fin main-container -->
    
</body>
</html>