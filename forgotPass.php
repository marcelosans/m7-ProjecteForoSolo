<?php
require 'conectadb.php'; // Conexión a la base de datos
require 'vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el correo y la contraseña desde el formulario
    $email = $_POST['email'];
    $password = $_POST['password']; // Asegúrate de que este campo esté en tu formulario

    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Format d'email invàlid.";
        $messageClass = "error-message";
    } else {
        try {
            // Verifica si el email existe
            $stmt = $db->prepare("SELECT iduser FROM users WHERE mail = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Genera un token seguro para restablecer la contraseña
                $resetPassCode = bin2hex(random_bytes(32));
                $resetPassExpiry = date("Y-m-d H:i:s", strtotime('+1 hour')); // Token válido por 1 hora

                // Guarda el token en la base de datos
                $stmt = $db->prepare("UPDATE users SET resetPassCode = ?, resetPassExpiry = ? WHERE iduser = ?");
                $stmt->execute([$resetPassCode, $resetPassExpiry, $user['iduser']]);

                // Configura PHPMailer
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Servidor SMTP de Gmail
                $mail->SMTPAuth = true;
                $mail->Username = $email; // Usa el correo del formulario
                $mail->Password = $password; // Usa la contraseña del formulario
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('no-reply@tu-dominio.com', 'ForoSolo');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = "Restabliment de contrasenya - ForoSolo";

                $resetLink = "http://localhost/m7-ProjecteForoSolo/reset_password.php?token=$resetPassCode&mail=" . urlencode($email);

                // Contenido HTML del correo
                $mail->Body = "
                    <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; text-align: center; }
                            .container { width: 80%; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
                            .btn { background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <img src='https://tu-dominio.com/img/logo-forosolo.png' width='150' alt='ForoSolo Logo'>
                            <h2>Restabliment de contrasenya</h2>
                            <p>Per restablir la teva contrasenya, fes clic al següent botó:</p>
                            <a class='btn' href='$resetLink'>Restablir contrasenya</a>
                            <p>Si el botó no funciona, copia i enganxa aquest enllaç al teu navegador:</p>
                            <p><a href='$resetLink'>$resetLink</a></p>
                        </div>
                    </body>
                    </html>";

                if ($mail->send()) {
                    $message = "S'ha enviat un correu electrònic amb l'enllaç per restablir la contrasenya.";
                    $messageClass = "success-message";
                } else {
                    $message = "No s'ha pogut enviar el correu. Torna-ho a intentar més tard.";
                    $messageClass = "error-message";
                }
            } else {
                $message = "Aquest email no està registrat.";
                $messageClass = "error-message";
            }
        } catch (Exception $e) {
            $message = "S'ha produït un error: " . $e->getMessage();
            $messageClass = "error-message";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Recuperar contrasenya - ForoSolo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./css/register.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
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
        
        <form action="forgotPass.php" method="POST">
            <div class="inputform">
                <div class="form-group">
                    <label for="email">Correu electrònic <span style="color: red;">*</span></label>
                    <input type="email" id="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <label for="password">Contrasenya <span style="color: red;">*</span></label>
                    <input type="password" id="password" name="password" placeholder="Contrasenya" required>
                </div>
            </div>

            <button type="submit" class="btn">Enviar enllaç de recuperació</button>
        </form>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageClass; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="options">
            <p><a href="login.php">Tornar a iniciar sessió</a></p>
        </div>
    </div>
</body>
</html>