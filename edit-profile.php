<?php
session_start(); 
if (!isset($_SESSION['email'])) {
    header('Location: login.php'); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forosolo || Inicio</title>
    <link rel="stylesheet" href="./css/navbar.css">
    <link rel="stylesheet" href="./css/edit-profile.css">
    <script defer src="script.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
</head>
<body>
<header class="navbar">
        <div class="container">
            <!-- Logo -->
            <a href="./homepage.php" class="logo">
                <img src="./img/logo-forosolo.png" alt="Logo">
            </a>

            <!-- Menú Desktop -->
            <nav class="nav-links">
                <a href="#">Inicio</a>
                <a href="#">Temas</a>

                <!-- Dropdown Perfil -->
                <div class="profile">
                    <img src="https://media.tenor.com/BTz-_I5htewAAAAe/borzoi-fish.png" alt="Foto de Perfil">
                    <ul class="dropdown">
                        <li><a href="#">Mi Perfil</a></li>
                        <li><a href="#">Cerrar Sesión</a></li>
                    </ul>
                </div>
            </nav>

            <!-- Botón Menú Móvil -->
            <div class="menu-toggle">&#9776;</div>
        </div>
    </header>

    <!-- Menú Móvil -->
    <div class="mobile-menu">
        <button class="close-menu">&times;</button>
        <div class="mobile-content">
            <img src="./img/logo-forosolo.png" alt="Logo" class="mobile-logo">
            <a href="#">Inicio</a>
            <a href="#">Temas</a>

            <!-- Dropdown en móvil -->
            <div class="mobile-profile">
                <img src="https://media.tenor.com/BTz-_I5htewAAAAe/borzoi-fish.png" alt="Foto de Perfil">
                <a href="#">Mi Perfil</a>
                <a href="#">Cerrar Sesión</a>
            </div>
        </div>
    </div>

    <div class="profile-container">
        <div class="profile-picture">
            <img src="https://media.tenor.com/BTz-_I5htewAAAAe/borzoi-fish.png" alt="Foto de Perfil">
            <p>Cambia Foto de Perfil</p>
        </div>
        
        <div class="profile-info">
            <form action="actualizar_perfil.php" method="POST">
                <label>Username</label>
                <input type="text" name="username" value="">

                <label>Nom</label>
                <input type="text" name="nombre" value="">

                <label>Cognom</label>
                <input type="text" name="apellido" value="">

                <label>Biografía</label>
                <input type="text" name="bio" value="">

                <label>Fecha Nacimiento</label>
                <input type="text" name="fecha_nacimiento" value="31-8-2004">

                <label>Ubicación</label>
                <input type="text" name="ubicacion" value="España">
                
                <button type="submit">Guardar Cambios</button>
            </form>
        </div>
    </div>
</body>
</html>
