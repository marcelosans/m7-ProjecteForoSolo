<?php
session_start(); 
if (!isset($_SESSION['email'])) {
    header('Location: Login.php'); 
    exit;
}

require_once('ConectaDB.php');

// Obtener datos del usuario
$sql = 'SELECT * FROM `users` WHERE mail = :email';
$preparada = $db->prepare($sql);
$preparada->bindParam(':email', $_SESSION['email']);
$preparada->execute();
$usuario = $preparada->fetch(PDO::FETCH_ASSOC);

// Obtener cantidad de publicaciones del usuario
$sqlPublicaciones = "SELECT COUNT(*) as total FROM Publicacio WHERE iduser = :iduser"; 
$preparadaPub = $db->prepare($sqlPublicaciones);
$preparadaPub->bindParam(':iduser', $usuario['iduser']);
$preparadaPub->execute();
$totalPublicaciones = $preparadaPub->fetchColumn();

// Obtener cantidad de hilos creados por el usuario
$sqlHilo = "SELECT COUNT(*) as total FROM hilo WHERE iduser = :iduser"; 
$preparadaHilo = $db->prepare($sqlHilo);
$preparadaHilo->bindParam(':iduser', $usuario['iduser']);
$preparadaHilo->execute();
$totalHilo = $preparadaHilo->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forosolo || Inicio</title>
    <link rel="stylesheet" href="../CSS/Navbar.css">
    <link rel="stylesheet" href="../CSS/Perfil.css">
    <link rel="stylesheet" href="../CSS/Loading.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
</head>
<body>

    <div id="loader" class="loader-overlay">
        <div>
            <img id="loading-gif" src="" alt="Cargando..." class="loading-gif">
            <div class="loading-text">CARGANDO...</div>
        </div>
    </div>

    <div class="login-container">
        <div id="content">
            <!-- 🔹 Navbar -->
            <header class="navbar">
                <div class="container">
                    <!-- Logo -->
                    <a href="HomePage.php" class="logo">
                        <img src="../Recursos/img/logo-forosolo.png" alt="Logo">
                    </a>

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

            <!-- 🔹 Perfil del usuario -->
            <div class="profile-container">
                <!-- Banner -->
                <div class="banner">
                    <img src="<?= !empty($usuario['banner']) ?  $usuario['banner'] : '../profile/bannerDefault.jpg' ?>" alt="Banner">
                </div>

                <!-- Información del perfil -->
                <div class="profile-info">
                    <div class="profile-pic">
                        <img src="<?= !empty($usuario['profile_image']) ?  $usuario['profile_image'] : '../profile/profile.png' ?>" alt="Imagen de perfil">
                    </div>
                    <h1><?= $usuario['username'] ?></h1>
                    <a href="EditProfile.php" class="edit-profile">Modificar Perfil</a>

                    <!-- Estadísticas -->
                    <div class="stats">
                        <div class="stat">
                            <p>Hilos:</p>
                            <h2><?= $totalHilo ?></h2>
                        </div>

                        <div class="about">
                            <p><strong>Sobre Mí:</strong> <?= $usuario['bio'] ?></p>
                            <p><strong>Ubicación 📍:</strong> <?= $usuario['location'] ?></p>
                            <p><strong>Nombre:</strong> <?= $usuario['userFirstName'] ?></p>
                            <p><strong>Edad:</strong> 
                                <?php 
                                    $fecha_nacimiento = new DateTime($usuario['age']); // Convertir a DateTime
                                    $hoy = new DateTime(); // Fecha actual
                                    echo $hoy->diff($fecha_nacimiento)->y; // Calcular edad
                                ?>
                            </p>
                            <p><strong>Se ha unido:</strong> 
                                <?php 
                                    $fecha = new DateTime($usuario['creationDate']);
                                    echo $fecha->format('d/m/Y'); // Formato Día/Mes/Año
                                ?>
                            </p>
                        </div>

                        <div class="stat">
                            <p>Publicaciones:</p>
                            <h2><?= $totalPublicaciones ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    <script src="../Js/Loading.js"></script>
    <script src="../Js/NavBar.js"></script>
</body>
</html>
