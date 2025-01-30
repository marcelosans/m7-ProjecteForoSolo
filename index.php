<?php
session_start(); 
if (!isset($_SESSION['email'])) {
    header('Location: login.php'); 
    exit;
}
echo '<h1>Bienvenido seas: </h1>';
echo '<h2>'.  $_SESSION['email'] . '</h2>';


?>
<!DOCTYPE html>
<html lang="ca">
    <head>
        <meta charset="utf-8">
        <title>Inicio</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="./css/style.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
    </head>
    <body>
    <form action="cerrarSesion.php" method="POST">
    <img src="./img/pescando.gif" alt="hombre-pescando">
    <br>
    <button type="submit" value="cerrar-session">Cerrar Sessión</button>
</form>
    </body>
</html>