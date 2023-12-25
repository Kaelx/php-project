<?php
session_start();
require_once "../controller/config.php";


$newPassword = "admin123";
$hashKey = password_hash($newPassword, PASSWORD_BCRYPT);

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE login_as = 'admin';");
$stmt->bind_param("s", $hashKey);
$stmt->execute();
$stmt->close();

header("location: index.php");
exit();

?>