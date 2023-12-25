<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}


if (isset($_GET['id'])) {
    $pid = $_GET['id'];

    $updtatus = "UPDATE products SET status = 'completed' WHERE id = ?";
    $stmt1 = $conn->prepare($updtatus);
    $stmt1->bind_param("i", $pid);

    if ($stmt1->execute()) {

        $fetchProductIdQry = "SELECT id FROM products WHERE status = 'completed' AND id = ?";
        $stmt3 = $conn->prepare($fetchProductIdQry);
        $stmt3->bind_param("i", $pid);

        if ($stmt3->execute()) {
            $stmt3->bind_result($product_id);
            $stmt3->fetch();
            $stmt3->close();

            if (!empty($product_id)) {

                $insTransQry = "INSERT INTO transactions (product_id, sold_date) VALUES (?, CURRENT_DATE)";
                $stmt2 = $conn->prepare($insTransQry);
                $stmt2->bind_param("i", $product_id);

                if ($stmt2->execute()) {
                    header("location: ../pending.php");
                    exit;
                    
                } 
                $stmt2->close();
            } 
        }
    }

    $stmt1->close();
}

$conn->close();
?>
