<?php
require_once 'controller/config.php';
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $pid = $_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $quantity = $_POST['productQuantity'];
        $price = $_POST['productPrice'];

        $updateProduct = "UPDATE products SET product_quantity = ?, product_price = ? WHERE id = ?";
        $stmt = $conn->prepare($updateProduct);
        $stmt->bind_param("ddi", $quantity, $price, $pid);
        if ($stmt->execute()) {
            header('Location: index.php');
            exit;
        }
    }

    $getProduct = "SELECT product_name, product_image, product_quantity, product_price FROM products WHERE id = ?";
    $stmt = $conn->prepare($getProduct);
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $stmt->bind_result($productName, $productImage, $quantity, $price);
    $stmt->fetch();
    $stmt->close();
} else {
    header('Location: unauth.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmers Assistant Web Service</title>
    <link rel="stylesheet" href="style/bootstrap.min.css">
    <link rel="stylesheet" href="style/main.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="style/icon.css">
</head>

<body>
    <?php
    include 'view/header.php';
    ?>

    <div class="container mt-4 ">
        <h3 class="mb-4 text-center">EDIT PRODUCT</h3>
        <div class="row justify-content-center">
            <div class="col-md-5">
                <form method="POST" action="">
                    <table class="table border border-2">
                        <tr>
                            <td><label for="productName">Product Name:</label></td>
                            <td><?php echo $productName; ?></td>
                        </tr>
                        <tr>
                            <td><label for="productImage">Product Image:</label></td>
                            <td><img src="<?php echo $productImage; ?>" class="img-fluid" alt="Product Image"></td>
                        </tr>
                        <tr>
                            <td><label for="productQuantity">Quantity (Kilograms):</label></td>
                            <td><input type="number" id="productQuantity" name="productQuantity" step="0.01" required class="form-control" value="<?php echo $quantity; ?>"></td>
                        </tr>
                        <tr>
                            <td><label for="productPrice">Price (Php):</label></td>
                            <td><input type="number" id="productPrice" name="productPrice" step="0.01" required class="form-control" value="<?php echo $price; ?>"></td>
                        </tr>
                    </table>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary mb-5">Submit</button>
                        <a href="index.php" class="btn btn-secondary mb-5">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>