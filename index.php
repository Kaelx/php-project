<?php
require_once 'controller/config.php';

session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if (isset($_SESSION["login_as"])) {
        if ($_SESSION["login_as"] === "seller") {
            header("location: main-seller.php");
            exit;
        } elseif ($_SESSION["login_as"] === "buyer") {
            header("location: main-buyer.php");
            exit;
        } else {
            header("location: unauth.php");
            exit;
        }
    }
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmers Assistant Web Service</title>
    <link rel="stylesheet" href="style/bootstrap.min.css">
</head>

<body>
    <header class="bg-success text-white text-center py-3">
        <h1><a href="index.php" class="text-decoration-none text-white">Farmers Assistant Web Service</a></h1>
    </header>

    <section class="container mt-4 text-center">
        <div class="card border-success mx-auto" style="max-width: 50rem;">
            <div class="card-body text-center">
                <p class="card-text">Farmers Assistant Web Service functions as an agricultural platform, enabling interaction between sellers and buyers. It enhances business communication and introduces transparency into the agricultural products.</p>
            </div>
        </div>
        <div class="text-center mt-5">
            <button type="button" class="btn btn-primary btn-lg" onclick="location.href='login.php'">CONTINUE TO LOGIN</button>
            <p class="mt-3">Don't have an account? <a class="text-success signup-link" href="register.php">Sign Up Now</a></p>
        </div>
    </section>

</body>

</html>