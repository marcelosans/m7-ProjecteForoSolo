<?php
session_start(); 
if (!isset($_SESSION['email'])) {
    header('Location: Login.php'); 
    exit;
}

require_once('ConectaDB.php');
         $sql = 'SELECT * FROM `users` WHERE mail = :email ';

$preparada = $db->prepare($sql);
$preparada->bindParam(':email', $_SESSION['email']);
$preparada->execute();
$usuario = $preparada->fetch(PDO::FETCH_ASSOC);
?>



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
                <a href="./Temas.php">Temas</a>

                <!-- Dropdown Perfil -->
                <div class="profile">
                <img src="<?= !empty($usuario['profile_image']) ?  $usuario['profile_image'] : '../profile/profile.png' ?>" alt="Imagen de perfil">
                    <ul class="dropdown">
                        <li><a href="./Perfil.php">Mi Perfil</a></li>
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
            <a href="">Inicio</a>
            <a href="./Temas.php">Temas</a>

            <!-- Dropdown en móvil -->
            <div class="mobile-profile">
            <img src="<?= !empty($usuario['profile_image']) ?  $usuario['profile_image'] : '../profile/profile.png' ?>" alt="Imagen de perfil">
                <a href="./Perfil.php">Mi Perfil</a>
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


    <div id="loader" class="loader-overlay">
        <div>
            <img id="loading-gif" src="" alt="Cargando..." class="loading-gif">
            <div class="loading-text">CARGANDO...</div>
        </div>
    </div>

    <h1>TEMAS</h1>
    <main class="page-content">
        <div class="container main-container">
            

        <?php
            require_once('ConectaDB.php');
            $sql = 'SELECT * FROM `Tema`';
            $preparada = $db->prepare($sql);
            $preparada->execute();
            $temas = $preparada->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <div class="content-layout">
                <div class="temas-container">
                <?php foreach ($temas as $tema): ?>

            <div class="tema">
                <div class="tema-image">
                    <img src="<?= htmlspecialchars($tema['imagen']) ?>" alt="<?= htmlspecialchars($tema['nomVideojoc']) ?>">
                </div>
                <div class="tema-info">
                    <h2><?= htmlspecialchars($tema['nomVideojoc']) ?></h2>
                    <div class="tema-stats">
                        <div class="stat">
                            <span class="stat-icon">📝</span>
                            <span class="stat-value">20 hilos</span>
                        </div>
                        <div class="stat">
                            <span class="stat-icon">💬</span>
                            <span class="stat-value"><?php
                      
                        $numPublicaciones = "SELECT COUNT(P.idPublicacio) AS totalPublicaciones
                                                FROM Tema T
                                                LEFT JOIN Hilo H ON T.nomVideojoc = H.nomVideojoc
                                                LEFT JOIN Publicacio P ON H.idHilo = P.idHilo
                                                WHERE T.nomVideojoc = :nomVideojoc";

                        $preparada = $db->prepare($numPublicaciones);
                        $preparada->bindParam(':nomVideojoc', $tema['nomVideojoc'], PDO::PARAM_INT);
                        $preparada->execute();
                        $resultado = $preparada->fetch(PDO::FETCH_ASSOC);

                        $totalPublicaciones = $resultado['totalPublicaciones'];

                       

                        echo "Publicaciones: $totalPublicaciones";
                          ?></span>
                        </div>
                        
                    </div>
                    <a href="<?php echo '../PHP/hilos-page.php?tema='.  $tema['nomVideojoc'] ?>" class="btn-ver-tema">Ver tema</a>
                </div>
            </div>
        <?php endforeach; ?>
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