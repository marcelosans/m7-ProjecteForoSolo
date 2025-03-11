// Función para manejar el efecto de carga
function handleLoading() {
    const loader = document.getElementById('loader');
    const content = document.getElementById('content');
    
    // Mostrar loader y ocultar contenido
    loader.classList.remove('hidden');
    content.style.display = 'none';
    
    // Ocultar loader y mostrar contenido después de 1.5 segundos
    setTimeout(function() {
        loader.classList.add('hidden');
        content.style.display = 'block';
    }, 1500);
}

// Ejecutar al cargar la página
window.addEventListener('load', handleLoading);