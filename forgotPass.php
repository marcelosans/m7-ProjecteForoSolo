<?php
require 'conectadb.php'; 
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Format d'email invàlid.";
        $messageClass = "error-message";
    } else {
        try {
            $stmt = $db->prepare("SELECT iduser FROM users WHERE mail = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                $resetPassCode = bin2hex(random_bytes(32));
                $resetPassExpiry = date("Y-m-d H:i:s", strtotime('+1 hour')); 

                $stmt = $db->prepare("UPDATE users SET resetPassCode = ?, resetPassExpiry = ? WHERE iduser = ?");
                $stmt->execute([$resetPassCode, $resetPassExpiry, $user['iduser']]);

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; 
                $mail->SMTPAuth = true;
                $mail->Username = 'alex.correat@educem.net';
                $mail->Password = 'qvxe jflw jfgv eqrt';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('no-reply@tu-dominio.com', 'ForoSolo');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = "Restabliment de contrasenya - ForoSolo";

                $resetLink = "http://localhost/m7-ProjecteForoSolo/newPass.php?code=$resetPassCode";

                // Contenido HTML del correo
                $mail->Body = "
                                <html>
                                    <head>
                                        <link href='https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap' rel='stylesheet'>
                                        <style>
                                            * {
                                                text-align: center;
                                                font-family: 'Press Start 2P', monospace; /* Aplica la fuente pixelada */
                                            }
                                            .container {
                                                width: 80%;
                                                margin: 0 auto;
                                                padding: 20px;
                                                border: 1px solid #ddd;
                                                border-radius: 10px;
                                                background-color: #949493;
                                            }
                                            .logo {
                                                max-width: 200px;
                                                margin-bottom: 20px;
                                            }
                                            .btn {
                                                padding: 0.625rem 0.75rem;
                                                font-size: 1rem;
                                                line-height: 1.5;
                                                color: #495057;
                                                background-color: #fff;
                                                border: 1px solid #ced4da;
                                                border-radius: 0.25rem;
                                                text-decoration: none;
                                                display: inline-block;
                                                font-family: 'Press Start 2P', monospace; /* Aplica la fuente pixelada al botón */
                                            }
                                            p {
                                                color: black;
                                                font-family: 'Press Start 2P', monospace; /* Aplica la fuente pixelada */
                                            }
                                            h2 {
                                                color: black;
                                                font-family: 'Press Start 2P', monospace; /* Aplica la fuente pixelada */
                                            }
                                        </style>
                                    </head>
                                    <body>
                                        <div class='container'>
                                            <img src='https://drive.google.com/uc?id=1d2_XF9OzFIN_0tTx6hs5gwL8QcD4ieDR' alt='Logo' class='logo'>
                                            <h2>Restabliment de contrasenya</h2>
                                            <p>Per restablir la teva contrasenya, fes clic al següent botó:</p>
                                            <a class='btn' href='$resetLink'>Restablir contrasenya</a>
                                            <p>Si el botó no funciona, copia i enganxa aquest enllaç al teu navegador:</p>
                                            <p><a href='$resetLink'>$resetLink</a></p>
                                        </div>
                                    </body>
                                </html>
                                ";
                

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
    <link rel="stylesheet" href="./css/newPass.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
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