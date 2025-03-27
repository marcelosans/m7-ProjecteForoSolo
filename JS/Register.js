document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const verifyPasswordField = document.getElementById('verify_password');
    const passwordStrength = document.getElementById('password-strength');
    const togglePasswordButton = document.getElementById('toggle-password');

    // Skip if any elements are missing
    if (!passwordField || !verifyPasswordField || !passwordStrength || !togglePasswordButton) {
        return;
    }

    // Verificar si las contraseñas coinciden
    function checkPasswordsMatch() {
        const password = passwordField.value;
        const verifyPassword = verifyPasswordField.value;
        
        passwordField.classList.remove('error-border', 'success-border');
        verifyPasswordField.classList.remove('error-border', 'success-border');
        
        if (verifyPassword === '') {
            return; // No hacer nada si el campo está vacío
        } else if (password !== verifyPassword) {
            passwordField.classList.add('error-border');
            verifyPasswordField.classList.add('error-border');
        } else {
            passwordField.classList.add('success-border');
            verifyPasswordField.classList.add('success-border');
        }
    }

    // Mostrar/ocultar la contraseña
    function togglePasswordVisibility() {
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            verifyPasswordField.type = 'text';
            togglePasswordButton.textContent = ' Ocultar ';
        } else {
            passwordField.type = 'password';
            verifyPasswordField.type = 'password';
            togglePasswordButton.textContent = ' Mostrar ';
        }
    }

    // Verificar la fortaleza de la contraseña
    function checkPasswordStrength() {
        const password = passwordField.value;
        const strongPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/;
        
        if (password.length === 0) {
            passwordStrength.style.display = "none";
            return;
        }
        
        passwordStrength.style.display = "block";
        
        passwordStrength.classList.remove("weak-password", "strong-password");
        
        if (strongPassword.test(password)) {
            passwordStrength.textContent = "La teva contrasenya és segura";
            passwordStrength.classList.add("strong-password");
        } else {
            passwordStrength.textContent = "Afegeix una contrasenya com aquesta (Fel1p$112)";
            passwordStrength.classList.add("weak-password");
        }
    }

    // Eventos
    passwordField.addEventListener('input', () => {
        checkPasswordsMatch();
        checkPasswordStrength();
    });
    
    verifyPasswordField.addEventListener('input', checkPasswordsMatch);
    
    togglePasswordButton.addEventListener('click', togglePasswordVisibility);
});