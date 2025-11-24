<?php
// Obtener datos de sesión
$rol = $_SESSION['rol_usuario'] ?? 'Usuario';
$nombre_materia_actual = htmlspecialchars($row['nombre_materia'] ?? 'N/A');
$id_academia_actual = $row['id_academia'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Materia</title>
    <!-- Enlazamos los estilos -->
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
</head>
<body>

    <div class="main-container">

        <!-- ===== CONTENIDO PRINCIPAL ===== -->
        <div class="main-content">
            
            <!-- Cabecera -->
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Gestión de Materias</h1>
                    <p>Editando materia: <strong><?php echo $nombre_materia_actual; ?></strong></p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol); ?></span>
            </header>

            <!-- Mensaje de Error (si existe) -->
            <?php if(!empty($error)): ?>
                <div class="form-alert error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Tarjeta del Formulario -->
            <form action="index.php?controlador=materia&accion=editar" method="POST" class="form-card">
                
                <h2>Actualizar Datos de Materia</h2>
                <p class="form-subtitle">Modifique los campos y sincronice los docentes.</p>

                <!-- Campo oculto ID de la Materia -->
                <input type="hidden" name="id_materia" value="<?php echo $row['id_materia']; ?>">

                <!-- Rejilla Principal -->
                <div class="form-grid">
                    
                    <div class="form-field">
                        <label for="nombre_materia">Nombre de la materia: *</label>
                        <input type="text" id="nombre_materia" name="nombre_materia" value="<?php echo htmlspecialchars($row['nombre_materia']); ?>" required>
                    </div>

                    <div class="form-field">
                        <label for="clave">Clave: *</label>
                        <input type="text" id="clave" name="clave" value="<?php echo htmlspecialchars($row['clave']); ?>" required>
                    </div>

                    <div class="form-field">
                        <label for="creditos">Créditos: *</label>
                        <input type="number" id="creditos" name="creditos" min="1" value="<?php echo (int)$row['creditos']; ?>" required>
                    </div>

                    <div class="form-field">
                        <label for="id_academia">Academia:</label>
                        <select id="id_academia" name="id_academia">
                            <option value="">-- Sin academia --</option>
                            <?php 
                            if ($academias && $academias->num_rows > 0) {
                                $academias->data_seek(0);
                                while($a = $academias->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $a['id_academia']; ?>"
                                    <?php echo ($id_academia_actual == $a['id_academia']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($a['nombre'].' ('.$a['siglas'].')'); ?>
                                </option>
                            <?php 
                                endwhile;
                            }
                            ?>
                        </select>
                    </div>

                    <!-- SELECCIÓN DE DOCENTES MEJORADA (Full Width) -->
                    <div class="form-field full-width">
                        <label>Docentes que imparten esta materia (Marque los asignados):</label>
                        
                        <div class="docentes-container">
                            <!-- Buscador JS -->
                            <input type="text" id="buscador-docentes" placeholder="🔍 Buscar docente por nombre o apellido...">
                            
                            <div class="scrollable-grid" id="lista-docentes">
                                <?php 
                                if ($profesores && $profesores->num_rows > 0) {
                                    $profesores->data_seek(0);
                                    while($p = $profesores->fetch_assoc()): 
                                        $nombre_completo = htmlspecialchars($p['nombre'].' '.$p['apellido_pa'].' '.$p['apellido_ma']);
                                        $checked = in_array($p['id_profesor'], $profesoresDeEsta) ? 'checked' : ''; 
                                ?>
                                    <!-- Usamos la clase 'hidden' y 'data-name' para el filtro JS -->
                                    <label class="checkbox-label" data-name="<?php echo strtolower($nombre_completo); ?>">
                                        <input type="checkbox" name="profesores[]" value="<?php echo $p['id_profesor']; ?>" <?php echo $checked; ?>>
                                        <?php echo $nombre_completo; ?>
                                    </label>
                                <?php 
                                    endwhile; 
                                } else {
                                ?>
                                    <p class="form-subtitle">No hay profesores registrados.</p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                </div> <!-- fin form-grid -->

                <div class="button-group">
                    <button type="submit" name="guardar_cambios" value="1" class="button-primary">
                        Guardar Cambios
                    </button>
                    
                    <a href="index.php?controlador=materia&accion=consultarMaterias" class="button-secondary">
                        Cancelar y Volver
                    </a>
                </div>
            </form>

        </div> <!-- fin main-content -->
    </div> <!-- fin main-container -->

    <!-- SCRIPT PARA EL BUSCADOR DE DOCENTES (Sin cambios) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buscador = document.getElementById('buscador-docentes');
            const lista = document.getElementById('lista-docentes');
            const etiquetas = lista.querySelectorAll('.checkbox-label');

            function normalizarTexto(texto) {
                // Función para eliminar acentos y convertir a minúsculas
                return texto.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
            }

            buscador.addEventListener('input', function() {
                const textoBuscado = normalizarTexto(this.value);

                etiquetas.forEach(label => {
                    const nombreProfesor = normalizarTexto(label.getAttribute('data-name'));
                    
                    if (nombreProfesor.includes(textoBuscado)) {
                        label.style.display = ""; // Mostrar
                    } else {
                        label.style.display = "none"; // Ocultar
                    }
                });
            });
        });
    </script>
</body>
</html>