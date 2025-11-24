<?php
// Obtener el rol actual para las restricciones de seguridad
$rol_actual = $_SESSION['rol_usuario'] ?? 'Profesor'; 

// Asumimos que $row contiene los datos del usuario a editar
$nombre_completo = htmlspecialchars($row['nombre'] . ' ' . $row['apellido_pa']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDITAR USUARIO</title>
    <link rel="stylesheet" href="public/css/panel_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/css/crud_style.css?v=<?php echo time(); ?>">
</head>
<body>

    <div class="main-container">
        
        <div class="main-content">
            
            <header class="module-header">
                <div class="header-title">
                    <h1>Módulo: Gestión de Usuarios</h1>
                    <p>Editando perfil de: <?php echo $nombre_completo; ?></p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($rol_actual); ?></span>                    
                </div>
                </header>

            <form action="index.php?controlador=user&accion=actualizarUsuario" method="POST" class="form-card">
                <h2>Edición de usuario</h2>
                <p class="form-subtitle">Los campos marcados con * son obligatorios.</p>

                <input type="hidden" name="id" value="<?php echo $row['id_profesor']; ?>">

                <div class="form-grid">

                    <div class="form-field">
                        <label for="matricula">Matrícula: *</label>
                        <input type="text" id="matricula" name="matricula" value="<?php echo htmlspecialchars($row['matricula']); ?>" required>
                    </div>

                    <div class="form-field"> 
                        <label for="nombre">Nombre(s): *</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($row['nombre']); ?>" required>
                    </div>

                    <div class="form-field">
                        <label for="apellido_pa">Apellido Paterno: *</label>
                        <input type="text" id="apellido_pa" name="apellido_pa" value="<?php echo htmlspecialchars($row['apellido_pa']); ?>" required>
                    </div>

                    <div class="form-field">
                        <label for="apellido_ma">Apellido Materno: </label>
                        <input type="text" id="apellido_ma" name="apellido_ma" value="<?php echo htmlspecialchars($row['apellido_ma']); ?>">
                    </div>

                    <div class="form-field">
                        <label for="sexo">Sexo: *</label>
                        <select id="sexo" name="sexo" required>
                            <option value="M" <?php echo ($row['sexo'] == 'M') ? 'selected' : ''; ?>>Masculino</option>
                            <option value="F" <?php echo ($row['sexo'] == 'F') ? 'selected' : ''; ?>>Femenino</option>
                            <option value="O" <?php echo ($row['sexo'] == 'O') ? 'selected' : ''; ?>>Otro</option>
                        </select>
                    </div>

                    <div class="form-field">
                        <label for="grado_academico">Grado Académico: *</label>
                        <input type="text" id="grado_academico" name="grado_academico" value="<?php echo htmlspecialchars($row['grado_academico']); ?>" required>
                    </div>
                    
                    

                </div> <div class="button-group">
                    <input type="submit" name="guardar_cambios" value="Guardar Cambios" class="button-primary">
                    
                    <a href="index.php?controlador=user&accion=consultarUsuarios" class="button-secondary">
                        Cancelar y Volver
                    </a>
                </div>
            </form>
            
        </div> </div> </body>
</html>