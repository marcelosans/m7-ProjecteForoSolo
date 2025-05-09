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

// Configuración de paginación
$hilosPorPagina = 10; // Número de hilos por página
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($paginaActual < 1) $paginaActual = 1;

// Calcular el offset para la consulta SQL
$offset = ($paginaActual - 1) * $hilosPorPagina;

// Obtener el número total de hilos para calcular las páginas
$sqlContarHilos = "SELECT COUNT(*) AS total FROM Hilo WHERE nomVideojoc = :tema";
$prepContar = $db->prepare($sqlContarHilos);
$prepContar->bindParam(':tema', $tema);
$prepContar->execute();
$totalHilos = $prepContar->fetch(PDO::FETCH_ASSOC)['total'];

// Calcular el número total de páginas
$totalPaginas = ceil($totalHilos / $hilosPorPagina);
if ($paginaActual > $totalPaginas && $totalPaginas > 0) {
    // Si la página actual es mayor que el total, redirigir a la última página
    header("Location: hilos-page.php?tema=" . urlencode($tema) . "&pagina=$totalPaginas");
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
    <div class="container-hilo">
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
    <div class="container-hilo main-container">
        <div class="content-layout">
            <div class="hilos-container">
                <div class="hilos-header">
                    <h2>Hilos de <?= htmlspecialchars($tema) ?></h2>
                    <div class="hilos-controls">
                        <button class="btn-new-hilo"> <a href="<?php echo 'crearHilo.php?tema=' . urlencode($_GET['tema']); ?>"> Crear Nuevo Hilo</a> </button>
                    </div>
                </div>

                <?php
                // Obtener todos los hilos del tema con paginación
                $sqlHilos = "SELECT H.*, U.username, U.profile_image, 
                            (SELECT COUNT(*) FROM Publicacio WHERE idHilo = H.idHilo) AS numRespuestas,
                            (SELECT MAX(dataPub) FROM Publicacio WHERE idHilo = H.idHilo) AS ultimaActividad
                            FROM Hilo H
                            JOIN Users U ON H.iduser = U.iduser
                            WHERE H.nomVideojoc = :tema
                            ORDER BY ultimaActividad DESC
                            LIMIT :offset, :limit";
                            
                $prepHilos = $db->prepare($sqlHilos);
                $prepHilos->bindParam(':tema', $tema);
                $prepHilos->bindParam(':offset', $offset, PDO::PARAM_INT);
                $prepHilos->bindParam(':limit', $hilosPorPagina, PDO::PARAM_INT);
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
                
                <!-- Paginación dinámica -->
                <?php if ($totalPaginas > 1): ?>
                <div class="pagination">
                    <?php if ($paginaActual > 1): ?>
                    <a href="?tema=<?= urlencode($tema) ?>&pagina=<?= $paginaActual - 1 ?>" class="page-link">Anterior</a>
                    <?php endif; ?>
                    
                    <?php
                    // Determinar qué páginas mostrar
                    $mostrarPaginas = 5; // Número de páginas a mostrar en la barra
                    $startPage = max(1, min($paginaActual - floor($mostrarPaginas / 2), $totalPaginas - $mostrarPaginas + 1));
                    $endPage = min($startPage + $mostrarPaginas - 1, $totalPaginas);
                    
                    // Asegurarse de que se muestran al menos $mostrarPaginas o todas si hay menos
                    if ($endPage - $startPage + 1 < $mostrarPaginas && $startPage > 1) {
                        $startPage = max(1, $endPage - $mostrarPaginas + 1);
                    }
                    
                    if ($startPage > 1): ?>
                    <a href="?tema=<?= urlencode($tema) ?>&pagina=1" class="page-link">1</a>
                    <?php 
                    if ($startPage > 2): 
                    ?>
                    <span class="page-dots">...</span>
                    <?php 
                    endif;
                    endif; 
                    
                    for ($i = $startPage; $i <= $endPage; $i++): 
                    ?>
                    <a href="?tema=<?= urlencode($tema) ?>&pagina=<?= $i ?>" class="page-link <?= $i == $paginaActual ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; 
                    
                    // Mostrar enlace a la última página si no es la actual
                    if ($endPage < $totalPaginas): 
                    ?>
                    <span class="page-dots">...</span>
                    <a href="?tema=<?= urlencode($tema) ?>&pagina=<?= $totalPaginas ?>" class="page-link"><?= $totalPaginas ?></a>
                    <?php endif; ?>
                    
                    <?php if ($paginaActual < $totalPaginas): ?>
                    <a href="?tema=<?= urlencode($tema) ?>&pagina=<?= $paginaActual + 1 ?>" class="page-link next">Siguiente</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
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
                    <button class="btn-new-topic">
                    <a href="<?php echo 'crearHilo.php?tema=' . urlencode($_GET['tema']); ?>"> Crear Nuevo Hilo</a>
                   </button>
                </div>
            </aside>
        </div>
    </div>
</main>

<script src="../Js/Loading.js"></script>
<script src="../Js/NavBar.js"></script>
<script src="../Js/hilos.js"></script>
</body>
</html>