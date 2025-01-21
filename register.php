<?php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="./css/register.css">
</head>
<body>
    <div class="overlay">
        <div class="login-container">
            <h2>Registro</h2>
            <form action="register.php" method="POST">
                <label for="username">Nombre de usuario <span style="color: red;">*</span></label>
                <input type="text" id="username" name="username" placeholder="Ingrese su nombre de usuario" required>
                
                <label for="email">Correo electrónico <span style="color: red;">*</span></label>
                <input type="email" id="email" name="email" placeholder="Ingrese su correo electrónico" required>
                
                <label for="first_name">Nombre</label>
                <input type="text" id="first_name" name="first_name" placeholder="Ingrese su nombre (opcional)">
                
                <label for="last_name">Apellido</label>
                <input type="text" id="last_name" name="last_name" placeholder="Ingrese su apellido (opcional)">
                
                <label for="password">Contraseña <span style="color: red;">*</span></label>
                <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
                
                <label for="verify_password">Confirmar contraseña <span style="color: red;">*</span></label>
                <input type="password" id="verify_password" name="verify_password" placeholder="Confirme su contraseña" required>
                
                <button type="submit">Registrarse</button>
            </form>
            <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
        </div>
    </div>
</body>
</html>
