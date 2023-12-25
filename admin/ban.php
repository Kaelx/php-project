<?php

session_start();

require_once "../controller/config.php";

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    $stmt = $conn->prepare("UPDATE users SET Login_as = 'banned' WHERE id = ?");
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        echo '<script>alert("User has been banned!");window.location.href="index.php";</script>';

    }

    $stmt->close();

}



?>

