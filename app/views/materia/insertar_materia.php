<?php
    // Obtener datos de sesión
    $rol = $_SESSION['rol_usuario'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Materia</title>
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
                <li><a href="index.php?controlador=materia&accion=consultarMaterias" class="sidebar-link active">Materias</a></li>
            </ul>
        </nav>

        <div class="main-content">
            
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Gestión de Materias</h1>
                    <p>Registro de asignaturas y asignación docente.</p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol); ?></span>
                    <a href="index.php?controlador=acceso&accion=cerrarSesion" class="logout-link">Cerrar Sesión</a>
                </div>
            </header>

            <?php if(!empty($error)): ?>
                <div class="form-alert error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="index.php?controlador=materia&accion=insertarMateria" method="POST" class="form-card">
                <h2>Registrar Nueva Materia</h2>
                <p class="form-subtitle">Complete la información de la materia y seleccione a los docentes que la imparten.</p>

                <div class="form-grid">
                    
                    <div class="form-field">
                        <label for="nombre_materia">Nombre de la materia: *</label>
                        <input type="text" id="nombre_materia" name="nombre_materia" required>
                    </div>

                    <div class="form-field">
                        <label for="clave">Clave: *</label>
                        <input type="text" id="clave" name="clave" required>
                    </div>

                    <div class="form-field">
                        <label for="creditos">Créditos: *</label>
                        <input type="number" id="creditos" name="creditos" min="1" required>
                    </div>

                    <div class="form-field">
                        <label for="id_academia">Academia:</label>
                        <select id="id_academia" name="id_academia">
                            <option value="">-- Seleccione una academia --</option>
                            <?php while($a = $academias->fetch_assoc()): ?>
                                <option value="<?php echo $a['id_academia']; ?>">
                                    <?php echo htmlspecialchars($a['nombre'].' ('.$a['siglas'].')'); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-field full-width">
                        <label>Docentes que imparten esta materia (Seleccione uno o más):</label>
                        
                        <div class="docentes-container">
                            <input type="text" id="buscador-docentes" placeholder="🔍 Buscar docente por nombre o apellido...">
                            
                            <div class="scrollable-grid" id="lista-docentes">
                                <?php 
                                if ($profesores && $profesores->num_rows > 0):
                                    while($p = $profesores->fetch_assoc()): 
                                        $nombre_completo = htmlspecialchars($p['nombre'].' '.$p['apellido_pa'].' '.$p['apellido_ma']);
                                ?>
                                    <label class="checkbox-label" data-name="<?php echo strtolower($nombre_completo); ?>">
                                        <input type="checkbox" name="profesores[]" value="<?php echo $p['id_profesor']; ?>">
                                        <?php echo $nombre_completo; ?>
                                    </label>
                                <?php 
                                    endwhile; 
                                else:
                                ?>
                                    <p class="no-professors-message">No hay profesores registrados.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <p class="search-note-text">Use el buscador para filtrar la lista si hay muchos docentes.</p>
                    </div>

                </div> <div class="button-group">
                    <input type="submit" name="registrar_materia" value="Registrar Materia" class="button-primary">
                    
                    <a href="index.php?controlador=materia&accion=consultarMaterias" class="button-secondary">
                        Cancelar / Ver Lista
                    </a>
                </div>
            </form>

        </div> </div> <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buscador = document.getElementById('buscador-docentes');
            const lista = document.getElementById('lista-docentes');
            // Selecciona todas las etiquetas (labels) que tienen el atributo data-name
            const etiquetas = lista.querySelectorAll('.checkbox-label');

            // Función para quitar acentos y caracteres especiales
            function normalizarTexto(texto) {
                // Se asegura de que el texto sea una cadena antes de normalizar
                if (typeof texto !== 'string') return '';
                return texto.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
            }

            buscador.addEventListener('input', function() {
                // Normalizamos el texto buscado (ej: 'López' -> 'lopez')
                const textoBuscado = normalizarTexto(this.value);

                etiquetas.forEach(label => {
                    // Normalizamos el nombre del profesor guardado en data-name
                    const nombreProfesor = normalizarTexto(label.getAttribute('data-name'));
                    
                    // Si el nombre incluye el texto buscado, mostrar. Si no, ocultar.
                    if (nombreProfesor.includes(textoBuscado)) {
                        label.style.display = ""; // Mostrar (volver al default del CSS)
                    } else {
                        label.style.display = "none"; // Ocultar (usando estilo en línea, la única forma aquí)
                    }
                });
            });
        });
    </script>

</body>
</html>