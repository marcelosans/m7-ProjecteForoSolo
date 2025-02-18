<?php
require 'conectadb.php';
$stmt = $db->prepare("UPDATE users SET activeU = ? ,activationDate = ? WHERE mail = ? AND activationCode = ?");
$stmt->execute([1,date("Y-m-d H:i:s"),$_GET['mail'],$_GET['code']]);

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
        <h1>Verificacion Exitosa:</h1>
        <h2><?php echo $_GET['mail']; ?></h2>
        <form action="index.php" method="POST">
            <button class="btn" type="submit" value="ir-menu">Ir al menu</button>
        </form>
    </div>
</body>
</html>