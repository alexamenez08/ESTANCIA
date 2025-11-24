<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/styles.css?v<?php echo time(); ?>">
    <title>Ingresa al sistema</title>
</head>
<body> 

    <div class="login-wrapper">
        <div class="login-header-card">
            <div class="login-logo"></div><div class="login-title-group">
                <h2>Universidad Politécnica del Estado de Morelos</h2>
                <p>UPEMOR</p>
            </div>
        </div>

        <div class="login-form-card">
            <h1>Ingresa al sistema web de desempeño docente</h1>

            <p>Introduce tus credenciales</p>

            <form action="index.php?controlador=acceso&accion=iniciarSesion" method="POST">
                <?php if(isset($_GET['error'])): ?>
                    <p class="login-error-message"> Matrícula o contraseña incorrectos </p>
                <?php endif; ?>
                <b><label for="matricula">Matricula: </label></b>
                <input type="text" name="matricula" required>
                <br><br>

                <b><label for="clave">Contraseña: </label></b>
                <input type="password" name="clave" required>
                <br><br>

                <input type="submit" value="Ingresar">
            </form>
        </div>

    </div>
    
</body>
</html>