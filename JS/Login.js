document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const togglePasswordButton = document.getElementById('toggle-password');

    // Skip if any elements are missing
    if (!passwordField || !togglePasswordButton) {
        return;
    }

    // Mostrar/ocultar la contraseña
    function togglePasswordVisibility() {
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            togglePasswordButton.textContent = 'Ocultar';
        } else {
            passwordField.type = 'password';
            togglePasswordButton.textContent = 'Mostrar';
        }
    }

    // Eventos
    togglePasswordButton.addEventListener('click', togglePasswordVisibility);
});