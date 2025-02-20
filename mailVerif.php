<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';



$mensaje = '
<html>
<head>
    <title>Verificació de Correu</title>
</head>
<body style="font-family: Arial, sans-serif; text-align: center;">
    <h2 style="color: #007bff;">Confirmació de Correu</h2>
    <p>Hola, si us plau, verifica el teu correu fent clic en el botó següent:</p>
    <a href="https://localhost/m7-ProjecteForoSolo/forgotPass.php?code=$code&mail=$email' . urlencode($_SESSION['email']) . '" 
       style="display: inline-block; background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
       Verificar Correu
    </a>
    <p>Si no vas sol·licitar aquest correu, ignora’l.</p>
</body>
</html>
';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPDebug = 0;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;

    $mail->Username = $_SESSION['email'];
    $mail->Password = $_SESSION['password'];

    $mail->SetFrom($_SESSION['email'], "Verifica el correo, Por Favor");
    $mail->Subject = $_POST['assumpte'];
    $mail->MsgHTML($mensaje);

    $mail->AddAddress($_SESSION['email']);
    if (!$mail->Send()) {
        $result = "Error: " . $mail->ErrorInfo;
    } else {
        $result = "Correu enviat correctament!";
    }
    
    
    exit();
}
?>