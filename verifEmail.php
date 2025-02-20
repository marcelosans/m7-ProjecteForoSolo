<?php
require 'conectadb.php'; // Conexión a la base de datos
require 'vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";
$messageClass = "";

    // Obtener el correo y la contraseña desde el formulario
    $email = $_GET['email'];
     // Asegúrate de que este campo esté en tu formulario

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
                
                $activationCode = bin2hex(random_bytes(32));
                // Guarda el token en la base de datos
                $stmt = $db->prepare("UPDATE users SET activationCode = ? WHERE iduser = ?");
                $stmt->execute([$activationCode,$user['iduser']]);

                // Configura PHPMailer
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Servidor SMTP de Gmail
                $mail->SMTPAuth = true;
                $mail->Username = 'alex.correat@educem.net';
                $mail->Password = 'qvxe jflw jfgv eqrt';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('no-reply@forosolo.com', 'ForoSolo');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = "Verificacio d'email - ForoSolo";

                $activateLink = "http://localhost/m7-ProjecteForoSolo/verifExito.php?code=$activationCode&mail=" . urlencode($email);

                // Contenido HTML del correo
                $mail->Body = '<html>
                <head>
                    <link rel="preconnect" href="https://fonts.googleapis.com">
                    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
                    <style>
                        * { text-align: center; font-optical-sizing: auto; }
                        body { font-family: "Pixelify Sans", serif; }
                        .container { width: 80%; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; background-color: #949493; }
                        .logo { max-width: 200px; margin-bottom: 20px; }
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
                        }
                        p { color: black; }
                        h2 { color: black; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <img src="https://drive.google.com/uc?id=1d2_XF9OzFIN_0tTx6hs5gwL8QcD4ieDR" alt="Logo" class="logo">
                        <h2>Verificació email</h2>
                        <p>Per verificar el email següent, fes clic al següent botó:</p>
                        <a class="btn" href="$activateLink">Verificar</a>
                        <p>Si el botó no funciona, copia i enganxa aquest enllaç al teu navegador:</p>
                        <p><a href="$activateLink">$activateLink</a></p>
                    </div>
                </body>
                </html>';

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

?>

<?php
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>ForoSolo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./css/index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <h1>Verifica el correo</h1>
        <p>Verifica el correo para poder iniciar sessión</p>
        <form action="index.php" method="POST">
            <button class="btn" type="submit" value="login">Volver al login</button>
        </form>
    </div>
</body>
</html>