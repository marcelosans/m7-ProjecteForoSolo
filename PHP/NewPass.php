<?php
require 'ConectaDB.php'; 

$message = "";
$messageClass = "";

if (isset($_GET['code'])) {
    $resetPassCode = $_GET['code'];

    try {
        $stmt = $db->prepare("SELECT iduser, resetPassExpiry FROM users WHERE resetPassCode = ?");
        $stmt->execute([$resetPassCode]);
        $user = $stmt->fetch();

        if ($user) {
            $currentDateTime = date("Y-m-d H:i:s");
            if ($currentDateTime > $user['resetPassExpiry']) {
                $message = "El codi de restabliment ha expirat.";
                $messageClass = "error-message";
            } else {
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $newPassword = $_POST['new_password'];
                    $confirmPassword = $_POST['confirm_password'];

                    if ($newPassword !== $confirmPassword) {
                        $message = "Les contrasenyes no coincideixen, torna a enviar la petició.";
                        $messageClass = "error-message";
                    } else {
                        $passHash = password_hash($newPassword, PASSWORD_DEFAULT);

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
    <link rel="stylesheet" href="../CSS/NewPass.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <img src="../Recursos/img/logo-forosolo.png" alt="logo-foro-solo">        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageClass; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if (isset($user) && empty($messageClass)): ?>
            <form action="NewPass.php?code=<?php echo $resetPassCode; ?>" method="POST">
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
            <p><a href="Login.php">Tornar a iniciar sessió</a></p>
        </div>
    </div>
</body>
</html>