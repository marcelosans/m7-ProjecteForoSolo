const newPasswordField = document.getElementById('new_password');
const confirmPasswordField = document.getElementById('confirm_password');
const passwordStrength = document.getElementById('password-strength');
const togglePasswordButton = document.getElementById('toggle-password');

// Verificar si las contraseñas coinciden
function checkPasswordsMatch() {
    const password = newPasswordField.value;
    const confirmPassword = confirmPasswordField.value;
    
    newPasswordField.classList.remove('error-border', 'success-border');
    confirmPasswordField.classList.remove('error-border', 'success-border');
    
    if (confirmPassword === '') {
        return; 
    } else if (password !== confirmPassword) {
        newPasswordField.classList.add('error-border');
        confirmPasswordField.classList.add('error-border');
    } else {
        newPasswordField.classList.add('success-border');
        confirmPasswordField.classList.add('success-border');
    }
}

// Mostrar/ocultar la contraseña
function togglePasswordVisibility() {
    if (newPasswordField.type === 'password') {
        newPasswordField.type = 'text';
        confirmPasswordField.type = 'text';
        togglePasswordButton.textContent = 'Ocultar';
    } else {
        newPasswordField.type = 'password';
        confirmPasswordField.type = 'password';
        togglePasswordButton.textContent = 'Mostrar';
    }
}

function checkPasswordStrength() {
    const password = newPasswordField.value;
    const strongPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/;
    
    if (password.length > 0) {
        passwordStrength.style.display = "block";
    } else {
        passwordStrength.style.display = "none";
        return;
    }
    
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
newPasswordField.addEventListener('input', () => {
    checkPasswordsMatch();
    checkPasswordStrength();
});
confirmPasswordField.addEventListener('input', checkPasswordsMatch);
togglePasswordButton.addEventListener('click', togglePasswordVisibility);