<?php
?>

<!DOCTYPE html>
<html lang="ca">
    <head>
        <meta charset="utf-8">
        <title>ForoSolo</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="./css/login.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
    </head>
    <body>
    
    <div class="login-container">
       
        <img src="./img/logo-forosolo.png" alt="logo-foro-solo">
        <form action="login.php" method="POST">
            <h1>¿Te has olvidado la contraseña?</h1>
            <br>
            <div class="inputform">
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Email/Usuario" required>
            </div>
            </div>
            <button type="submit" class="btn">Restablecer Contraseña</button>
        </form>
        <div class="options">
            <a href="login.php">¿Tienes cuenta?</a>
        </div>
        <div class="options">
            <a href="register.php">Regístrate</a>
        </div>
    </div>
    </body>
</html>