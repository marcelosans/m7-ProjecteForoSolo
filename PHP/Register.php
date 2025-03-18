<?php
require_once 'ConectaDB.php';

class RegistrationHandler
{
    private $message;
    private $messageClass;

    public function handleRegistration()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $verifyPassword = $_POST['verify_password'];
            $firstName = $_POST['first_name'];
            $lastName = $_POST['last_name'];
            $creationDate = date("Y-m-d H:i:s");

            if ($this->arePasswordsMatching($password, $verifyPassword)) {
                if ($this->isPasswordValid($password)) {
                    $passHash = password_hash($password, PASSWORD_DEFAULT);
                    try {
                        $stmt = $GLOBALS['db']->prepare("INSERT INTO Users (mail, username, passHash, userFirstName, userLastName, creationDate, activeU) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $activeU = 0;
                        $stmt->execute([$email, $username, $passHash, $firstName, $lastName, $creationDate, $activeU]);
                        $this->message = "Registre completat amb èxit.";
                        $this->messageClass = "success-message";
                        header("Location: VerifEmail.php?email=$email");
                    } catch (PDOException $e) {
                        $this->message = "El nom d'usuari ja està registrat.";
                        $this->messageClass = "error-message";
                    }
                } else {
                    $this->message = "La contrasenya ha de tenir almenys 8 caràcters, incloent-hi una lletra majúscula, una minúscula, un número i un caràcter especial.";
                    $this->messageClass = "error-message";
                }
            } else {
                $this->message = "Les contrasenyes no coincideixen.";
                $this->messageClass = "error-message";
            }
        }
    }

    private function arePasswordsMatching($password, $verifyPassword)
    {
        return $password === $verifyPassword;
    }

    private function isPasswordValid($password)
    {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);
        return $uppercase && $lowercase && $number && $specialChars && strlen($password) >= 8;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getMessageClass()
    {
        return $this->messageClass;
    }
}

$registrationHandler = new RegistrationHandler();
$registrationHandler->handleRegistration();
?>

<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="utf-8">
    <title>ForoSolo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../CSS/Register.css">
    <link rel="stylesheet" href="../CSS/Loading.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>
</head>

<body>
    <!-- Animacion Load -->
    <div id="loader" class="loader-overlay">
        <div>
            <img id="loading-gif" src="" alt="Cargando..." class="loading-gif">
            <div class="loading-text">CARGANDO...</div>
        </div>
    </div>

    <div id="content">
    <!-- Contenido de la página -->
    
        <div class="login-container">
            <img src="../Recursos/img/logo-forosolo.png" alt="logo-foro-solo">

            <form action="register.php" method="POST">
                <div class="inputform">
                    <div class="form-group">
                        <label for="username">Nom d'usuari <span style="color: red;">*</span></label>
                        <input type="text" id="username" name="username" placeholder="Usuari" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correu electrònic <span style="color: red;">*</span></label>
                        <input type="email" id="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group-half">
                        <div class="form-group">
                            <label for="first_name">Nom</label>
                            <input type="text" id="first_name" name="first_name" placeholder="Nom">
                        </div>
                        <div class="form-group">
                            <label for="last_name">Cognom</label>
                            <input type="text" id="last_name" name="last_name" placeholder="Cognom">
                        </div>
                    </div>
                    <div class="form-group-half">
                        <div class="form-group password-field">
                            <label for="password">Contrasenya <span style="color: red;">*</span></label>
                            <input type="password" id="password" name="password" placeholder="Contrasenya" required>
                            <button type="button" id="toggle-password" class="toggle-password">Mostrar</button>
                        </div>
                        <div class="form-group">
                            <label for="verify_password">Confirmar contrasenya <span style="color: red;">*</span></label>
                            <input type="password" id="verify_password" name="verify_password" placeholder="Verif. Contrasenya" required>
                        </div>
                        
                    </div>
                    <div id="password-strength" class="password-strength"></div>
                </div>

                <button type="submit" class="btn">Registrar-se</button>
            </form>

            <?php if (!empty($registrationHandler->getMessage())): ?>
            <div class="message <?php echo $registrationHandler->getMessageClass(); ?>">
                <?php echo $registrationHandler->getMessage(); ?>
            </div>
            <?php endif; ?>

            <div class="options">
                <p>Ja tens compte? <a href="login.php">Inicia sessió</a></p>
            </div>
        </div>
    </div>

    <script src="../Js/Loading.js"></script>
    <script src="../Js/Register.js"></script>
    

</body>
</html>