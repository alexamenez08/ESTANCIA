<?php
//* Incluir los modelos necesarios
include_once "app/models/RubroModel.php";
include_once "app/models/InstrumentoModel.php"; 

class InstrumentoController {

    private $rubroModel;
    private $instrumentoModel;

    //* Constructor de la clase
    public function __construct($connection) {
        $this->rubroModel = new RubroModel($connection);
        $this->instrumentoModel = new InstrumentoModel($connection);
    }

    public function consultarInstrumentos() {
        // 1. Obtener la lista de instrumentos
        $instrumentos = $this->instrumentoModel->consultarTodos();

        // 2. Cargar la vista de consulta
        include_once "app/views/instrumento/consult.php";
    }

    /**
     * ACCIÓN: Muestra la vista para editar los rubros de un instrumento.
     * Ruta: index.php?controlador=instrumento&accion=editar&id=1
     */
    public function editar() {
        // 1. Validar que tengamos un ID de instrumento
        if (!isset($_GET['id'])) {
            die("Error: Se requiere un ID de instrumento.");
        }
        $id_instrumento = (int)$_GET['id'];

        // 2. Obtener los datos (del instrumento y sus rubros)
        // Obtenemos los rubros ("Aspectos a observar")
        $rubros = $this->rubroModel->getRubrosPorInstrumento($id_instrumento);

        // 3. Cargar la vista
        // Pasamos las variables $rubros y $id_instrumento a la vista
        include_once "app/views/instrumento/editar.php";
    }

    /** Procesa los cambios del formulario de edición de rubros. */
    public function guardarRubros() {
        if (isset($_POST['guardar_cambios'])) {
            $id_instrumento = (int)$_POST['id_instrumento'];
            // 🔑 INICIALIZAMOS EL CONTADOR
            $orden_contador = 1; 
            
            //* Procesar rubros existentes
            if (isset($_POST['rubro_id'])) {
                foreach ($_POST['rubro_id'] as $key => $id_rubro) {
                    $texto = trim($_POST['texto_aspecto'][$key]);
                    // 🔑 ASIGNAMOS Y AUMENTAMOS EL ORDEN
                    $orden = $orden_contador++; 

                    $this->rubroModel->actualizarRubro($id_rubro, $texto, $orden);
                }
            }

            //* procesar nuevos rubros
            if (isset($_POST['nuevo_texto'])) {
                foreach ($_POST['nuevo_texto'] as $key => $valor) {

                    if (!empty($valor)) { // Solo si no está vacío
                        $texto = trim($valor);
                        // 🔑 ASIGNAMOS Y AUMENTAMOS EL ORDEN
                        $orden = $orden_contador++; 

                        $this->rubroModel->crearRubro($id_instrumento, $texto, $orden);
                    }

                }

            }
            //* redireccionar de vuelta a la edición
            header("Location: index.php?controlador=instrumento&accion=editar&id=" . $id_instrumento);

            exit();
        }else {
            //* Si no se envió el formulario, redirigir
            header("Location: index.php?controlador=instrumento&accion=listar"); // O a tu dashboard
            exit();
        }
    }

    //* Método para mostrar el formulario de creación
    public function crearInstrumento() {
        // 1. No necesitamos datos del modelo por ahora.
        
        // 2. Cargamos la vista del formulario.
        include_once "app/views/instrumento/form_insert.php";
    }

    // Nuevo método para insertar los datos
    public function insertarInstrumento() {
        if(isset($_POST['enviar'])) {
            // 1. Capturar los datos del formulario
            $id_instrumento = (int)$_POST['id_instrumento'];
            $nombre = trim($_POST['nombre']); 
            $descripcion = trim($_POST['descripcion']); 

            // 2. Llamar al método del modelo para insertar
            $insert = $this->instrumentoModel->insertarInstrumento($id_instrumento, $nombre, $descripcion); 
            
            // 3. Verificar y redirigir
            if($insert) {
                // Redirigir de vuelta a la lista de instrumentos con mensaje de éxito
                header("Location: index.php?controlador=instrumento&accion=consultarInstrumentos&exito=1");
                exit();
            } else {
                // Manejo de error (ej. mostrar mensaje y volver al formulario)
                echo "<br>Error al registrar el instrumento. Revisa el ID si no es AUTO_INCREMENT.";
                include_once "app/views/instrumento/form_insert.php"; // Volver a la vista
            }
        } else {
            // Si se accede sin enviar el formulario, redirigir a la vista de creación
            $this->crearInstrumento();
        }
    }

    public function eliminarRubro() {
        // 1. Validar que vengan los IDs necesarios
        if (!isset($_GET['id_rubro']) || !isset($_GET['id_instrumento'])) {
            die("Error: Se requiere el ID del rubro a eliminar y el ID del instrumento.");
        }
        
        $id_rubro = (int)$_GET['id_rubro'];
        $id_instrumento = (int)$_GET['id_instrumento']; // Necesario para redirigir

        // 2. Llamar al método del modelo para la eliminación
        $delete = $this->rubroModel->eliminarRubro($id_rubro); 

        // 3. Verificar y redirigir
        if ($delete) {
            // Eliminación exitosa, redirigimos a la vista de edición del instrumento
            header("Location: index.php?controlador=instrumento&accion=editar&id=" . $id_instrumento . "&exito_del=1");
            exit();
        } else {
            // Manejo de error. Redirigimos con un mensaje de error.
            header("Location: index.php?controlador=instrumento&accion=editar&id=" . $id_instrumento . "&error_del=1");
            exit();
        }
    }
}
?>