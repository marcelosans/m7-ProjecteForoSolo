<?php
session_start();

// Redirigir al login si no hay sesión iniciada
if (!isset($_SESSION['email'])) {
    header('Location: Login.php');
    exit;
}

require_once('ConectaDB.php');

// Obtener datos del usuario actual
$sql = 'SELECT * FROM `users` WHERE mail = :email';
$preparada = $db->prepare($sql);
$preparada->bindParam(':email', $_SESSION['email']);
$preparada->execute();
$usuario = $preparada->fetch(PDO::FETCH_ASSOC);

// Obtener publicaciones con información del usuario
$sql = "SELECT p.*, u.username, u.profile_image 
        FROM Publicacio p
        JOIN Users u ON p.idUser = u.iduser";
$preparada = $db->prepare($sql);
$preparada->execute();
$publicaciones = $preparada->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forosolo || Inicio</title>
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/publicacion.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
</head>
<body>

    <div class="login-container">
        <div id="content">
            <header class="navbar">
                <div class="container">
                    <a href="HomePage.php" class="logo">
                        <img src="../Recursos/img/logo-forosolo.png" alt="Logo">
                    </a>

                    <nav class="nav-links">
                        <a href="#">Inicio</a>
                        <a href="./Temas.php">Temas</a>
                        <div class="profile">
                            <img src="<?= !empty($usuario['profile_image']) ? $usuario['profile_image'] : '../profile/profile.png' ?>" alt="Imagen de perfil">
                            <ul class="dropdown">
                                <li><a href="./Perfil.php">Mi Perfil</a></li>
                                <li><a href="cerrarSesion.php">Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    </nav>
                    <div class="menu-toggle">&#9776;</div>
                </div>
            </header>

            <div class="mobile-menu">
                <button class="close-menu">&times;</button>
                <div class="mobile-content">
                    <img src="../Recursos/img/logo-forosolo.png" alt="Logo" class="mobile-logo">
                    <a href="">Inicio</a>
                    <a href="./Temas.php">Temas</a>
                    <div class="mobile-profile">
                        <img src="<?= !empty($usuario['profile_image']) ? $usuario['profile_image'] : '../profile/profile.png' ?>" alt="Imagen de perfil">
                        <a href="./Perfil.php">Mi Perfil</a>
                        <a href="cerrarSesion.php">Cerrar Sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="publicacions">
        <?php foreach ($publicaciones as $publicacio): ?>
            <div class="post-container">
                <div class="post-header">
                    <div class="user-info">
                        <img src="<?= !empty($publicacio['profile_image']) ? 'data:image/jpeg;base64,' . htmlspecialchars($publicacio['profile_image']) : 'default-profile.png' ?>" alt="Usuario">
                        <span class="username"> <?= htmlspecialchars($publicacio['username']) ?> </span>
                    </div>
                    <span class="date">Fecha: <?= htmlspecialchars($publicacio['dataPub']) ?></span>
                </div>
                <div class="post-content">
                    <p><?= nl2br(htmlspecialchars($publicacio['Contingut'])) ?></p>
                </div>
                <button class="reply-button">Responder</button>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="../Js/NavBar.js"></script>
    
</body>
</html>
