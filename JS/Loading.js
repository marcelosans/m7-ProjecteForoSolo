function handleLoading() {
    const loader = document.getElementById('loader');
    const content = document.getElementById('content');
    const loadingGif = document.getElementById('loading-gif');
    
    // Array con las rutas de los dos GIFs
    const gifs = [
        "../Recursos/Gif/68747470733a2f2f7061312e6e61727669692e636f6d2f363534322f343931363730343634343937316439633230633439346639373735663539653164333164363864345f68712e676966.gif",  // Reemplaza con la ruta de tu primer GIF
        "../Recursos/Gif/3c06599306cca1e170ce8df10949cf91.gif"
    ];
    
    // Seleccionar un GIF aleatorio
    const randomIndex = Math.floor(Math.random() * gifs.length);
    loadingGif.src = gifs[randomIndex];
    
    // Mostrar loader y ocultar contenido
    loader.classList.remove('hidden');
    content.style.display = 'none';
    
    // Ocultar loader y mostrar contenido después de 0.5 segundos
    setTimeout(function() {
        loader.classList.add('hidden');
        content.style.display = 'block';
    }, 500);
}

// Ejecutar al cargar la página
window.addEventListener('load', handleLoading);