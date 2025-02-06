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
            <h1>Restablece la contraseña</h1>
            <br>
            <br>
            <div class="inputform">
            <div class="form-group">
                <input type="password" id="new_password" name="new_password" placeholder="Nueva Contraseña" required>
            </div>
            <div class="form-group">
                <input type="password" id="again_password" name="again_password" placeholder="Confirmar Contraseña" required>
            </div>
            </div>
            <button type="submit" class="btn">Restablecer la contraseña</button>
        </form>
    </div>
    </body>
</html>