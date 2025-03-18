<?php
require_once('ConectaDB.php');

if (isset($_POST['username'])) {
    $username = trim($_POST['username']);

    // Comprobar si el usuario existe en la base de datos
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $count = $stmt->fetchColumn();

    echo json_encode(["exists" => $count > 0]);
}
?>
