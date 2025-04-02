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

// Configuración de paginación
$temasPorPagina = 10; // Número de temas por página
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($paginaActual < 1) $paginaActual = 1;

// Calcular el offset para la consulta SQL
$offset = ($paginaActual - 1) * $temasPorPagina;

// Obtener el número total de temas para calcular las páginas
$sqlContarTemas = "SELECT COUNT(*) AS total FROM Tema";
$prepContar = $db->prepare($sqlContarTemas);
$prepContar->execute();
$totalTemas = $prepContar->fetch(PDO::FETCH_ASSOC)['total'];

// Calcular el número total de páginas
$totalPaginas = ceil($totalTemas / $temasPorPagina);
if ($paginaActual > $totalPaginas && $totalPaginas > 0) {
    // Si la página actual es mayor que el total, redirigir a la última página
    header("Location: Temas.php?pagina=$totalPaginas");
    exit;
}

// Obtener los temas con paginación
$sql = 'SELECT * FROM `Tema`
        LIMIT :offset, :limit';
$preparada = $db->prepare($sql);
$preparada->bindParam(':offset', $offset, PDO::PARAM_INT);
$preparada->bindParam(':limit', $temasPorPagina, PDO::PARAM_INT);
$preparada->execute();
$temas = $preparada->fetchAll(PDO::FETCH_ASSOC);

$sqlRanking = 'SELECT 
                t.nomVideojoc AS nomVideojoc, 
                COUNT(h.idHilo) AS numHilos
            FROM 
                Tema t
            LEFT JOIN 
                Hilo h ON t.nomVideojoc = h.nomVideojoc
            GROUP BY 
                t.nomVideojoc
            ORDER BY 
                numHilos DESC
            LIMIT 5';
$preparadaRanking = $db->prepare($sqlRanking);
$preparadaRanking->execute();
$ranking = $preparadaRanking->fetchAll(PDO::FETCH_ASSOC);

$sqlNuevo = 'SELECT 
    nomVideojoc, 
     dataPub
 FROM 
     Tema
 ORDER BY 
     dataPub DESC
 LIMIT 3';
$preparadaNuevo = $db->prepare($sqlNuevo);  // Corregido de $preparadaNuevo = $db->prepare($sqlRanking);
$preparadaNuevo->execute();
$nuevoJuego = $preparadaNuevo->fetchAll(PDO::FETCH_ASSOC);

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
    
    <div id="loader" class="loader-overlay">
        <div>
            <img id="loading-gif" src="../Recursos/img/loading.gif" alt="Cargando..." class="loading-gif">
            <div class="loading-text">CARGANDO...</div>
        </div>
    </div>
    
    <header class="navbar">
        <div class="container">
            <a href="HomePage.php" class="logo">
                <img src="../Recursos/img/logo-forosolo.png" alt="Logo">
            </a>
            <div id="content">
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
    
    <main class="page-content">
        <div class="container-tema main-container">
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
                                        <span class="stat-value">
                                            <?php 
                                                  $sqlHilosCount = "SELECT COUNT(*) AS totalHilos FROM Hilo WHERE nomVideojoc = :tema";
                                                  $prepHilos = $db->prepare($sqlHilosCount);
                                                  $prepHilos->bindParam(':tema', $tema['nomVideojoc'], PDO::PARAM_STR);
                                                  $prepHilos->execute();
                                                  $hilosResult = $prepHilos->fetch(PDO::FETCH_ASSOC);
                                                  echo "Hilos: " . ($hilosResult['totalHilos'] ?? 0);
                                            ?>
                                        </span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-icon">💬</span>
                                        <span class="stat-value">
                                            <?php
                                            $numPublicaciones = "SELECT COUNT(P.idPublicacio) AS totalPublicaciones FROM Tema T LEFT JOIN Hilo H ON T.nomVideojoc = H.nomVideojoc LEFT JOIN Publicacio P ON H.idHilo = P.idHilo WHERE T.nomVideojoc = :nomVideojoc";
                                            $preparada = $db->prepare($numPublicaciones);
                                            $preparada->bindParam(':nomVideojoc', $tema['nomVideojoc'], PDO::PARAM_STR);
                                            $preparada->execute();
                                            $resultado = $preparada->fetch(PDO::FETCH_ASSOC);
                                            echo "Publicaciones: " . ($resultado['totalPublicaciones'] ?? 0);
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <a href="<?php echo '../PHP/hilos-page.php?tema='.  urlencode($tema['nomVideojoc']) ?>" class="btn-ver-tema">Ver tema</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                

                <aside class="sidebar">
                    <div class="sidebar-box">
                        <h3>Temas Populares</h3>
                        <ul class="popular-themes">
                            <?php  $i = 0; ?>
                            <?php foreach($ranking as $rango):
                                ?>
                            <li><span class="rank"><?php echo $i+1 ?></span><a href="<?php echo '../PHP/hilos-page.php?tema='.  urlencode($rango['nomVideojoc']) ?>"><?php echo $rango['nomVideojoc'] ?></a><span class="views"><?php echo $rango['numHilos'] ?></span></li>
                            <?php
                            $i++;
                        endforeach;?>
                        </ul>
                    </div>
                    <div class="sidebar-box">
                        <h3>Temas Nuevos</h3>
                        <ul class="new-themes">
                            <?php foreach($nuevoJuego as $game): ?>
                            <li><a href="<?php echo '../PHP/hilos-page.php?tema='.  urlencode($game['nomVideojoc']) ?>"><?php echo $game['nomVideojoc']  ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </aside>
                 
            </div>
        </div>

        <!-- Paginación dinámica -->
        <?php if ($totalPaginas > 1): ?>
        <div class="pagination">
            <?php if ($paginaActual > 1): ?>
            <a href="?pagina=<?= $paginaActual - 1 ?>" class="page-link">Anterior</a>
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
            
            // Mostrar enlace a la primera página si no es la actual
            if ($startPage > 1): ?>
            <a href="?pagina=1" class="page-link">1</a>
            <?php 
            if ($startPage > 2): 
            ?>
            <span class="page-dots">...</span>
            <?php 
            endif;
            endif; 
            
            // Mostrar las páginas numeradas
            for ($i = $startPage; $i <= $endPage; $i++): 
            ?>
            <a href="?pagina=<?= $i ?>" class="page-link <?= $i == $paginaActual ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; 
            
            // Mostrar enlace a la última página si no es la actual
            if ($endPage < $totalPaginas): 
            ?>
            <span class="page-dots">...</span>
            <a href="?pagina=<?= $totalPaginas ?>" class="page-link"><?= $totalPaginas ?></a>
            <?php endif; ?>
            
            <?php if ($paginaActual < $totalPaginas): ?>
            <a href="?pagina=<?= $paginaActual + 1 ?>" class="page-link next">Siguiente</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </main>
    
    <script src="../Js/NavBar.js"></script>
    <script src="../Js/Loading.js"></script>
</body>
</html>