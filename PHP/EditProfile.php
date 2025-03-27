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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $nombre = htmlspecialchars($_POST['nombre']);
    $apellido = htmlspecialchars($_POST['apellido']);
    $bio = htmlspecialchars($_POST['bio']);
    $ubicacion = htmlspecialchars($_POST['ubicacion']);
    $fecha_nacimiento = htmlspecialchars($_POST['fecha_nacimiento']);
    $iduser = $usuario['iduser'];
    
    // Manejo de imágenes
    $image_base64 = !empty($_POST['image_base64']) ? $_POST['image_base64'] : $usuario['profile_image'];
    $banner_base64 = !empty($_POST['banner_base64']) ? $_POST['banner_base64'] : $usuario['banner'];

    if (!empty($_POST['image_base64']) && strpos($_POST['image_base64'], 'data:image') === false) {
        $image_base64 = 'data:image/jpeg;base64,' . $_POST['image_base64'];
    }
    if (!empty($_POST['banner_base64']) && strpos($_POST['banner_base64'], 'data:image') === false) {
        $banner_base64 = 'data:image/jpeg;base64,' . $_POST['banner_base64'];
    }

    // Actualizar datos del usuario
    $stmt = $db->prepare("UPDATE users SET username = ?, userFirstName = ?, userLastName = ?, bio = ?, location = ?, age = ?, profile_image = ?, banner = ? WHERE iduser = ?");
    $stmt->execute([$username, $nombre, $apellido, $bio, $ubicacion, $fecha_nacimiento, $image_base64, $banner_base64, $iduser]);
    
    echo "<meta http-equiv='refresh' content='0'>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forosolo || Inicio</title>
    <link rel="stylesheet" href="../CSS/Navbar.css">
    <link rel="stylesheet" href="../CSS/Loading.css">
    <link rel="stylesheet" href="../CSS/EditProfile.css">
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
</head>
<body>
    <div id="loader" class="loader-overlay">
        <div>
            <img id="loading-gif" src="" alt="Cargando..." class="loading-gif">
            <div class="loading-text">CARGANDO...</div>
        </div>
    </div>
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
                        <img src="<?= !empty($usuario['profile_image']) ?  $usuario['profile_image'] : '../profile/profile.png' ?>" alt="Imagen de perfil">
                        <ul class="dropdown">
                            <li><a href="./Perfil.php">Mi Perfil</a></li>
                            <li><a href="cerrarSesion.php">Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </nav>
                <div class="menu-toggle">&#9776;</div>
            </div>
        </header>

        <div class="profile-info">
            <form action="" method="POST" enctype="multipart/form-data" id="profile-form">
                <div class="profile-container">
                    <div class="profile-picture">
                        <img src="<?= !empty($usuario['profile_image']) ? $usuario['profile_image'] : '../profile/profile.png' ?>" alt="Foto de Perfil" id="profile-img">
                        <label for="profile-upload" class="change-photo">Cambia Foto de Perfil</label>
                        <input type="file" id="profile-upload" name="profile-image" accept="image/*" style="display: none;">
                        <input type="hidden" name="image_base64" id="image_base64" value="">
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($usuario['username']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nom</label>
                        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['userFirstName']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="apellido">Cognom</label>
                        <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($usuario['userLastName']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="bio">Biografía</label>
                        <textarea id="bio" name="bio" rows="3"><?= htmlspecialchars($usuario['bio']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="fecha_nacimiento">Fecha Nacimiento</label>
                        <input type="date" id="fecha_nacimiento" max="<?= date('Y-m-d') ?>" name="fecha_nacimiento" value="<?= htmlspecialchars($usuario['age']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="ubicacion">Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion" value="<?= htmlspecialchars($usuario['location']) ?>">
                    </div>
                    <div class="form-group banner-container">
                        <label for="banner-upload" class="change-banner">Cambia Banner</label>
                        <input type="file" id="banner-upload" name="banner-image" accept="image/*" style="display: none;">
                        <input type="hidden" name="banner_base64" id="banner_base64" value="">
                        <div class="banner-preview">
                            <img src="<?= !empty($usuario['banner']) ? $usuario['banner'] : '../profile/bannerDefault.jpg' ?>" alt="Banner" id="banner-img">
                        </div>
                    </div>
                    <button type="submit" class="save-button">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../Js/Loading.js"></script>
    <script src="../Js/NavBar.js"></script>
    <script src="../Js/EditProfile.js"></script>

</body>
</html>