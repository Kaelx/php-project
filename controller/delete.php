<?php
require_once 'config.php';

function deleteProductFromDatabase($productId) {
    global $conn;

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);

    if ($stmt->execute()) {
        header("location: ../index.php");
        exit();

    } else {
        echo '<script>alert("Error: ' . $stmt->error . '");</script>';
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    $productId = $_GET["id"];
    deleteProductFromDatabase($productId);
}

$conn->close();
?>