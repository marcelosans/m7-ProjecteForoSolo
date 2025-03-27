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

// Verificar si se ha pasado un hilo en la URL
if (isset($_GET['hilo']) && !empty($_GET['hilo'])) {
    $idHilo = $_GET['hilo'];
    
    // Obtener detalles del hilo
    $sqlHilo = 'SELECT H.*, T.nomVideojoc, T.imagen, U.username as creador 
                FROM Hilo H 
                JOIN Tema T ON H.nomVideojoc = T.nomVideojoc 
                JOIN Users U ON H.iduser = U.iduser 
                WHERE H.idHilo = :idHilo';
    $preparadaHilo = $db->prepare($sqlHilo);
    $preparadaHilo->bindParam(':idHilo', $idHilo);
    $preparadaHilo->execute();
    $hiloDetalle = $preparadaHilo->fetch(PDO::FETCH_ASSOC);
    
    if (!$hiloDetalle) {
        // Si no existe el hilo, redirigir a la página de temas
        header('Location: Temas.php');
        exit;
    }
} else {
    // Si no se especificó un hilo, redirigir a la página de temas
    header('Location: Temas.php');
    exit;
}

// Procesar el envío de nueva publicación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publicar'])) {
    $contenido = trim($_POST['contenido']);
    
    if (!empty($contenido)) {
        $sqlInsert = "INSERT INTO Publicacio (Contingut, dataPub, idHilo, iduser) 
                     VALUES (:contenido, CURDATE(), :idHilo, :iduser)";
        $prepInsert = $db->prepare($sqlInsert);
        $prepInsert->bindParam(':contenido', $contenido);
        $prepInsert->bindParam(':idHilo', $idHilo);
        $prepInsert->bindParam(':iduser', $usuario['iduser']);
        
        if ($prepInsert->execute()) {
            // Redirigir para evitar reenvío de formulario
            header("Location: Publicaciones.php?hilo=$idHilo");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forosolo || <?= htmlspecialchars($hiloDetalle['Titol']) ?></title>
    <link rel="stylesheet" href="../CSS/Navbar.css">
    <link rel="stylesheet" href="../CSS/publicaciones-css.css">
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

<div class="breadcrumbs">
    <div class="container">
        <a href="Temas.php">Temas</a> &gt; 
        <a href="Hilos.php?tema=<?= urlencode($hiloDetalle['nomVideojoc']) ?>"><?= htmlspecialchars($hiloDetalle['nomVideojoc']) ?></a> &gt; 
        <span><?= htmlspecialchars($hiloDetalle['Titol']) ?></span>
    </div>
</div>

<main class="page-content">
    <div class="container main-container">
        <div class="content-layout">
            <div class="publicaciones-container">
                <div class="hilo-header">
                    <h1><?= htmlspecialchars($hiloDetalle['Titol']) ?></h1>
                    <div class="hilo-meta">
                        <div class="tema-badge">
                            <img src="<?= htmlspecialchars($hiloDetalle['imagen']) ?>" alt="<?= htmlspecialchars($hiloDetalle['nomVideojoc']) ?>">
                            <span><?= htmlspecialchars($hiloDetalle['nomVideojoc']) ?></span>
                        </div>
                        <div class="creador">
                            Creado por: <strong><?= htmlspecialchars($hiloDetalle['creador']) ?></strong>
                        </div>
                    </div>
                </div>

                <?php
                // Obtener todas las publicaciones del hilo
                $sqlPublicaciones = "SELECT P.*, U.username, U.profile_image 
                                    FROM Publicacio P
                                    JOIN Users U ON P.iduser = U.iduser
                                    WHERE P.idHilo = :idHilo
                                    ORDER BY P.dataPub ASC, P.idPublicacio ASC";
                $prepPublicaciones = $db->prepare($sqlPublicaciones);
                $prepPublicaciones->bindParam(':idHilo', $idHilo);
                $prepPublicaciones->execute();
                $publicaciones = $prepPublicaciones->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($publicaciones) > 0):
                    foreach ($publicaciones as $index => $publicacion):
                ?>
                <div class="publicacion <?= $index === 0 ? 'publicacion-original' : '' ?>">
                    <div class="publicacion-sidebar">
                        <div class="usuario-info">
                            <img src="<?= !empty($publicacion['profile_image']) ? $publicacion['profile_image'] : '../profile/profile.png' ?>" alt="Avatar" class="usuario-avatar">
                            <div class="usuario-nombre"><?= htmlspecialchars($publicacion['username']) ?></div>
                            <?php if ($index === 0): ?>
                                <div class="badge-op">OP</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="publicacion-content">
                        <div class="publicacion-header">
                            <div class="publicacion-fecha">
                                <?= date('d/m/Y', strtotime($publicacion['dataPub'])) ?>
                            </div>
                            <div class="publicacion-numero">#<?= $index + 1 ?></div>
                        </div>
                        <div class="publicacion-texto">
                            <?= nl2br(htmlspecialchars($publicacion['Contingut'])) ?>
                        </div>
                        <div class="publicacion-footer">
                            <button class="btn-citar">Citar</button>
                            <?php if ($publicacion['iduser'] == $usuario['iduser']): ?>
                                <button class="btn-editar">Editar</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php 
                    endforeach;
                else:
                ?>
                <div class="no-publicaciones">
                    <p>No hay publicaciones en este hilo. ¡Sé el primero en responder!</p>
                </div>
                <?php endif; ?>
                
                <!-- Formulario para agregar nueva publicación -->
                <div class="nueva-publicacion">
                    <h3>Responder</h3>
                    <form action="" method="POST">
                        <textarea name="contenido" rows="5" placeholder="Escribe tu respuesta..." required></textarea>
                        <button type="submit" name="publicar" class="btn-publicar">Publicar respuesta</button>
                    </form>
                </div>
                
                <!-- Paginación -->
                <div class="pagination">
                    <a href="#" class="page-link active">1</a>
                    <a href="#" class="page-link">2</a>
                    <a href="#" class="page-link">3</a>
                    <span class="page-dots">...</span>
                    <a href="#" class="page-link">10</a>
                    <a href="#" class="page-link next">Siguiente</a>
                </div>
            </div>

            <aside class="sidebar">
                <div class="sidebar-box">
                    <h3>Hilos Relacionados</h3>
                    <ul class="related-hilos">
                        <?php
                        // Obtener hilos relacionados del mismo tema
                        $sqlRelacionados = "SELECT H.idHilo, H.Titol
                                          FROM Hilo H
                                          WHERE H.nomVideojoc = :nomVideojoc
                                          AND H.idHilo != :idHilo
                                          ORDER BY RAND()
                                          LIMIT 5";
                        $prepRelacionados = $db->prepare($sqlRelacionados);
                        $prepRelacionados->bindParam(':nomVideojoc', $hiloDetalle['nomVideojoc']);
                        $prepRelacionados->bindParam(':idHilo', $idHilo);
                        $prepRelacionados->execute();
                        $hilosRelacionados = $prepRelacionados->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($hilosRelacionados as $hilo):
                        ?>
                        <li><a href="Publicaciones.php?hilo=<?= $hilo['idHilo'] ?>"><?= htmlspecialchars($hilo['Titol']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="sidebar-box">
                    <h3>Usuarios Activos</h3>
                    <ul class="active-users">
                        <?php
                        // Obtener usuarios que han participado en el hilo
                        $sqlUsuarios = "SELECT DISTINCT U.iduser, U.username, U.profile_image
                                      FROM Publicacio P
                                      JOIN Users U ON P.iduser = U.iduser
                                      WHERE P.idHilo = :idHilo
                                      LIMIT 5";
                        $prepUsuarios = $db->prepare($sqlUsuarios);
                        $prepUsuarios->bindParam(':idHilo', $idHilo);
                        $prepUsuarios->execute();
                        $usuariosActivos = $prepUsuarios->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($usuariosActivos as $user):
                        ?>
                        <li>
                            <img src="<?= !empty($user['profile_image']) ? $user['profile_image'] : '../profile/profile.png' ?>" alt="<?= htmlspecialchars($user['username']) ?>">
                            <span><?= htmlspecialchars($user['username']) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="sidebar-box cta-box">
                    <p>¿Te interesa este tema?</p>
                    <a href="Hilos.php?tema=<?= urlencode($hiloDetalle['nomVideojoc']) ?>" class="btn-all-hilos">Ver todos los hilos</a>
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
        
        // Funcionalidad para citar publicaciones
        const btnCitar = document.querySelectorAll(".btn-citar");
        const textarea = document.querySelector("textarea[name='contenido']");
        
        btnCitar.forEach((btn, index) => {
            btn.addEventListener("click", function() {
                const publicacionTexto = document.querySelectorAll(".publicacion-texto")[index].textContent.trim();
                const username = document.querySelectorAll(".usuario-nombre")[index].textContent.trim();
                
                const cita = `[Cita de ${username}]\n${publicacionTexto}\n[Fin de cita]\n\n`;
                
                textarea.value += cita;
                textarea.focus();
                textarea.scrollIntoView({ behavior: 'smooth' });
            });
        });
    });
</script>

</body>
</html>
