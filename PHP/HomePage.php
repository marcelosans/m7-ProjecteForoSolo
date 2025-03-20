<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forosolo || Inicio</title>
    <link rel="stylesheet" href="../CSS/Navbar.css">
    <link rel="stylesheet" href="../CSS/Loading.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
</head>
<body>

    <header class="navbar">
    <div id="loader" class="loader-overlay">
        <div>
            <img id="loading-gif" src="" alt="Cargando..." class="loading-gif">
            <div class="loading-text">CARGANDO...</div>
        </div>
    </div>

    <div class="login-container">
        <div id="content">
        <div class="container">
            <!-- Logo -->
            <a href="../PHP/HomePage.php" class="logo">
                <img src="../Recursos/img/logo-forosolo.png" alt="Logo">
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
            <img src="../Resursos/img/logo-forosolo.png" alt="Logo" class="mobile-logo">
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

    <script src="../Js/Loading.js"></script>

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
</body>
</html>
