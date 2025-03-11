<?php
session_start(); 
if (!isset($_SESSION['email'])) {
    header('Location: Login.php'); 
    exit;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>ForoSolo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../CSS/Index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <h1>Bienvenido seas:</h1>
        <h2><?php echo $_SESSION['email']; ?></h2>
        <form action="CerrarSesion.php" method="POST">
            <button class="btn" type="submit" value="cerrar-session">Cerrar Sesión</button>
        </form>
    </div>
</body>
</html>