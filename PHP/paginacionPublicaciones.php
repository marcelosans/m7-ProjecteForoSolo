<?php
// paginacion.php - Funciones de ayuda para la paginación

/**
 * Calcular los números de página para mostrar en la paginación
 */
function generarEnlacesPaginacion($paginaActual, $totalPaginas) {
    $enlaces = [];
    
    // Siempre incluir la primera página
    $enlaces[] = 1;
    
    // Calcular el rango alrededor de la página actual
    $inicio = max(2, $paginaActual - 1);
    $fin = min($totalPaginas - 1, $paginaActual + 1);
    
    // Agregar elipsis después de la página 1 si es necesario
    if ($inicio > 2) {
        $enlaces[] = '...';
    }
    
    // Agregar páginas en el rango
    for ($i = $inicio; $i <= $fin; $i++) {
        $enlaces[] = $i;
    }
    
    // Agregar elipsis antes de la última página si es necesario
    if ($fin < $totalPaginas - 1) {
        $enlaces[] = '...';
    }
    
    // Siempre incluir la última página si hay más de una página
    if ($totalPaginas > 1) {
        $enlaces[] = $totalPaginas;
    }
    
    return $enlaces;
}

/**
 * Generar URL para la paginación
 */
function paginaURL($pagina, $idHilo) {
    return "publicaciones-page.php?hilo=$idHilo&pagina=$pagina";
}

/**
 * Validar y ajustar el número de página actual
 */
function validarPagina($paginaActual, $totalPaginas) {
    if ($paginaActual < 1) {
        return 1;
    } else if ($paginaActual > $totalPaginas && $totalPaginas > 0) {
        return $totalPaginas;
    }
    return $paginaActual;
}
?>