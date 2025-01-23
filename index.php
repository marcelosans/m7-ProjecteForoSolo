<?php
session_start(); 
if (!isset($_SESSION['email'])) {
    header('Location: login.php'); 
    exit;
}
echo 'Bienvenido seas: ';
echo $_SESSION['email'];


?>
<!DOCTYPE html>
<html lang="ca">
    <head>
        <meta charset="utf-8">
        <title>Inicio</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="">
    </head>
    <body>
    <form action="cerrarSesion.php" method="POST">
    <button type="submit" value="cerrar-session">Cerrar Sessión</button>
</form>
    </body>
</html>