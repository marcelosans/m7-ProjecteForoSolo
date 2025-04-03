document.addEventListener("DOMContentLoaded", function () {
        
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