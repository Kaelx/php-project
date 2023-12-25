<?php
require_once 'controller/config.php';
session_start();

if(isset($_POST["verify"])){
    if(isset($_SESSION['otp']) && isset($_SESSION['mail'])){
        $otp = $_SESSION['otp'];
        $email = $_SESSION['mail'];
        $otp_code = $_POST['otp_code'];

        if($otp != $otp_code){
            echo '<script>alert("Invalid OTP code. Please try again.");</script>';
        } else {
            $updateStatusQuery = "UPDATE users SET status = 1 WHERE email = ?";
            $stmt = $conn->prepare($updateStatusQuery);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->close();

            echo '<script>alert("Account verified successfully. You may now sign in."); window.location.replace("login.php");</script>';
        }
    } else {
        echo '<script>alert("Session variables not set. Please try again.");</script>';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Account</title>
    <link rel="stylesheet" href="style/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-success">
    <div class="container">
        <a class="navbar-brand" href="#">Verification Account</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<main class="login-form mt-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Verify Your Account</div>
                    <div class="card-body">
                        <form action="#" method="POST">
                            <div class="form-group row">
                                <label for="otp" class="col-md-4 col-form-label text-md-right">Enter OTP Code</label>
                                <div class="col-md-6">
                                    <input type="text" id="otp" class="form-control" name="otp_code" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary mt-3" name="verify">Verify</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>
