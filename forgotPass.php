<?php
require 'conectadb.php';
require 'vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Comprova si l'email existeix
    $stmt = $pdo->prepare("SELECT iduser FROM users WHERE mail = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Genera un codi d'activació SHA-256
        $activationCode = hash('sha256', random_bytes(32));

        // Guarda el codi a la base de dades
        $stmt = $pdo->prepare("UPDATE users SET activationCode = ? WHERE iduser = ?");
        $stmt->execute([$activationCode, $user['iduser']]);

        // Configura PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.tu-dominio.com'; // Configura el servidor SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'tu-correo@tu-dominio.com';
            $mail->Password = 'tu-contraseña';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('no-reply@tu-dominio.com', 'ForoSolo');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Benvingut a ForoSolo - Activa el teu compte";
            
            $activationLink = "https://tu-dominio.com/mailCheckAccount.php?code=$activationCode&mail=$email";

            // Contingut HTML del correu
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
                        <h2>Benvingut a ForoSolo!</h2>
                        <p>Per activar el teu compte, fes clic al següent botó:</p>
                        <a class='btn' href='$activationLink'>Activa el teu compte ara!</a>
                    </div>
                </body>
                </html>";

            $mail->send();
            echo "S'ha enviat un correu electrònic amb l'enllaç d'activació.";
        } catch (Exception $e) {
            echo "Error en enviar el correu: {$mail->ErrorInfo}";
        }
    } else {
        echo "Aquest email no està registrat.";
    }
}
?>
