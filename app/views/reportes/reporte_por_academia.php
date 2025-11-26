<?php 
// $estadisticas viene del controlador
$rol = $_SESSION['rol_usuario'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Avance por Academia</title>
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="main-container">
        
        <nav class="sidebar">
            <div class="sidebar-header">
                <span class="logo">UPEMOR</span>
                <span class="logo-sub">Reportes</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php?controlador=report&accion=reportePorAcademia" class="sidebar-link active">Reportes</a></li>
            </ul>
        </nav>

        <div class="main-content">
            
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Reportes</h1>
                    <p>Avance de Evaluaciones Docentes por Área.</p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol); ?></span>
                    <a href="index.php?controlador=acceso&accion=cerrarSesion" class="logout-link">Cerrar Sesión</a>
                </div>
            </header>

            <div class="form-card">
                <h3>Reporte: Avance de Aplicaciones por Academia</h3>
                <p class="form-subtitle">Muestra el total de evaluaciones asignadas vs. las completadas de cada academia.</p>
                
                <div class="button-group">
                    <a href="index.php?controlador=report&accion=generarReporteAcademiaPDF" class="button-primary">
                        Descargar PDF
                    </a>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Academia</th>
                            <th class="text-center">Completadas</th>
                            <th class="text-center">Total Asignadas</th>
                            <th class="col-avance">Porcentaje de Avance</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (isset($estadisticas) && $estadisticas->num_rows > 0):
                            // Reiniciamos el puntero para la impresión si fuera necesario
                            $estadisticas->data_seek(0); 
                            while($stat = $estadisticas->fetch_assoc()):
                                
                                $completadas = (int)$stat['total_completadas'];
                                $asignadas = (int)$stat['total_asignadas'];
                                $porcentaje = 0;
                                
                                // Lógica de cálculo (se mantiene en PHP)
                                if ($asignadas > 0) {
                                    $porcentaje = ($completadas / $asignadas) * 100;
                                } elseif ($completadas > 0) { 
                                    $porcentaje = 100;
                                }

                        ?>
                            <tr>
                                <td class="text-strong"><?php echo htmlspecialchars($stat['academia_nombre']); ?></td>
                                <td class="text-center"><?php echo $completadas; ?></td>
                                <td class="text-center"><?php echo $asignadas; ?></td>
                                <td>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar <?php if($porcentaje < 100) echo 'progress-bar-pending'; ?>" 
                                             data-percentage="<?php echo number_format($porcentaje, 0); ?>">
                                            <?php echo number_format($porcentaje, 0); ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                            endwhile; 
                        else:
                        ?>
                            <tr>
                                <td colspan="4" class="text-center no-data">No se encontraron datos de academias o aplicaciones.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <br>
            <a href="index.php?controlador=acceso&accion=panelPrincipal" class="button-secondary back-to-panel">
                &larr; Volver al Panel Principal
            </a>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Selecciona todas las barras de progreso que tienen el atributo de datos
            const progressBars = document.querySelectorAll('.progress-bar[data-percentage]');

            progressBars.forEach(bar => {
                const percentage = bar.getAttribute('data-percentage');
                // Aplica el ancho dinámico usando JavaScript
                bar.style.width = percentage + '%'; 
            });
        });
    </script>
</body>
</html>