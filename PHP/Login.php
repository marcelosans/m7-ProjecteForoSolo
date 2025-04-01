<?php
    session_start();
    
    if (isset($_SESSION['email'])) {
        header('Location: Index.php');
        exit;
    }
    
    $message      = ""; // Variable para almacenar mensajes
    $messageClass = ""; // Variable para almacenar la clase del mensaje
    
    // Procesar el formulario cuando se envía
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require_once 'ConectaDB.php';
        
        $email  = $_POST['email'];
        $contra = $_POST['password'];
        
        $sql = 'SELECT * FROM `users` WHERE (mail = :email OR username = :email) AND activeU = 1';
        
        $preparada = $db->prepare($sql);
        $preparada->bindParam(':email', $email);
        $preparada->execute();
        
        if ($preparada->rowCount() == 1) {
            $usuario = $preparada->fetch(PDO::FETCH_ASSOC);
            if (password_verify($contra, $usuario['passHash'])) {
                // Iniciar sesión y guardar datos del usuario
                $_SESSION['email']    = $usuario['mail'];
                $_SESSION['username'] = $usuario['username'];
                $_SESSION['nom']      = $usuario['userFirstName'];
                $_SESSION['profile']  = $usuario['profile_image'];
                $_SESSION['bio']      = $usuario['bio'];
                $_SESSION['location'] = $usuario['location'];
                header('Location: Index.php');
                exit;
            } else {
                $message      = 'La contraseña es incorrecta.';
                $messageClass = 'error-message';
            }
        } else {
            $message      = 'El usuario no existe o no está activo.';
            $messageClass = 'error-message';
        }
    }
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>ForoSolo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../CSS/Login.css">
    <link rel="stylesheet" href="../CSS/Loading.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
    <style>
        .error-message {
            color: red;
            text-align: center;
            font-weight: bold;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <!-- Animacion Load -->
    <div id="loader" class="loader-overlay">
        <div>
            <img id="loading-gif" src="" alt="Cargando..." class="loading-gif">
            <div class="loading-text">CARGANDO...</div>
        </div>
    </div>
    
    <div class="login-container">
        <div id="content">
            <img src="../Recursos/img/logo-forosolo.png" alt="logo-foro-solo">
            <form action="Login.php" method="POST">
                <div class="inputform">
                    <div class="form-group">
                        <input type="text" id="email" name="email" placeholder="Email/Usuario" required>
                    </div>
                    <div class="form-group password-field">
                        <input type="password" id="password" name="password" placeholder="Contraseña" required>
                        <button type="button" id="toggle-password" class="toggle-password">Mostrar</button>
                    </div>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="<?php echo $messageClass; ?>"><?php echo $message; ?></div>
                <?php endif; ?>

                <button type="submit" class="btn">Iniciar Sesión</button>
            </form>
            
            
            
            <div class="options">
                <a href="ForgotPass.php">¿No recuerdas tu contraseña?</a>
            </div>
            <div class="options">
                <a href="Register.php">Regístrate</a>
            </div>
        </div>


      
    </form>

<?php
require_once('ConectaDB.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $contra = $_POST['password'];

    $sql = 'SELECT * FROM `users` WHERE (mail = :email OR username = :email) AND activeU = 1';

    $preparada = $db->prepare($sql);
    $preparada->bindParam(':email', $email);
    $preparada->execute();

    if ($preparada->rowCount() == 1) {
        $usuario = $preparada->fetch(PDO::FETCH_ASSOC);
        if (password_verify($contra, $usuario['passHash'])) {
            session_start();
            $_SESSION['email'] = $usuario['mail'];
            header('Location: HomePage.php');
            exit;
        } else {
            $message = 'La contraseña es incorrecta.';
            $messageClass = 'error-message';
        }
    } else {
        $message = 'El usuario no existe.';
        $messageClass = 'error-message';
    }
}
?>

    


    </div>
    

    <script src="../Js/Loading.js"></script>
    <script src="../Js/Login.js"></script>
    
</body>
</html>