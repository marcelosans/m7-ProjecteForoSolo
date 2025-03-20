<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forosolo || Temas</title>
    <link rel="stylesheet" href="../CSS/Navbar.css">
    <link rel="stylesheet" href="../CSS/Temas.css">
    <link rel="stylesheet" href="../CSS/Loading.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body>

    <header class="navbar">
        <div class="container">
            <!-- Logo -->
            <a href="HomePage.php" class="logo">
                <img src="../Recursos/img/logo-forosolo.png" alt="Logo">
            </a>
            
            <div id="content">

            <!-- Menú Desktop -->
            <nav class="nav-links">
                <a href="#">Inicio</a>
                <a href="#" >Temas</a>

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
            <img src="../Recursos/img/logo-forosolo.png" alt="Logo" class="mobile-logo">
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

    <div id="loader" class="loader-overlay">
        <div>
            <img id="loading-gif" src="" alt="Cargando..." class="loading-gif">
            <div class="loading-text">CARGANDO...</div>
        </div>
    </div>

    <h1>TEMAS</h1>
    <main class="page-content">
        <div class="container main-container">
            

            <div class="content-layout">
                <div class="temas-container">
                    <div class="tema">
                        <div class="tema-image">
                            <img src="https://ih1.redbubble.net/image.790724956.2450/flat,750x,075,f-pad,750x1000,f8f8f8.u23.jpg" alt="Yakuza 0">
                        </div>
                        <div class="tema-info">
                            <h2>Yakuza 0</h2>
                            <div class="tema-stats">
                                <div class="stat">
                                    <span class="stat-icon">📝</span>
                                    <span class="stat-value">20 hilos</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-icon">💬</span>
                                    <span class="stat-value">100 publicaciones</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-icon">🔥</span>
                                    <span class="stat-value">Muy activo</span>
                                </div>
                            </div>
                            <a href="#" class="btn-ver-tema">Ver tema</a>
                        </div>
                    </div>

                    <div class="tema">
                        <div class="tema-image">
                            <img src="https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/32440/header.jpg?t=1604517910" alt="Lego Star Wars Complete Saga">
                        </div>
                        <div class="tema-info">
                            <h2>Lego Star Wars Complete Saga</h2>
                            <div class="tema-stats">
                                <div class="stat">
                                    <span class="stat-icon">📝</span>
                                    <span class="stat-value">20 hilos</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-icon">💬</span>
                                    <span class="stat-value">100 publicaciones</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-icon">🔥</span>
                                    <span class="stat-value">Muy activo</span>
                                </div>
                            </div>
                            <a href="#" class="btn-ver-tema">Ver tema</a>
                        </div>
                    </div>

                    <div class="tema">
                        <div class="tema-image">
                            <img src="https://www.nintendo.com/eu/media/images/10_share_images/games_15/nintendo_switch_4/H2x1_NSwitch_SuperMarioOdyssey_image1600w.jpg" alt="Super Mario Odyssey">
                        </div>
                        <div class="tema-info">
                            <h2>Super Mario Odyssey</h2>
                            <div class="tema-stats">
                                <div class="stat">
                                    <span class="stat-icon">📝</span>
                                    <span class="stat-value">15 hilos</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-icon">💬</span>
                                    <span class="stat-value">75 publicaciones</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-icon">🔥</span>
                                    <span class="stat-value">Activo</span>
                                </div>
                            </div>
                            <a href="#" class="btn-ver-tema">Ver tema</a>
                        </div>
                    </div>
                </div>

                <aside class="sidebar">
                    <div class="sidebar-box">
                        <h3>Temas Populares</h3>
                        <ul class="popular-themes">
                            <li>
                                <span class="rank">1</span>
                                <a href="#">Yakuza 0</a>
                                <span class="views">2.5k vistas</span>
                            </li>
                            <li>
                                <span class="rank">2</span>
                                <a href="#">Lego Star Wars</a>
                                <span class="views">1.8k vistas</span>
                            </li>
                            <li>
                                <span class="rank">3</span>
                                <a href="#">Super Mario Odyssey</a>
                                <span class="views">1.2k vistas</span>
                            </li>
                            <li>
                                <span class="rank">4</span>
                                <a href="#">The Legend of Zelda</a>
                                <span class="views">950 vistas</span>
                            </li>
                            <li>
                                <span class="rank">5</span>
                                <a href="#">Final Fantasy VII</a>
                                <span class="views">820 vistas</span>
                            </li>
                        </ul>
                    </div>

                    <div class="sidebar-box">
                        <h3>Temas Nuevos</h3>
                        <ul class="new-themes">
                            <li><a href="#">Hollow Knight: Silksong</a></li>
                            <li><a href="#">Elden Ring DLC</a></li>
                            <li><a href="#">Persona 6</a></li>
                        </ul>
                    </div>

                    
                </aside>
            </div>

           
        </div>
    </main>

    <script src="../Js/Loading.js"></script>

    <script>
       document.addEventListener("DOMContentLoaded", function () {
        const menuToggle = document.querySelector(".menu-toggle");
        const mobileMenu = document.querySelector(".mobile-menu");
        const closeMenu = document.querySelector(".close-menu");

        menuToggle.addEventListener("click", function () {
            mobileMenu.classList.add("active");
        });

        closeMenu.addEventListener("click", function () {
            mobileMenu.classList.remove("active");
        });

        // Si tienes elementos con id mobile-profile-toggle y mobile-dropdown, descomenta estas líneas
        /*
        const mobileProfileToggle = document.getElementById("mobile-profile-toggle");
        const mobileDropdown = document.getElementById("mobile-dropdown");

        if (mobileProfileToggle && mobileDropdown) {
            mobileProfileToggle.addEventListener("click", function () {
                mobileDropdown.classList.toggle("active");
            });
        }
        */
    });
    </script>
    

</body>
</html>