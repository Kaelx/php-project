<?php
session_start();
require_once "../controller/config.php";

if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    $stmt = $conn->prepare("UPDATE products SET status = 'Canceled' WHERE id = ?");
    $stmt->bind_param("i", $productId);

    if ($stmt->execute()) {
        header("location: ../pending.php");
        exit;
    }
    $stmt->close();

}

?>
