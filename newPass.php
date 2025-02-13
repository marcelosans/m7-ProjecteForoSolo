<?php
require 'conectadb.php'; // Conexión a la base de datos

$message = "";
$messageClass = "";

// Verificar si se ha proporcionado el código de restablecimiento
if (isset($_GET['code'])) {
    $resetPassCode = $_GET['code'];

    // Verificar si el código de restablecimiento es válido
    try {
        $stmt = $db->prepare("SELECT iduser, resetPassExpiry FROM users WHERE resetPassCode = ?");
        $stmt->execute([$resetPassCode]);
        $user = $stmt->fetch();

        // Verificar si el usuario existe y si el código no ha expirado
        if ($user) {
            $currentDateTime = date("Y-m-d H:i:s");
            if ($currentDateTime > $user['resetPassExpiry']) {
                $message = "El codi de restabliment ha expirat.";
                $messageClass = "error-message";
            } else {
                // Procesar el formulario al enviar
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $newPassword = $_POST['new_password'];
                    $confirmPassword = $_POST['confirm_password'];

                    // Validar que las contraseñas coincidan
                    if ($newPassword !== $confirmPassword) {
                        $message = "Les contrasenyes no coincideixen.";
                        $messageClass = "error-message";
                    } else {
                        // Hashear la nueva contraseña
                        $passHash = password_hash($newPassword, PASSWORD_DEFAULT);

                        // Actualizar la contraseña en la base de datos
                        $stmt = $db->prepare("UPDATE users SET passHash = ?, resetPassCode = NULL, resetPassExpiry = NULL WHERE iduser = ?");
                        $stmt->execute([$passHash, $user['iduser']]);

                        $message = "La contrasenya s'ha restablert amb èxit.";
                        $messageClass = "success-message";
                    }
                }
            }
        } else {
            $message = "Codi de restabliment invàlid.";
            $messageClass = "error-message";
        }
    } catch (Exception $e) {
        $message = "S'ha produït un error: " . $e->getMessage();
        $messageClass = "error-message";
    }
} else {
    $message = "No s'ha proporcionat cap codi de restabliment.";
    $messageClass = "error-message";
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Restablir contrasenya - ForoSolo</title>
    <link rel="stylesheet" href="./css/register.css">
    <style>
        .message {
            text-align: center;
            font-weight: bold;
            margin-top: 1rem;
        }
        .error-message {
            color: red;
        }
        .success-message {
            color: green;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="./img/logo-forosolo.png" alt="logo-foro-solo">
        
        <h2>Restablir contrasenya</h2>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageClass; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if (isset($user) && empty($messageClass)): ?>
            <form action="newPass.php?code=<?php echo $resetPassCode; ?>" method="POST">
                <div class="inputform">
                    <div class="form-group">
                        <label for="new_password">Nova contrasenya <span style="color: red;">*</span></label>
                        <input type="password" id="new_password" name="new_password" placeholder="Introdueix la nova contrasenya" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmar contrasenya <span style="color: red;">*</span></label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirma la nova contrasenya" required>
                    </div>
                </div>
                <button type="submit" class="btn">Restablir contrasenya</button>
            </form>
        <?php endif; ?>

        <div class="options">
            <p><a href="login.php">Tornar a iniciar sessió</a></p>
        </div>
    </div>
</body>
</html>