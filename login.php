<?php
session_start(); 
if (isset($_SESSION['email'])) {
    header('Location: index.php'); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="ca">
    <head>
        <meta charset="utf-8">
        <title>ForoSolo</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="./css/login.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
    </head>
    <body>
    
    <div class="login-container">
       
        <img src="./img/logo-forosolo.png" alt="logo-foro-solo">
        <form action="login.php" method="POST">
            <div class="inputform">
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Email/Usuario" required>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Contraseña" required>
            </div>
            </div>
            <button type="submit" class="btn">Iniciar Sesión</button>
        </form>
        
<?php
require_once('conectadb.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $contra = $_POST['password'];

    $sql = 'SELECT * FROM `users` WHERE mail = :email AND activeU = 1';

    $preparada = $db->prepare($sql);
    $preparada->bindParam(':email', $email);
    $preparada->execute();

    if ($preparada->rowCount() == 1) {
        $usuario = $preparada->fetch(PDO::FETCH_ASSOC);
        if (password_verify($contra, $usuario['passHash'])) {
            session_start();
            $_SESSION['email'] = $usuario['mail'];
            header('Location: index.php');
            exit;
        } else {
            echo 'La contraseña es incorrecta.';
        }
    } else {
        echo 'El usuario no existe.';
    }
}
?>

        <div class="options">
            <a href="forgotPass.php">¿No recuerdas tu contraseña?</a>
        </div>
        <div class="options">
            <a href="register.php">Regístrate</a>
        </div>
    </div>
    </body>
</html>