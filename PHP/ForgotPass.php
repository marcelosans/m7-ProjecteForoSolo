<?php
require 'ConectaDB.php'; 
require '../vendor/autoload.php';

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

                // Lee el contenido del archivo CSS
                $css_File = file_get_contents("../CSS/Correu.css");

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

                $resetLink = "http://localhost/m7-ProjecteForoSolo/PHP/NewPass.php?code=$resetPassCode";

                // Contenido HTML del correo con CSS incorporado desde archivo externo
                $mail->Body = "
                                <html>
                                    <head>
                                        <style type=\"text/css\">" . $css_File . "</style>
                                        <link href='https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap' rel='stylesheet'>
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
    <link rel="stylesheet" href="../CSS/NewPass.css">
    <link rel="stylesheet" href="../CSS/Loading.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
</head>
<body>
<div id="loader" class="loader-overlay">
        <div>
            <img id="loading-gif" src="" alt="Cargando..." class="loading-gif">
            <div class="loading-text">CARGANDO...</div>
        </div>
    </div>
        
    <div class="login-container">
        <div id="content">
        <img src="../Recursos/img/logo-forosolo.png" alt="logo-foro-solo">
        
        <form action="ForgotPass.php" method="POST">
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
            <p><a href="Login.php">Tornar a iniciar sessió</a></p>
        </div>
    </div>
    <script src="../Js/Loading.js"></script>
            
</body>
</html>