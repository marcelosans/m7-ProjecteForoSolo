<?php
session_start(); 
if (!isset($_SESSION['email'])) {
    header('Location: Login.php'); 
    exit;
}
require_once('ConectaDB.php');
require_once('publicacionesDB.php');
require_once('paginacionPublicaciones.php');

// Obtener datos del usuario actual
$sql = 'SELECT * FROM `users` WHERE mail = :email ';
$preparada = $db->prepare($sql);
$preparada->bindParam(':email', $_SESSION['email']);
$preparada->execute();
$usuario = $preparada->fetch(PDO::FETCH_ASSOC);

// Verificar si se ha pasado un hilo en la URL
if (isset($_GET['hilo']) && !empty($_GET['hilo'])) {
    $idHilo = $_GET['hilo'];
    
    // Obtener detalles del hilo
    $hiloDetalle = obtenerDetallesHilo($db, $idHilo);
    
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
        if (insertarPublicacion($db, $contenido, $idHilo, $usuario['iduser'])) {
            // Redirigir para evitar reenvío de formulario
            header("Location: publicaciones-page.php?hilo=$idHilo");
            exit;
        }
    }
}
// Procesar eliminación de publicación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_publicacion'])) {
    $idPublicacion = $_POST['id_publicacion'];
    $idHilo = $_POST['id_hilo'];
    
    $resultado = eliminarPublicacion($db, $idPublicacion, $idHilo, $usuario['iduser']);
    
    if ($resultado['success']) {
        if ($resultado['deleted_thread']) {
            // Si se eliminó el hilo completo, redirigir a la página de temas del videojuego
            header("Location: hilos-page.php?tema=" . urlencode($resultado['tema']));
            exit;
        } else {
            // Si solo se eliminó una publicación, redirigir a la misma página
            header("Location: publicaciones-page.php?hilo=$idHilo");
            exit;
        }
    } else {
        // Error al eliminar
        header("Location: publicaciones-page.php?hilo=$idHilo&error=1");
        exit;
    }
}

// Configuración de paginación
$publicacionesPorPagina = 5; // Número de publicaciones por página
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
// Obtener el total de publicaciones y calcular páginas
$totalPublicaciones = contarPublicaciones($db, $idHilo);
$totalPaginas = ceil($totalPublicaciones / $publicacionesPorPagina);
// Validar y ajustar la página actual
$paginaActual = validarPagina($paginaActual, $totalPaginas);
// Calcular el offset para la consulta
$offset = ($paginaActual - 1) * $publicacionesPorPagina;
// Obtener publicaciones para la página actual
$publicaciones = obtenerPublicacionesPaginadas($db, $idHilo, $offset, $publicacionesPorPagina);
// Obtener el ID del primer post para marcar como OP
$primerPostId = obtenerPrimerPostId($db, $idHilo);
// Calcular los enlaces de paginación
$enlacesPaginacion = ($totalPaginas > 0) ? generarEnlacesPaginacion($paginaActual, $totalPaginas) : [];
// Obtener hilos relacionados
$hilosRelacionados = obtenerHilosRelacionados($db, $hiloDetalle['nomVideojoc'], $idHilo);
// Obtener usuarios activos
$usuariosActivos = obtenerUsuariosActivos($db, $idHilo);
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
    <div class="container-publicacion">
        <a href="Temas.php">Temas</a> &gt; 
        <a href="hilos-page.php?tema=<?= urlencode($hiloDetalle['nomVideojoc']) ?>"><?= htmlspecialchars($hiloDetalle['nomVideojoc']) ?></a> &gt; 
        <span><?= htmlspecialchars($hiloDetalle['Titol']) ?></span>
    </div>
</div>

<main class="page-content">
    <div class="container-publicacion main-container">
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

                <?php if (count($publicaciones) > 0): ?>
                    <?php 
                    // Calcular el índice base para la numeración de publicaciones
                    $baseIndex = ($paginaActual - 1) * $publicacionesPorPagina;
                    
                    foreach ($publicaciones as $index => $publicacion):
                        $esOP = ($publicacion['idPublicacio'] == $primerPostId);
                    ?>
                    <div class="publicacion <?= $esOP ? 'publicacion-original' : '' ?>">
                        <div class="publicacion-sidebar">
                            <div class="usuario-info">
                                <img src="<?= !empty($publicacion['profile_image']) ? $publicacion['profile_image'] : '../profile/profile.png' ?>" alt="Avatar" class="usuario-avatar">
                                <div class="usuario-nombre"><?= htmlspecialchars($publicacion['username']) ?></div>
                                <?php if ($esOP): ?>
                                    <div class="badge-op">OP</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="publicacion-content">
                            <div class="publicacion-header">
                                <div class="publicacion-fecha">
                                    <?= date('d/m/Y', strtotime($publicacion['dataPub'])) ?>
                                </div>
                                <div class="publicacion-numero">#<?= $baseIndex + $index + 1 ?></div>
                            </div>
                            <div class="publicacion-texto">
                                <?= nl2br(htmlspecialchars($publicacion['Contingut'])) ?>
                            </div>
                            <div class="publicacion-footer">
                                <?php if ($publicacion['iduser'] == $usuario['iduser']): ?>
                                    <form action="" method="POST">
                                        <input type="hidden" name="id_publicacion" value="<?= $publicacion['idPublicacio'] ?>">
                                        <input type="hidden" name="id_hilo" value="<?= $idHilo ?>">
                                        <button type="submit" name="eliminar_publicacion" class="btn-eliminar">Eliminar</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
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
                <?php if ($totalPaginas > 1): ?>
                <div class="pagination">
                    <?php if ($paginaActual > 1): ?>
                        <a href="<?= paginaURL($paginaActual - 1, $idHilo) ?>" class="page-link prev">Anterior</a>
                    <?php endif; ?>
                    
                    <?php foreach ($enlacesPaginacion as $pagina): ?>
                        <?php if ($pagina === '...'): ?>
                            <span class="page-dots">...</span>
                        <?php else: ?>
                            <a href="<?= paginaURL($pagina, $idHilo) ?>" class="page-link <?= ($pagina == $paginaActual) ? 'active' : '' ?>">
                                <?= $pagina ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    
                    <?php if ($paginaActual < $totalPaginas): ?>
                        <a href="<?= paginaURL($paginaActual + 1, $idHilo) ?>" class="page-link next">Siguiente</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <aside class="sidebar">
                <div class="sidebar-box">
                    <h3>Hilos Relacionados</h3>
                    <ul class="related-hilos">
                        <?php foreach ($hilosRelacionados as $hilo): ?>
                        <li><a href="publicaciones-page.php?hilo=<?= $hilo['idHilo'] ?>"><?= htmlspecialchars($hilo['Titol']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="sidebar-box">
                    <h3>Usuarios Activos</h3>
                    <ul class="active-users">
                        <?php foreach ($usuariosActivos as $user): ?>
                        <li>
                            <img src="<?= !empty($user['profile_image']) ? $user['profile_image'] : '../profile/profile.png' ?>" alt="<?= htmlspecialchars($user['username']) ?>">
                            <span><?= htmlspecialchars($user['username']) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="sidebar-box cta-box">
                    <p>¿Te interesa este tema?</p>
                    <a href="hilos-page.php?tema=<?= urlencode($hiloDetalle['nomVideojoc']) ?>" class="btn-all-hilos">Ver todos los hilos</a>
                </div>
            </aside>
        </div>
    </div>
</main>

<script src="../Js/NavBar.js"></script>
<script src="../Js/Loading.js"></script>
</body>
</html>