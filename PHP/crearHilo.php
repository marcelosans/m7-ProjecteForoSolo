<?php
session_start(); 
if (!isset($_SESSION['email'])) {
    header('Location: Login.php'); 
    exit;
}

require_once('ConectaDB.php');

// Obtener información del usuario
$sql = 'SELECT * FROM `users` WHERE mail = :email';
$preparada = $db->prepare($sql);
$preparada->bindParam(':email', $_SESSION['email']);
$preparada->execute();
$usuario = $preparada->fetch(PDO::FETCH_ASSOC);

// Verificar si se ha pasado un tema en la URL
if (isset($_GET['tema']) && !empty($_GET['tema'])) {
    $tema = $_GET['tema'];
    
    // Obtener detalles del tema
    $sqlTema = 'SELECT * FROM `Tema` WHERE nomVideojoc = :tema';
    $preparadaTema = $db->prepare($sqlTema);
    $preparadaTema->bindParam(':tema', $tema);
    $preparadaTema->execute();
    $temaDetalle = $preparadaTema->fetch(PDO::FETCH_ASSOC);
    
    if (!$temaDetalle) {
        // Si no existe el tema, redirigir a la página de temas
        header('Location: Temas.php');
        exit;
    }
} else {
    // Si no se especificó un tema, redirigir a la página de temas
    header('Location: Temas.php');
    exit;
}

// Procesar el formulario
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    
    // Validar campos
    if (empty($titulo)) {
        $error = "Por favor, introduce un título para el hilo.";
    } elseif (empty($contenido)) {
        $error = "Por favor, escribe un mensaje para iniciar el hilo.";
    } elseif (strlen($titulo) > 100) {
        $error = "El título no puede superar los 100 caracteres.";
    } elseif (strlen($contenido) > 500) {
        $error = "El mensaje no puede superar los 500 caracteres.";
    } else {
        try {
            // Iniciar transacción
            $db->beginTransaction();
            
            // Insertar el hilo
            $sqlHilo = "INSERT INTO Hilo (Titol, nomVideojoc, iduser) VALUES (:titulo, :tema, :iduser)";
            $prepHilo = $db->prepare($sqlHilo);
            $prepHilo->bindParam(':titulo', $titulo);
            $prepHilo->bindParam(':tema', $tema);
            $prepHilo->bindParam(':iduser', $usuario['iduser']);
            $prepHilo->execute();
            
            // Obtener el ID del hilo recién creado
            $idHilo = $db->lastInsertId();
            
            // Insertar la primera publicación
            $sqlPublicacion = "INSERT INTO Publicacio (Contingut, dataPub, idHilo, iduser) VALUES (:contenido, NOW(), :idHilo, :iduser)";
            $prepPublicacion = $db->prepare($sqlPublicacion);
            $prepPublicacion->bindParam(':contenido', $contenido);
            $prepPublicacion->bindParam(':idHilo', $idHilo);
            $prepPublicacion->bindParam(':iduser', $usuario['iduser']);
            $prepPublicacion->execute();
            
            // Confirmar transacción
            $db->commit();
            
            // Redirigir a la página del hilo creado
            header("Location: publicaciones-page.php?hilo=" . $idHilo);
            exit;
            
        } catch (PDOException $e) {
            // Revertir la transacción en caso de error
            $db->rollBack();
            $error = "Ha ocurrido un error al crear el hilo. Por favor, inténtalo de nuevo.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forosolo || Crear Hilo - <?= htmlspecialchars($tema) ?></title>
    <link rel="stylesheet" href="../CSS/Navbar.css">
    <link rel="stylesheet" href="../CSS/Loading.css">
    <link rel="stylesheet" href="../CSS/crear-hilo.css">
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
                <a href="HomePage.php">Inicio</a>
                <a href="./Temas.php">Temas</a>

                <!-- Dropdown Perfil -->
                <div class="profile">
                    <img src="<?= !empty($usuario['profile_image']) ? $usuario['profile_image'] : '../profile/profile.png' ?>" alt="Imagen de perfil">
                    <ul class="dropdown">
                        <li><a href="./Perfil.php">Mi Perfil</a></li>
                        <li><a href="cerrarSesion.php">Cerrar Sesión</a></li>
                    </ul>
                </div>
            </nav>

            <!-- Botón Menú Móvil -->
            <div class="menu-toggle">&#9776;</div>
        </div>
    </div>
</header>

<!-- Menú Móvil -->
<div class="mobile-menu">
    <button class="close-menu">&times;</button>
    <div class="mobile-content">
        <img src="../Recursos/img/logo-forosolo.png" alt="Logo" class="mobile-logo">
        <a href="HomePage.php">Inicio</a>
        <a href="./Temas.php">Temas</a>

        <!-- Dropdown en móvil -->
        <div class="mobile-profile">
            <img src="<?= !empty($usuario['profile_image']) ? $usuario['profile_image'] : '../profile/profile.png' ?>" alt="Imagen de perfil">
            <a href="./Perfil.php">Mi Perfil</a>
            <a href="cerrarSesion.php">Cerrar Sesión</a>
        </div>
    </div>
</div>

<div id="loader" class="loader-overlay">
    <div>
        <img id="loading-gif" src="" alt="Cargando..." class="loading-gif">
        <div class="loading-text">CARGANDO...</div>
    </div>
</div>

<div class="tema-banner">
    <div class="container">
        <div class="tema-header">
            <div class="tema-image">
                <img src="<?= htmlspecialchars($temaDetalle['imagen'] ?? '../Recursos/img/default-game.jpg') ?>" alt="<?= htmlspecialchars($tema) ?>">
            </div>
            <div class="tema-info">
                <h1><?= htmlspecialchars($tema) ?></h1>
                <div class="breadcrumbs">
                    <a href="Temas.php">Temas</a> &gt;
                    <a href="hilos-page.php?tema=<?= urlencode($tema) ?>"><?= htmlspecialchars($tema) ?></a> &gt;
                    <span>Crear Hilo</span>
                </div>
            </div>
        </div>
    </div>
</div>

<main class="page-content">
    <div class="container main-container">
        <div class="crear-hilo-container">
            <div class="crear-hilo-header">
                <h2>Crear Nuevo Hilo en <?= htmlspecialchars($tema) ?></h2>
            </div>
            
            <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <p><?= htmlspecialchars($error) ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <p><?= htmlspecialchars($success) ?></p>
            </div>
            <?php endif; ?>
            
            <form method="post" class="crear-hilo-form">
                <div class="form-group">
                    <label for="titulo">Título del Hilo</label>
                    <input type="text" id="titulo" name="titulo" maxlength="100" placeholder="Escribe un título descriptivo" value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>" required>
                    <div class="char-counter">
                        <span id="titulo-count">0</span>/100
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="contenido">Tu Mensaje</label>
                    <textarea id="contenido" name="contenido" rows="8" maxlength="500" placeholder="Escribe aquí tu mensaje inicial. Recuerda ser respetuoso y seguir las normas del foro." required><?= htmlspecialchars($_POST['contenido'] ?? '') ?></textarea>
                    <div class="char-counter">
                        <span id="contenido-count">0</span>/500
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="hilos-page.php?tema=<?= urlencode($tema) ?>" class="btn-cancel">Cancelar</a>
                    <button type="submit" class="btn-submit">Crear Hilo</button>
                </div>
            </form>
            
            <div class="normas-hilo">
                <h3>Normas para crear hilos</h3>
                <ul>
                    <li>Escribe un título claro y descriptivo.</li>
                    <li>Evita crear hilos duplicados, busca antes si ya existe uno similar.</li>
                    <li>Mantén el respeto hacia todos los usuarios.</li>
                    <li>No compartas información personal o sensible.</li>
                    <li>Escribe en el tema adecuado. Este hilo se creará en "<?= htmlspecialchars($tema) ?>".</li>
                </ul>
            </div>
        </div>
    </div>
</main>

<script src="../Js/Loading.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Menú móvil
        const menuToggle = document.querySelector(".menu-toggle");
        const mobileMenu = document.querySelector(".mobile-menu");
        const closeMenu = document.querySelector(".close-menu");

        menuToggle.addEventListener("click", function () {
            mobileMenu.classList.add("active");
        });

        closeMenu.addEventListener("click", function () {
            mobileMenu.classList.remove("active");
        });
        
        // Contadores de caracteres
        const tituloInput = document.getElementById("titulo");
        const contenidoTextarea = document.getElementById("contenido");
        const tituloCount = document.getElementById("titulo-count");
        const contenidoCount = document.getElementById("contenido-count");
        
        // Inicializar contadores
        tituloCount.textContent = tituloInput.value.length;
        contenidoCount.textContent = contenidoTextarea.value.length;
        
        // Actualizar contadores al escribir
        tituloInput.addEventListener("input", function() {
            tituloCount.textContent = this.value.length;
            if (this.value.length >= 90) {
                tituloCount.classList.add("warning");
            } else {
                tituloCount.classList.remove("warning");
            }
        });
        
        contenidoTextarea.addEventListener("input", function() {
            contenidoCount.textContent = this.value.length;
            if (this.value.length >= 450) {
                contenidoCount.classList.add("warning");
            } else {
                contenidoCount.classList.remove("warning");
            }
        });
    });
</script>

</body>
</html>