<?php
// Obtener el rol actual para la estructura de la página
$rol_actual = $_SESSION['rol_usuario'] ?? 'Usuario'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Evaluación</title>
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
</head>
<body>

    <div class="main-container">
        
        <!-- ===== SIDEBAR (Placeholder) ===== -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <span class="logo">UPEMOR</span>
                <span class="logo-sub">Evaluación y Seguimiento</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php?controlador=aplicacion&accion=consultarAplicaciones" class="sidebar-link active">Consultar Evaluaciones</a></li>
            </ul>
        </nav>

        <!-- ===== CONTENIDO ===== -->
        <div class="main-content">
            
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Evaluación de Desempeño</h1>
                    <p>Detalle de la aplicación finalizada.</p>
                </div>
            </header>
            
            <div class="form-card">
                <h2>Detalle de Evaluación de Desempeño</h2>
                
                <!-- Información General de la Aplicación -->
                <table class="detalle-table">
                    <tr>
                        <th class="detalle-header">Instrumento Aplicado</th>
                        <td><?php echo htmlspecialchars($datos['instrumento_nombre']); ?></td>
                    </tr>
                    <tr>
                        <th class="detalle-header">Profesor Evaluado</th>
                        <!--  CORRECCIÓN: Usando los tres campos de nombre y apellido -->
                        <td><?php echo htmlspecialchars($datos['profesor_nombre'] . ' ' . $datos['profesor_apellido_pa'] . ' ' . $datos['profesor_apellido_ma']); ?></td>
                    </tr>
                    <tr>
                        <th class="detalle-header">Período</th>
                        <td><?php echo htmlspecialchars($datos['periodo_nombre']); ?></td>
                    </tr>
                    <tr>
                        <th class="detalle-header">Puntaje Total Obtenido</th>
                        <td class="puntaje-final-text"><?php echo htmlspecialchars($datos['puntaje']); ?></td>
                    </tr>
                </table>
                
                <!-- Observaciones Generales -->
                <h4 class="detalle-subtitulo">Observaciones Generales</h4>
                <div class="general-obs-box">
                    <?php echo htmlspecialchars($datos['observaciones'] ?: 'No se registraron observaciones generales.'); ?>
                </div>

                <!-- Resultados por Rubro -->
                <h4 class="detalle-subtitulo">Resultados Detallados por Rubro</h4>
                
                <?php if (!empty($datos['respuestas_rubros'])): ?>
                    <?php foreach ($datos['respuestas_rubros'] as $respuesta): ?>
                        <div class="rubro-detalle-box">
                            <h4 class="rubro-titulo"><?php echo htmlspecialchars($respuesta['rubro_nombre']); ?></h4>
                            <p><strong>Puntaje Obtenido:</strong> <span class="puntaje-rubro-text"><?php echo htmlspecialchars($respuesta['puntaje_obtenido']); ?></span></p>
                            
                            <?php if (!empty($respuesta['comentario_adicional'])): ?>
                                <div class="comentario-box">
                                    Comentario del Evaluador: <?php echo htmlspecialchars($respuesta['comentario_adicional']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <hr class="rubro-divisor">
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="form-subtitle">No se encontraron respuestas detalladas para este instrumento.</p>
                <?php endif; ?>

                <br>
                <?php if ($rol_actual == 'Administrador' || $rol_actual == 'Coordinador'): ?>
                    <a href="index.php?controlador=aplicacion&accion=consultarAplicaciones" class="button-secondary">
                        Volver a la Consulta General
                    </a>
                <?php else: ?>
                    <a href="index.php?controlador=profesorpanel&accion=seguimientoEvaluaciones" class="button-secondary">
                        Volver a Mi Seguimiento
                    </a>
                <?php endif; ?>
            </div>

        </div> <!-- fin main-content -->
    </div> <!-- fin main-container -->
</body>
</html>