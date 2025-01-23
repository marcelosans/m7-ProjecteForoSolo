<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>ForoSolo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./css/register.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <img src="./img/logo-forosolo.png" alt="logo-foro-solo">
        
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
                    <div class="form-group">
                        <label for="password">Contrasenya <span style="color: red;">*</span></label>
                        <input type="password" id="password" name="password" placeholder="Contrasenya" required>
                    </div>
                    <div class="form-group">
                        <label for="verify_password">Confirmar contrasenya <span style="color: red;">*</span></label>
                        <input type="password" id="verify_password" name="verify_password" placeholder="Verif. Contrasenya" required>
                    </div>
                </div>
            </div>
            <?php
                require_once('conectadb.php');

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $username = $_POST['username'];
                    $email = $_POST['email'];
                    $password = $_POST['password'];
                    $firstName = $_POST['first_name'];
                    $lastName = $_POST['last_name'];
                    $creationDate = date("Y-m-d H:i:s");
                    
                    $passHash = password_hash($password, PASSWORD_DEFAULT);
                    
                    try {
                        $stmt = $db->prepare("INSERT INTO Users (mail, username, passHash, userFirstName, userLastName, creationDate, activeU) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $activeU = 1; 
                        $stmt->execute([$email, $username, $passHash, $firstName, $lastName, $creationDate, $activeU]);
                        
                        echo "Registro exitoso.";
                    } catch (PDOException $e) {
                        echo "El usuario ya esta creado.";
                    }
                }
            ?>
            <button type="submit" class="btn">Registrar-se</button>
        </form>
        <div class="options">
            <p>Ja tens compte? <a href="login.php">Inicia sessió</a></p>
        </div>
    </div>
</body>
</html>