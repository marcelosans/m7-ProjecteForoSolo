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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forosolo || Hilos - <?= htmlspecialchars($tema) ?></title>
    <link rel="stylesheet" href="../CSS/Navbar.css">
    <link rel="stylesheet" href="../CSS/hilos.css">
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

<div class="tema-banner">
    <div class="container">
        <div class="tema-header">
            <div class="tema-image">
                <img src="<?= htmlspecialchars($temaDetalle['imagen']) ?>" alt="<?= htmlspecialchars($tema) ?>">
            </div>
            <div class="tema-info">
                <h1><?= htmlspecialchars($tema) ?></h1>
                <div class="tema-stats">
                    <?php
                    // Contar hilos del tema
                    $sqlHilosCount = "SELECT COUNT(*) AS totalHilos FROM Hilo WHERE nomVideojoc = :tema";
                    $prepHilosCount = $db->prepare($sqlHilosCount);
                    $prepHilosCount->bindParam(':tema', $tema);
                    $prepHilosCount->execute();
                    $hilosCount = $prepHilosCount->fetch(PDO::FETCH_ASSOC);
                    
                    // Contar publicaciones del tema
                    $sqlPubCount = "SELECT COUNT(P.idPublicacio) AS totalPublicaciones
                                    FROM Tema T
                                    LEFT JOIN Hilo H ON T.nomVideojoc = H.nomVideojoc
                                    LEFT JOIN Publicacio P ON H.idHilo = P.idHilo
                                    WHERE T.nomVideojoc = :tema";
                    $prepPubCount = $db->prepare($sqlPubCount);
                    $prepPubCount->bindParam(':tema', $tema);
                    $prepPubCount->execute();
                    $pubCount = $prepPubCount->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <div class="stat">
                        <span class="stat-icon">📝</span>
                        <span class="stat-value"><?= $hilosCount['totalHilos'] ?> hilos</span>
                    </div>
                    <div class="stat">
                        <span class="stat-icon">💬</span>
                        <span class="stat-value"><?= $pubCount['totalPublicaciones'] ?> publicaciones</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<main class="page-content">
    <div class="container main-container">
        <div class="content-layout">
            <div class="hilos-container">
                <div class="hilos-header">
                    <h2>Hilos de <?= htmlspecialchars($tema) ?></h2>
                    <div class="hilos-controls">
                        <button class="btn-new-hilo">Nuevo Hilo</button>
                    </div>
                </div>

                <?php
                // Obtener todos los hilos del tema
                $sqlHilos = "SELECT H.*, U.username, U.profile_image, 
                            (SELECT COUNT(*) FROM Publicacio WHERE idHilo = H.idHilo) AS numRespuestas,
                            (SELECT MAX(dataPub) FROM Publicacio WHERE idHilo = H.idHilo) AS ultimaActividad
                            FROM Hilo H
                            JOIN Users U ON H.iduser = U.iduser
                            WHERE H.nomVideojoc = :tema
                            ORDER BY ultimaActividad DESC";
                $prepHilos = $db->prepare($sqlHilos);
                $prepHilos->bindParam(':tema', $tema);
                $prepHilos->execute();
                $hilos = $prepHilos->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($hilos) > 0):
                    foreach ($hilos as $hilo):
                ?>
                <div class="hilo">
                    <div class="hilo-user">
                        <img src="<?= !empty($hilo['profile_image']) ? $hilo['profile_image'] : '../profile/profile.png' ?>" alt="Avatar">
                        <span class="username"><?= htmlspecialchars($hilo['username']) ?></span>
                    </div>
                    <div class="hilo-content">
                        <h3><a href="publicaciones-page.php?hilo=<?= $hilo['idHilo'] ?>"><?= htmlspecialchars($hilo['Titol']) ?></a></h3>
                        <div class="hilo-stats">
                            <div class="stat">
                                <span class="stat-icon">💬</span>
                                <span class="stat-value"><?= $hilo['numRespuestas'] ?> respuestas</span>
                            </div>
                            <div class="stat">
                                <span class="stat-icon">🕒</span>
                                <span class="stat-value">Última actividad: <?= $hilo['ultimaActividad'] ?? 'No hay actividad' ?></span>
                            </div>
                        </div>
                    </div>
                    <a href="publicaciones-page.php?hilo=<?= $hilo['idHilo'] ?>" class="btn-ver-hilo">Ver hilo</a>
                </div>
                <?php 
                    endforeach;
                else:
                ?>
                <div class="no-hilos">
                    <p>No hay hilos disponibles para este tema. ¡Sé el primero en crear uno!</p>
                </div>
                <?php endif; ?>
                
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
                    <h3>Hilos Populares</h3>
                    <ul class="popular-hilos">
                        <?php
                        // Obtener hilos populares del tema (los que tienen más publicaciones)
                        $sqlPopulares = "SELECT H.idHilo, H.Titol, COUNT(P.idPublicacio) AS numPublicaciones
                                        FROM Hilo H
                                        LEFT JOIN Publicacio P ON H.idHilo = P.idHilo
                                        WHERE H.nomVideojoc = :tema
                                        GROUP BY H.idHilo
                                        ORDER BY numPublicaciones DESC
                                        LIMIT 5";
                        $prepPopulares = $db->prepare($sqlPopulares);
                        $prepPopulares->bindParam(':tema', $tema);
                        $prepPopulares->execute();
                        $hilosPopulares = $prepPopulares->fetchAll(PDO::FETCH_ASSOC);
                        
                        $contador = 1;
                        foreach ($hilosPopulares as $hilo):
                        ?>
                        <li>
                            <span class="rank"><?= $contador++ ?></span>
                            <a href="publicaciones-page.php?hilo=<?= $hilo['idHilo'] ?>"><?= htmlspecialchars($hilo['Titol']) ?></a>
                            <span class="views"><?= $hilo['numPublicaciones'] ?> respuestas</span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="sidebar-box">
                    <h3>Hilos Nuevos</h3>
                    <ul class="new-hilos">
                        <?php
                        // Obtener hilos más recientes
                        $sqlNuevos = "SELECT H.idHilo, H.Titol
                                      FROM Hilo H
                                      WHERE H.nomVideojoc = :tema
                                      ORDER BY H.idHilo DESC
                                      LIMIT 3";
                        $prepNuevos = $db->prepare($sqlNuevos);
                        $prepNuevos->bindParam(':tema', $tema);
                        $prepNuevos->execute();
                        $hilosNuevos = $prepNuevos->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($hilosNuevos as $hilo):
                        ?>
                        <li><a href="publicaciones-page.php?hilo=<?= $hilo['idHilo'] ?>"><?= htmlspecialchars($hilo['Titol']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="sidebar-box cta-box">
                    <p>¿Tienes algo que compartir?</p>
                    <button class="btn-new-topic">Crear Nuevo Hilo</button>
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
        
        // Botones para crear nuevo hilo
        const btnNewHilo = document.querySelector(".btn-new-hilo");
        const btnNewTopic = document.querySelector(".btn-new-topic");
        
        btnNewHilo.addEventListener("click", function() {
            window.location.href = "CrearHilo.php?tema=<?= urlencode($tema) ?>";
        });
        
        btnNewTopic.addEventListener("click", function() {
            window.location.href = "CrearHilo.php?tema=<?= urlencode($tema) ?>";
        });
    });
</script>

</body>
</html>
