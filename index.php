<?php
Header("Cache-Control: max-age=3000, must-revalidate");


require 'controller/config.php';
session_start();


function redirect($url) {
    header("Location: $url");
    exit;
}

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if (isset($_SESSION["login_as"])) {
        switch ($_SESSION["login_as"]) {
            case "seller":
                redirect("main-seller.php");
                break;
            case "buyer":
                redirect("main-buyer.php");
                break;
            default:
                redirect("unauth.php");
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
    <link rel="icon" href="storage/farmer.png" type="image/x-icon">
    <link rel="stylesheet" href="style/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="style/icon.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <header class="bg-success text-white text-center py-3">
        <h1><a href="index.php" class="text-decoration-none text-white">Farmers Assistant Web Service</a></h1>
    </header>

    <section class="container mt-4 text-center flex-grow-1">
        <div class="card border-success mx-auto" style="max-width: 50rem;">
            <div class="card-body text-center">
                <p class="card-text">Farmers Assistant Web Service functions as an agricultural platform, enabling interaction between sellers and buyers. It enhances business communication and introduces transparency into the agricultural products.</p>
            </div>
        </div>
        <div class="text-center mt-5">
            <button type="button" class="btn btn-primary btn-lg" onclick="location.href='login.php'">CONTINUE TO LOGIN</button>
            <p class="mt-3">Don't have an account? <a class="text-success signup-link" href="register.php">Sign Up Now <i class="material-icons">person_add</i></a></p>
        </div>
    </section>

</body>

<?php
include 'view/footer.php';
?>

</html>