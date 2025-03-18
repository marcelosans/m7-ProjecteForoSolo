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
    <link rel="stylesheet" href="../CSS/EditProfile.css">
    <script defer src="script.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
</head>
<body>

    <?php
    require_once('ConectaDB.php');
    $sql = 'SELECT * FROM `users` WHERE mail = :email';
    $preparada = $db->prepare($sql);
    $preparada->bindParam(':email', $_SESSION['email']);
    $preparada->execute();
    $usuario = $preparada->fetch(PDO::FETCH_ASSOC);
    ?>

    
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
    
    
    <div class="profile-info">
    <form action="" method="POST" enctype="multipart/form-data" id="profile-form">
        
         
        <div class="profile-container">
            <div class="profile-picture">
                <img src="<?php echo $usuario['profile_image'] ? $usuario['profile_image'] : '../profile/profile.png'; ?>" 
                     alt="Foto de Perfil" id="profile-img">
                <label for="profile-upload" class="change-photo">Cambia Foto de Perfil</label>
                <input type="file" id="profile-upload" name="profile-image" accept="image/*" style="display: none;">
                <input type="hidden" name="image_base64" id="image_base64" value="">
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($usuario['username']); ?>">
                <span id="username-msg"></span> 
            </div>

            <div class="form-group">
                <label for="nombre">Nom</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['userFirstName']); ?>">
            </div>

            <div class="form-group">
                <label for="apellido">Cognom</label>
                <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($usuario['userLastName']); ?>">
            </div>

            <div class="form-group">
                <label for="bio">Biografía</label>
                <textarea id="bio" name="bio" rows="3"><?php echo htmlspecialchars($usuario['bio']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="fecha_nacimiento">Fecha Nacimiento</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($usuario['age']); ?>">
            </div>

            <div class="form-group">
                <label for="ubicacion">Ubicación</label>
                <input type="text" id="ubicacion" name="ubicacion" value="<?php echo htmlspecialchars($usuario['location']); ?>">
            </div>

            <div class="form-group banner-container">
                <label for="banner-upload" class="change-banner">Cambia Banner</label>
                <input type="file" id="banner-upload" name="banner-image" accept="image/*" style="display: none;">
                <input type="hidden" name="banner_base64" id="banner_base64" value="">
                
                <div class="banner-preview">
                    <img src="<?php echo $usuario['banner'] ? $usuario['banner'] : '../profile/bannerDefault.jpg'; ?>" 
                        alt="Banner" id="banner-img">
                </div>
            </div>

            <button type="submit" class="save-button" id="save-btn" >Guardar Cambios</button>
        </div>
    </form>
</div>

<script>
document.getElementById("username").addEventListener("input", function () {
    let username = this.value.trim();
    let saveButton = document.getElementById("save-btn");
    let msg = document.getElementById("username-msg");

    if (username.length > 0) {
        fetch("check_username.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "username=" + encodeURIComponent(username)
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                msg.textContent = "Nombre de usuario ya en uso";
                msg.style.color = "red";
                saveButton.disabled = true; // Bloquear envío del formulario
            } else {
                msg.textContent = "Nombre de usuario disponible";
                msg.style.color = "green";
                saveButton.disabled = false; // Permitir envío del formulario
            }
        })
        .catch(error => console.error("Error:", error));
    } else {
        msg.textContent = "";
        saveButton.disabled = true; // Bloquear si el campo está vacío
    }
});


    window.addEventListener('DOMContentLoaded', function() {
        // Si el usuario ya tiene un banner, lo cargamos
        <?php if(!empty($usuario['banner'])): ?>
        document.getElementById('profile-banner').style.backgroundImage = 'url("<?php echo $usuario['banner']; ?>")';
        <?php endif; ?>
    });

    document.getElementById('profile-upload').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profile-img').src = e.target.result; // Mostrar la imagen
            document.getElementById('image_base64').value = e.target.result.split(',')[1]; // Guardar en el input oculto
        };
        reader.readAsDataURL(file);
    }
});

// Script para el banner
document.getElementById("banner-upload").addEventListener("change", function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById("banner-img").src = e.target.result; // Cambia la imagen del banner
            document.getElementById("banner_base64").value = e.target.result; // Guarda la imagen en base64 en el input hidden
        };
        reader.readAsDataURL(file);
    }
});


</script>


<?php
require_once('ConectaDB.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $nombre = htmlspecialchars($_POST['nombre']);
    $apellido = htmlspecialchars($_POST['apellido']);
    $bio = htmlspecialchars($_POST['bio']);
    $ubicacion = htmlspecialchars($_POST['ubicacion']);
    $fecha_nacimiento = htmlspecialchars($_POST['fecha_nacimiento']);
    $iduser = $usuario['iduser'];
    
    // Comprobar si hay una nueva imagen de perfil en base64
        $image_base64 = !empty($_POST['image_base64']) ?  $_POST['image_base64'] : $usuario['profile_image'];
        $banner_base64 = !empty($_POST['banner_base64']) ?  $_POST['banner_base64'] : $usuario['banner'];
 
        if (!empty($_POST['image_base64']) && strpos($_POST['image_base64'], 'data:image') === false) {
            $image_base64 = 'data:image/jpeg;base64,' . $_POST['image_base64']; 
        }

        if (!empty($_POST['banner_base64']) && strpos($_POST['banner_base64'], 'data:image') === false) {
            $banner_base64 = 'data:image/jpeg;base64,' . $_POST['banner_base64']; 
        }


    // Actualizar todos los campos, incluyendo el banner
    $stmt = $db->prepare("UPDATE users SET username = ?, userFirstName = ?, userLastName = ?, bio = ?, location = ?, age = ?, profile_image = ?, banner = ? WHERE iduser = ?");
    $stmt->execute([$username, $nombre, $apellido, $bio, $ubicacion, $fecha_nacimiento, $image_base64, $banner_base64, $iduser]);
    
    
    echo "<meta http-equiv='refresh' content='0'>";
   
    
}
?>



</body>
</html>
