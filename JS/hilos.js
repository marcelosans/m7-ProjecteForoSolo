document.addEventListener("DOMContentLoaded", function () {
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