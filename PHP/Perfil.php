<?php
session_start(); 
if (!isset($_SESSION['email'])) {
    header('Location: Login.php'); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forosolo || Inicio</title>
    <link rel="stylesheet" href="../CSS/Navbar.css">
    <link rel="stylesheet" href="../CSS/Perfil.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
</head>
<body>

    <header class="navbar">
        <div class="container">
            <!-- Logo -->
            <a href="HomePage.php" class="logo">
                <img src="../Recursos/img/logo-forosolo.png" alt="Logo">
            </a>

            <!-- Menú Desktop -->
            <nav class="nav-links">
                <a href="#">Inicio</a>
                <a href="#">Temas</a>

                <!-- Dropdown Perfil -->
                <div class="profile">
                    <img src="../profile/<?php echo $_SESSION['profile']; ?>" alt="Foto de Perfil">
                    <ul class="dropdown">
                        <li><a href="#">Mi Perfil</a></li>
                        <li><a href="cerrarSesion.php">Cerrar Sesión</a></li>
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
            <img src="../Recursos/img/logo-forosolo.png" alt="Logo" class="mobile-logo">
            <a href="#">Inicio</a>
            <a href="#">Temas</a>

            <!-- Dropdown en móvil -->
            <div class="mobile-profile">
                <img src="../profile/<?php echo $_SESSION['profile']; ?>" alt="Foto de Perfil">
                <a href="#">Mi Perfil</a>
                <a href="cerrarSesion.php">Cerrar Sesión</a>
            </div>
        </div>
    </div>
<script>
   document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.querySelector(".menu-toggle");
    const mobileMenu = document.querySelector(".mobile-menu");
    const closeMenu = document.querySelector(".close-menu");
    const mobileProfileToggle = document.getElementById("mobile-profile-toggle");
    const mobileDropdown = document.getElementById("mobile-dropdown");

    menuToggle.addEventListener("click", function () {
        mobileMenu.classList.add("active");
    });

    closeMenu.addEventListener("click", function () {
        mobileMenu.classList.remove("active");
    });

    // Mostrar/ocultar dropdown en móvil
    mobileProfileToggle.addEventListener("click", function () {
        mobileDropdown.classList.toggle("active");
    });
});
</script>


<div class="profile-container">
        <div class="banner">
            <img src="https://i.pinimg.com/736x/eb/80/34/eb803487c680ce5c7936f8c49fa8ac30.jpg" alt="Banner">
        </div>
        <div class="profile-info">
            <div class="profile-pic">
                <img src="../profile/<?php echo $_SESSION['profile']; ?>" alt="Foto de perfil">
            </div>
            <h1><?php echo $_SESSION['username']; ?></h1>
            <a href="EditProfile.php" class="edit-profile">Modificar Perfil</a>

            <div class="stats">
                <div class="stat">
                    <p>Hilos:</p>
                    <h2>69</h2>
                </div>
                <div class="about">
                    <p><strong>Sobre Mí:</strong> <?php echo $_SESSION['bio']; ?></p>
                    <p><strong>Ubicación 📍:</strong> Barcelona</p>
                    <p><strong>Nombre :</strong> <?php echo $_SESSION['nom']; ?></p>
                </div>
                <div class="stat">
                    <p>Publicaciones:</p>
                    <h2>420</h2>
                </div>
            </div>
        </div>
    </div>


</body>
</html>
