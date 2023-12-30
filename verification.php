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


if (isset($_POST["resend"])) {
    if (isset($_SESSION['otp']) && isset($_SESSION['mail'])) {
        $newOtp = rand(100000, 999999);
        $_SESSION['otp'] = $newOtp;
        $email = $_SESSION['mail'];
        require "phpmailer/PHPMailerAutoload.php";
        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';

        $mail->Username = '000phpmailer@gmail.com';    //email
        $mail->Password = 'qbrz dvmt otmf sjly';    //16 keys

        $mail->setFrom('000phpmailer@gmail.com', 'OTP Verification');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "New Verification OTP";
        $mail->Body = "<p>Dear user, </p> <h3>Your new verification OTP code is $newOtp <br></h3>";

        if (!$mail->send()) {
            echo '<script>alert("Failed to resend OTP. Please try again.");</script>';
        } else {
            echo '<script>alert("New OTP sent successfully.");</script>';
        }
    } else {
        echo '<script>alert("Session variables not set. Please try again.");</script>';
    }
}

if (isset($_POST["verify"])) {
    if (isset($_SESSION['otp']) && isset($_SESSION['mail'])) {
        $otp = $_SESSION['otp'];
        $email = $_SESSION['mail'];
        $otp_code = $_POST['otp_code'];

        if ($otp != $otp_code) {
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
    <link rel="icon" href="storage/farmer.png" type="image/x-icon">
    <link rel="stylesheet" href="style/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="style/icon.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-success">
        <div class="container">
            <a class="navbar-brand" href="#">Verification Account</a>
        </div>
    </nav>

    <main class="login-form mt-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 mt-3">
                    <div class="card">
                        <div class="card-header"><span class="material-icons">privacy_tip</span> Verify Your Account</div>
                        <div class="card-body">
                            <form action="#" method="POST">
                                <div class="form-group row">
                                    <label for="otp" class="col-md-4 col-form-label text-md-right">Enter OTP Code</label>
                                    <div class="col-md-8">
                                        <input type="text" id="otp" class="form-control" name="otp_code" autofocus>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary mt-3" name="verify">Verify</button>
                                        <button type="submit" class="btn btn-secondary mt-3" name="resend">Resend</button>
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