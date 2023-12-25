<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $pid = $_GET['id'];

    $updateStatusQuery = "UPDATE products SET status = 'pending' WHERE id = ?";
    $stmt = $conn->prepare($updateStatusQuery);
    $stmt->bind_param("i", $pid);

    if ($stmt->execute()) {
        $stmt->close();

        $fetchProductIdQuery = "SELECT id FROM products WHERE status = 'pending' AND id = ?";
        $stmt3 = $conn->prepare($fetchProductIdQuery);
        $stmt3->bind_param("i", $pid);

        if ($stmt3->execute()) {
            $stmt3->bind_result($product_id);
            $stmt3->fetch();
            $stmt3->close();

            if (!empty($product_id)) {
                $user_id = $_SESSION["id"];

                $insertOrderQuery = "INSERT INTO orders (user_id, product_id, ordered_date) VALUES (?, ?, CURRENT_DATE)";
                $stmt2 = $conn->prepare($insertOrderQuery);
                $stmt2->bind_param("ii", $user_id, $product_id);

                if ($stmt2->execute()) {
                    header("Location: ../index.php");
                    exit;
                }
                
                $stmt2->close();
            }
        }
    }
    $stmt->close();
}

$conn->close();
?>
