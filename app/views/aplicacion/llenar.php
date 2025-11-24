<?php
// Extracción de datos para simplificar la vista
$aplicacion = $datos['aplicacion'];
$rubros = $datos['rubros'];
$evaluador = $datos['evaluador'] ?? []; 
$materias_lista = $datos['materias_lista'] ?? [];
$cuatrimestres_lista = $datos['cuatrimestres_lista'] ?? [];

$profesor_nombre_completo = htmlspecialchars($aplicacion['profesor_nombre'] . ' ' . $aplicacion['profesor_apellido'] ?? 'N/A');
$evaluador_nombre_completo = htmlspecialchars(($evaluador['nombre'] ?? 'Evaluador') . ' ' . ($evaluador['apellido_pa'] ?? ''));
$fecha_actual = date('Y-m-d'); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplicar Instrumento</title>
    <!-- Incluye tus estilos externos -->
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
</head>
<body>
    
    <div class="main-container">
        <!-- Contenido principal -->
        <div class="main-content">
            
            <header class="module-header">
                <h1>GUÍA DE OBSERVACIÓN DEL DESEMPEÑO DOCENTE</h1>
                <p>Aplicación para: <?php echo $profesor_nombre_completo; ?></p>
            </header>

            <form action="index.php?controlador=aplicacion&accion=guardarAplicacion" method="POST" class="form-card">
                <input type="hidden" name="id_aplicacion" value="<?php echo $aplicacion['id_aplicacion']; ?>">
                
                <!-- Campo oculto para el número de rubros (usado por JS) -->
                <input type="hidden" id="num_rubros" value="<?php echo count($rubros); ?>">

                <!-- 1. DATOS DE LA EVALUACIÓN (Metadatos) -->
                <h3>DATOS DE LA EVALUACIÓN</h3>
                <div class="form-grid" style="grid-template-columns: repeat(4, 1fr);">
                    
                    <div class="form-field">
                        <label>Docente a evaluar</label>
                        <input type="text" value="<?php echo $profesor_nombre_completo; ?>" readonly>
                    </div>
                    
                    <div class="form-field">
                        <label for="asignatura">Asignatura</label>
                        <select name="asignatura" id="asignatura" required>
                            <option value="">-- Seleccionar Asignatura --</option>
                            <?php if ($materias_lista->num_rows > 0): $materias_lista->data_seek(0); while($m = $materias_lista->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($m['nombre_materia']); ?>"><?php echo htmlspecialchars($m['nombre_materia']); ?></option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>
                    
                    <div class="form-field">
                        <label for="cuatrimestre">Cuatrimestre</label>
                        <select name="cuatrimestre" id="cuatrimestre" required>
                            <option value="">-- Seleccionar Cuatrimestre --</option>
                            <?php foreach ($cuatrimestres_lista as $c): ?>
                                <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-field">
                        <label for="fecha_evaluacion">Fecha</label>
                        <input type="date" name="fecha_evaluacion" id="fecha_evaluacion" value="<?php echo $fecha_actual; ?>" required>
                    </div>
                </div>
                
                <hr style="margin: 20px 0;">

                <!-- 2. INSTRUCCIONES -->
                <h3>INSTRUCCIONES</h3>
                <ol style="padding-left: 20px; font-size: 0.9em; color: #555;">
                    <li>Lea cuidadosamente cada aspecto a observar antes de iniciar la evaluación.</li>
                    <li>Observe el desarrollo de la clase completa considerando los criterios establecidos.</li>
                    <li>Asigne un puntaje objetivo por cada aspecto con base en la evidencia observada.</li>
                    <li>Utilice el campo de comentarios para registrar observaciones puntuales.</li>
                    <li>Si un aspecto no aplica, indique "N/A" y justifique brevemente.</li>
                    <li>Al finalizar, sume los puntajes y registre el total en el apartado correspondiente.</li>
                    <li>Comparta retroalimentación respetuosa y constructiva con el personal docente.</li>
                    <li>Recabe las firmas de conformidad del evaluador y del personal docente.</li>
                </ol>
                
                <hr style="margin: 20px 0;">

                <!-- 3. DETALLE DE RUBROS -->
                <h3>Gestión de la asignatura (Aspectos a Observar)</h3>
                <h5 style="margin-top: -15px; margin-bottom: 15px;">A cada aspecto a evaluar se le asignará el siguiente puntaje: 0 no cumple; 5 cumple parcialmente y 10 cumple satisfactoriamente</h5>
                
                <table class="data-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No.</th>
                            <th style="width: 45%;">Aspectos a observar</th>
                            <th style="width: 15%;">Puntaje (Máx 10)</th>
                            <th style="width: 35%;">Comentarios adicionales</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $contador = 1; 
                        foreach ($rubros as $rubro): 
                        ?>
                            <tr>
                                <td style="text-align: center;"><?php echo $contador++; ?></td>
                                <td><?php echo htmlspecialchars($rubro['texto_aspecto']); ?></td>

                                <td>
                                    <!-- 🔑 PUNTUACIÓN MÁXIMA CORREGIDA A 10 (con step 5) -->
                                    <input type="number" 
                                           class="puntaje-input"
                                           name="puntaje[<?php echo $rubro['id_rubro']; ?>]" 
                                           id="puntaje-<?php echo $rubro['id_rubro']; ?>"
                                           min="0" max="10" step="5" 
                                           required style="width: 80px; text-align: center;">
                                </td>
                                <td>
                                    <textarea name="comentarios[<?php echo $rubro['id_rubro']; ?>]" rows="2" style="width: 95%;"></textarea>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <hr style="margin: 20px 0;">

                <!-- 4. PUNTAJE Y RETROALIMENTACIÓN FINAL -->
                <h3>Puntaje y retroalimentación</h3>

                <div class="form-grid" style="grid-template-columns: 1fr 2fr;">
                    
                    <div class="form-field">
                        <label for="puntaje_total">Puntaje obtenido (suma)</label>
                        <!-- Campo donde JS calculará el total -->
                        <input type="number" name="puntaje_total" id="puntaje_total" value="0" readonly style="font-weight: bold; width: 100%; padding: 10px;">
                    </div>
                    
                    <div class="form-field full-width" style="grid-column: 1 / -1;">
                        <!-- 🔑 Regla dinámica -->
                        <div id="regla-retroalimentacion" style="padding: 10px; border: 1px dashed gray; font-weight: bold; margin-bottom: 15px;">
                            Calculando umbral...
                        </div>
                        
                        <label for="observaciones_generales">Retroalimentación al personal docente (Comentarios y observaciones clave)</label>
                        <textarea name="observaciones_generales" id="observaciones_generales" rows="4" style="width: 99%;"></textarea>
                    </div>
                </div>

                <hr style="margin: 20px 0;">

                <!-- 5. FIRMAS (Nombres) -->
                <h3>Firmas/Nombres</h3>
                
                <div class="form-grid" style="grid-template-columns: 1fr 1fr; margin-top: 20px;">
                    
                    <!-- Evaluador -->
                    <div style="text-align: center;">
                        <div style="font-weight: bold;"><?php echo $evaluador_nombre_completo; ?></div>
                        <hr style="border: none; border-top: 1px solid black; margin-top: 50px;">
                        <label style="display: block; margin-top: 5px; font-size: 0.9em;">Nombre del Coordinador(a)</label>
                    </div>
                    
                    <!-- Evaluado -->
                    <div style="text-align: center;">
                        <div style="font-weight: bold;"><?php echo $profesor_nombre_completo; ?></div>
                        <hr style="border: none; border-top: 1px solid black; margin-top: 50px;">
                        <label style="display: block; margin-top: 5px; font-size: 0.9em;">Nombre del personal docente (Evaluado)</label>
                    </div>
                </div>

                <div class="button-group" style="margin-top: 40px; justify-content: center;">
                    <button type="submit" name="guardar_aplicacion" class="button-primary" style="padding: 12px 30px;">Finalizar y Guardar Evaluación</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- SCRIPT PARA EL CÁLCULO DEL PUNTAJE TOTAL Y REGLA -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const totalInput = document.getElementById('puntaje_total');
            const puntajeInputs = document.querySelectorAll('.puntaje-input');
            const numRubros = <?php echo count($rubros); ?>;
            const retroContainer = document.getElementById('regla-retroalimentacion');
            const form = document.querySelector('form');
            
            // --- REGLAS DE NEGOCIO ---
            const MAX_PUNTAJE_POR_RUBRO = 10;
            const UMBRAL_PORCENTAJE = 0.70; // 70% de aprobación
            const MIN_RUBROS_REQUERIDOS = 3; 

            // Cálculo dinámico del umbral
            const PUNTAJE_MAX_TOTAL = numRubros * MAX_PUNTAJE_POR_RUBRO;
            const PUNTAJE_MINIMO_APROBACION = PUNTAJE_MAX_TOTAL * UMBRAL_PORCENTAJE; // 70% del total
            // -------------------------

            function calcularTotal() {
                let total = 0;
                let rubrosCompletados = 0;
                
                puntajeInputs.forEach(input => {
                    const valor = parseFloat(input.value);
                    if (!isNaN(valor) && input.value !== "") { // Valor válido
                        total += valor;
                        rubrosCompletados++;
                    }
                });
                
                totalInput.value = total.toFixed(2); 

                mostrarRegla(total, rubrosCompletados);
            }

            function mostrarRegla(totalObtenido, rubrosCompletados) {
                let mensaje = "";
                let colorFondo = '';
                let colorTexto = '';
                
                if (rubrosCompletados < MIN_RUBROS_REQUERIDOS) {
                    mensaje = `Faltan rubros: Debe completar al menos ${MIN_RUBROS_REQUERIDOS} rubros de ${numRubros} para validar la evaluación.`;
                    colorFondo = '#f8d7da';
                    colorTexto = '#721c24';
                } else if (totalObtenido >= PUNTAJE_MINIMO_APROBACION) {
                    mensaje = `🟢 Puntaje (${totalObtenido.toFixed(2)}): No se realiza plan de acción. (Umbral min: ${PUNTAJE_MINIMO_APROBACION.toFixed(2)})`;
                    colorFondo = '#d4edda';
                    colorTexto = '#155724';
                } else {
                    mensaje = `🟡 Puntaje (${totalObtenido.toFixed(2)}): El personal docente deberá realizar plan de acción. (Umbral min: ${PUNTAJE_MINIMO_APROBACION.toFixed(2)})`;
                    colorFondo = '#fff3cd';
                    colorTexto = '#856404';
                }
                
                retroContainer.style.backgroundColor = colorFondo;
                retroContainer.style.color = colorTexto;
                retroContainer.innerHTML = mensaje;
            }

            // 🔑 VALIDACIÓN FINAL DE ENVÍO
            form.addEventListener('submit', function(event) {
                let rubrosCompletados = 0;
                puntajeInputs.forEach(input => {
                    if (!isNaN(parseFloat(input.value)) && input.value !== "") {
                        rubrosCompletados++;
                    }
                });

                if (rubrosCompletados < MIN_RUBROS_REQUERIDOS) {
                    event.preventDefault(); // Detener el envío
                    alert(`ERROR: Debe completar al menos ${MIN_RUBROS_REQUERIDOS} rubros de ${numRubros} para finalizar la evaluación.`);
                }
            });

            puntajeInputs.forEach(input => {
                input.addEventListener('input', calcularTotal);
            });

            calcularTotal();
        });
    </script>
</body>
</html>