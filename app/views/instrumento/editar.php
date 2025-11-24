<?php
    $rol = $_SESSION['rol_usuario'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
    <title>Editar Instrumento</title>
</head>
<body>

    <div class="main-container">

        <div class="main-content">
            
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Gestión de Instrumentos</h1>
                    <p>Editando Instrumento (ID: <?php echo $id_instrumento; ?>)</p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol); ?></span>
                </div>
            </header>

            <!-- Tarjeta del Formulario -->
            <form action="index.php?controlador=instrumento&accion=guardarRubros" method="POST" class="form-card" id="form-rubros">
                
                <input type="hidden" name="id_instrumento" value="<?php echo $id_instrumento; ?>">

                <h2>Aspectos a Observar (Rubros)</h2>
                <p class="form-subtitle">El instrumento debe tener un **mínimo de 3 rubros** para ser válido.</p>
                
                <div id="rubros_existentes">
                    <?php 
                    $contador_orden = 1; 
                    foreach ($rubros as $key => $rubro): 
                    ?>
                        <div class="rubro-fila">
                            <span class="rubro-order-display"><?php echo $contador_orden++; ?>.</span>
                            
                            <input type="hidden" name="rubro_id[]" value="<?php echo $rubro['id_rubro']; ?>">
                            
                            <label>Texto del Rubro:</label>
                            <textarea name="texto_aspecto[]" rows="2"><?php echo htmlspecialchars($rubro['texto_aspecto']); ?></textarea>
                            
                            <a href="index.php?controlador=instrumento&accion=eliminarRubro&id_rubro=<?php echo $rubro['id_rubro']; ?>&id_instrumento=<?php echo $id_instrumento; ?>" 
                               onclick="return confirm('¿Seguro?');" 
                               class="btn-row-action btn-delete">
                                Eliminar
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                
                
                <div>
                    <h3>Agregar Nuevos Rubros</h3>
                    <button type="button" onclick="agregarNuevoRubro()" class="button-secondary">➕ Agregar Nuevo Aspecto</button>
                </div>

                <div id="nuevos_rubros_container">
                </div>

                <div class="button-group">
                    <input type="submit" name="guardar_cambios" value="Guardar Cambios" class="button-primary">
                    <a href="index.php?controlador=instrumento&accion=consultarInstrumentos" class="button-secondary">Cancelar / Volver</a>
                </div>
            </form>
            
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('form-rubros');
            const containerExistente = document.getElementById('rubros_existentes');
            const containerNuevo = document.getElementById('nuevos_rubros_container');
            const MINIMO_REQUERIDO = 3;

            // --- Lógica de Numeración y Re-Indexación ---
            function getNextOrderNumber() {
                const totalChildren = containerExistente.children.length + containerNuevo.children.length;
                return totalChildren + 1;
            }

            function reindexRubros() {
                let contador = 1;
                
                // 1. Reindexar existentes (display-only)
                Array.from(containerExistente.children).forEach(div => {
                    const display = div.querySelector('.rubro-order-display');
                    if (display) display.textContent = `${contador++}.`;
                });
                
                // 2. Reindexar nuevos (display y hidden input)
                Array.from(containerNuevo.children).forEach(div => {
                    const display = div.querySelector('.rubro-order-display');
                    const hiddenInput = div.querySelector('input[name^="nuevo_orden"]');
                    if (display) display.textContent = `${contador}.`;
                    if (hiddenInput) hiddenInput.value = contador;
                    contador++;
                });
            }

            window.agregarNuevoRubro = function() {
                const orden = getNextOrderNumber();
                const nuevaFila = document.createElement('div');
                nuevaFila.classList.add('rubro-fila');
                
                // Eliminamos el style del textarea y el botón
                nuevaFila.innerHTML = `
                    <span class="rubro-order-display">${orden}.</span>
                    <input type="hidden" name="nuevo_orden[]" value="${orden}"> 
                    <label>Texto del Aspecto:</label>
                    <textarea name="nuevo_texto[]" rows="2" placeholder="Escriba el nuevo aspecto a observar..."></textarea>
                    <button type="button" onclick="this.parentElement.remove(); reindexRubros();" class="btn-row-action btn-delete">Quitar</button>
                `;
                containerNuevo.appendChild(nuevaFila);
                reindexRubros();
            }
            
            // --- VALIDACIÓN DE MÍNIMO DE RUBROS (En el submit) ---
            form.addEventListener('submit', function(event) {
                // Contar todos los rubros: existentes + nuevos que no estén vacíos
                const totalRubrosExistentes = containerExistente.children.length;
                
                let totalRubrosValidos = totalRubrosExistentes;
                
                // Contar los nuevos rubros que tienen texto
                Array.from(containerNuevo.children).forEach(div => {
                    const textarea = div.querySelector('textarea[name^="nuevo_texto"]');
                    if (textarea && textarea.value.trim() !== '') {
                        totalRubrosValidos++;
                    }
                });

                if (totalRubrosValidos < MINIMO_REQUERIDO) {
                    event.preventDefault(); // Detener el envío
                    alert(`ERROR: Un instrumento debe tener un mínimo de ${MINIMO_REQUERIDO} rubros. Actualmente tiene ${totalRubrosValidos}.`);
                }
            });

            // Lógica de re-indexación
            reindexRubros();
        });
    </script>
</div>
</body>
</html>