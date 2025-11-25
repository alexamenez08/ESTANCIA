<?php 
    //? Incluir modelos y librerías externas
    include_once "app/models/ReportModel.php";
    include_once "public/libraries/phplot/phplot.php";
    include_once "config/db_connection.php";

    //! Clase controladora para generar reportes estadísticos y documentos PDF (FPDF).
    class ReportController{
        private $model;

        public function __construct($connection){
            //? Inicializa la instancia del modelo
            $this -> model = new ReportModel($connection);
        }

        //* Método para mostrar la vista (formulario) de selección de profesor para el reporte individual.
        public function vistaReporteProfesor(){
            //? Obtiene la lista de profesores para llenar el dropdown
            $profesores = $this->model->consultarTodosProfesores();
            //? Carga la vista del selector
            include_once "app/views/reportes/selector_profesor.php";
        }

        //* Método para generar el PDF del Reporte Individual por Profesor (usando FPDF).
        public function generarReporteProfesor(){
            //? Válida que se haya seleccionado un ID de profesor
            if (!isset($_POST['id_profesor']) || empty($_POST['id_profesor'])) {
                die("Error: No se seleccionó ningún profesor.");
            }
            $id_profesor = (int)$_POST['id_profesor'];

            //? Obtiene datos del profesor y sus materias asignadas
            $profesor = $this->model->consultarProfesorPorID($id_profesor);
            $materias = $this->model->consultarMateriasPorProfesor($id_profesor);

            if (!$profesor) {
                die("Error: Profesor no encontrado.");
            }

            //? Configuración inicial del PDF
            $pdf = new FPDF('P', 'mm', 'Letter');
            $pdf->AddPage();
            $pdf->SetMargins(20, 20, 20);

            //? --- Encabezado del Reporte ---
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, utf8_decode("Reporte Individual de Profesor"), 0, 1, 'C');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 7, utf8_decode("Gestión del Trabajo Docente"), 0, 1, 'C');
            $pdf->Ln(15);

            //? --- Datos del Profesor ---
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 8, ("Datos del Docente"), 0, 1, 'L');
            $pdf->SetFont('Arial', '', 12);
            
            $nombre_completo = ($profesor['nombre'] . ' ' . $profesor['apellido_pa'] . ' ' . $profesor['apellido_ma']);
            
            $pdf->Cell(40, 7, utf8_decode("Nombre:"), 0, 0);
            $pdf->Cell(0, 7, utf8_decode($nombre_completo), 0, 1);
            
            $pdf->Cell(40, 7, utf8_decode("Matrícula:"), 0, 0);
            $pdf->Cell(0, 7, utf8_decode($profesor['matricula']), 0, 1);
            
            $pdf->Cell(40, 7, utf8_decode("Grado Académico:"), 0, 0);
            $pdf->Cell(0, 7, utf8_decode($profesor['grado_academico']), 0, 1);
            
            $pdf->Cell(40, 7, utf8_decode("Rol:"), 0, 0);
            $pdf->Cell(0, 7, utf8_decode($profesor['rol']), 0, 1);
            $pdf->Ln(10);

            //? --- Tabla de Materias ---
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 8, utf8_decode("Materias que Imparte"), 0, 1, 'L');
            
            //? Cabecera de la tabla de materias
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetFillColor(230, 230, 230);
            $pdf->SetTextColor(0);
            $pdf->Cell(25, 10, 'Clave', 1, 0, 'C', true);
            $pdf->Cell(65, 10, 'Nombre de la Materia', 1, 0, 'C', true);
            $pdf->Cell(75, 10, 'Academia', 1, 1, 'C', true);

            $pdf->SetFont('Arial', '', 10);

            //? Llenado de filas de la tabla
            if ($materias->num_rows > 0) {
                while($materia = $materias->fetch_assoc()) {
                    $pdf->Cell(25, 8, utf8_decode($materia['clave']), 1, 0, 'L');
                    $pdf->Cell(65, 8, utf8_decode($materia['nombre_materia']), 1, 0, 'L');
                    $pdf->Cell(75, 8, utf8_decode($materia['nombre_academia'] ?? 'N/A'), 1, 1, 'L');
                }
            } else {
                $pdf->Cell(175, 10, utf8_decode("Este profesor no tiene materias asignadas actualmente."), 1, 1, 'C');
            }

            //? Salida del PDF (Descarga)
            $nombre_archivo = "Reporte_Profesor_" . $profesor['apellido_pa'] . "_" . $profesor['id_profesor'] . ".pdf";
            $pdf->Output('D', $nombre_archivo);
        }

        
        //* Método para mostrar la vista de reporte de avance de aplicaciones por academia.
        public function reportePorAcademia() {
            
            //? 1. Obtener los datos estadísticos del modelo
            $estadisticas = $this->model->getEstadisticasPorAcademia();

            //? 2. Cargar la vista y pasarle los datos
            include "app/views/reportes/reporte_por_academia.php";
        }

        
        //* Método para generar el PDF del reporte de avance de aplicaciones por academia (usando FPDF).
        public function generarReporteAcademiaPDF() {
            
            //? 1. Obtener los datos estadísticos del modelo (la misma fuente que la vista)
            $estadisticas = $this->model->getEstadisticasPorAcademia();

            //? 2. Crear la instancia de FPDF
            $pdf = new FPDF('P', 'mm', 'Letter'); // P = Portrait (Vertical)
            $pdf->AddPage();
            $pdf->SetMargins(20, 20, 20); // Márgenes de 2 cm

            //? --- Encabezado del Reporte ---
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, utf8_decode("Reporte de Avance por Academia"), 0, 1, 'C');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 7, utf8_decode("Estado de aplicaciones: Pendientes vs. Completadas"), 0, 1, 'C');
            $pdf->Ln(15); // Salto de línea

            //? --- Cabecera de la Tabla ---
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetFillColor(230, 230, 230); // Relleno gris claro
            $pdf->SetTextColor(0); // Texto negro
            $pdf->Cell(70, 10, utf8_decode('Academia'), 1, 0, 'C', true); 
            $pdf->Cell(35, 10, utf8_decode('Completadas'), 1, 0, 'C', true); 
            $pdf->Cell(35, 10, utf8_decode('Asignadas'), 1, 0, 'C', true); 
            $pdf->Cell(35, 10, ('Avance (%)'), 1, 1, 'C', true); 
            
            //? --- Cuerpo de la Tabla ---
            $pdf->SetFont('Arial', '', 10);

            //? Llenado de filas de la tabla
            if ($estadisticas->num_rows > 0) {
                while($stat = $estadisticas->fetch_assoc()) {
                    
                    $completadas = (int)$stat['total_completadas'];
                    $asignadas = (int)$stat['total_asignadas'];
                    $porcentaje_num = 0;
                    
                    //? Cálculo del porcentaje de avance
                    if ($asignadas > 0) {
                        $porcentaje_num = ($completadas / $asignadas) * 100;
                    } elseif ($completadas > 0) { //? Si no hay asignadas pero sí completadas
                        $porcentaje_num = 100;
                    }
                    
                    $porcentaje_texto = number_format($porcentaje_num, 0) . " %";

                    //? Dibujar celdas
                    $pdf->Cell(70, 8, utf8_decode($stat['academia_nombre']), 1, 0, 'L');
                    $pdf->Cell(35, 8, $completadas, 1, 0, 'C');
                    $pdf->Cell(35, 8, $asignadas, 1, 0, 'C');
                    $pdf->Cell(35, 8, $porcentaje_texto, 1, 1, 'R');
                }
            } else {
                $pdf->Cell(175, 10, utf8_decode("No se encontraron datos de academias o aplicaciones."), 1, 1, 'C');
            }

            //? 3. Salida del PDF (Descarga)
            $nombre_archivo = "Reporte_Avance_Academias_" . date('Y-m-d') . ".pdf";
            $pdf->Output('D', $nombre_archivo); // 'D' fuerza la descarga
            exit; //? Terminar el script después de generar el PDF
        }

        
        //* Método para generar el Reporte Gráfico de Avance por Estado (PENDIENTE/COMPLETADO) y exportar a PDF.
        public function generarReporteEstadoGrafico(){
            //? Obtiene los datos de conteo por estado
            $data = $this -> model -> consultarAplicacionesPorEstado();

            //? --- GENERAR GRÁFICA (PHPLOT) ---
            $plot = new PHPlot(600, 400); //? Tamaño para una buena resolución
            
            $plot -> SetDataValues($data);
            $plot -> SetPlotType('pie'); //? Gráfica de pastel
            $plot -> SetDataType('text-data-single'); //? Necesario para gráficas de pastel
            
            $plot -> SetTitle(utf8_decode('Distribución de Aplicaciones por Estado'));
            //? Usamos las etiquetas (PENDIENTE/COMPLETADO) como leyenda
            $plot -> SetLegend(array_column($data, 0)); 
            $plot -> SetShading(0); //? Sin sombra
            $plot -> SetDataColors(['#f0ad4e', '#7c3aed']); //? Colores para los estados

            //? Guardar la gráfica temporalmente
            $filename = 'public/media/graphs/grafica_estado_aplicaciones.png';
            
            //? Asegura que el directorio temporal exista y tenga permisos
            if (!is_dir(dirname($filename))) {
                //? @mkdir se usa para suprimir el warning si el directorio ya existe.
                @mkdir(dirname($filename), 0777, true); 
            }

            $plot -> SetOutputFile($filename);
            $plot -> SetIsInline(true); //? Guardar imagen de forma local
            $plot -> DrawGraph();

            //? --- GENERAR PDF (FPDF) ---
            $pdf = new FPDF('P', 'mm', 'Letter');
            $pdf -> AddPage();
            $pdf -> SetMargins(20, 20, 20);

            $pdf -> SetFont('Arial', 'B', 16);
            $pdf -> SetTextColor(106, 27, 154); //? Púrpura
            $pdf -> Cell(0, 10, utf8_decode('Reporte Gráfico: Estado de Evaluaciones'), 0, 1, 'C');
            $pdf -> Ln(5);
            
            $pdf -> SetFont('Arial', '', 11);
            $pdf -> SetTextColor(50, 50, 50);
            $pdf -> Cell(0, 7, utf8_decode('Distribución de tareas pendientes vs. completadas en el sistema.'), 0, 1, 'L');
            $pdf -> Ln(5);

            //? Incrustar la imagen generada
            $pdf -> Image($filename, 20, 50, 175, 120); 
            
            //? Salida del PDF (Descarga)
            $pdf -> Output('D', utf8_decode('Reporte_Estado_Evaluaciones_Gráfico.pdf'));
            exit;
        }
    }