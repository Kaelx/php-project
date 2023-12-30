<?php

session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if (isset($_SESSION["login_as"])) {
        if ($_SESSION["login_as"] === "seller") {
            header("location: main-seller.php");
            exit;
        } elseif ($_SESSION["login_as"] === "buyer") {
            header("location: main-buyer.php");
            exit;
        }else{
            header("location: unauth.php");
            exit;
        }
    }
}


if (isset($_POST["recover"])) {
    require_once 'controller/config.php';
    $email = $_POST["email"];

    $sql = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $query = mysqli_num_rows($sql);
    $fetch = mysqli_fetch_assoc($sql);

    if (mysqli_num_rows($sql) <= 0) {
?>
        <script>
            alert("<?php echo "Invalid, email does not exist! " ?>");
        </script>
    <?php
    } else if ($fetch["status"] == 0) {
        echo '<script>alert("Account not verified yet. Please verify first!"); window.location.replace("verification.php");</script>';
    } else {
        // generate token by binaryhexa 
        $token = bin2hex(random_bytes(50));

        //session_start ();
        $_SESSION['token'] = $token;
        $_SESSION['email'] = $email;

        require "phpmailer/PHPMailerAutoload.php";
        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->Host = 'smtp.mail.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';

        //account
        $mail->Username='';  //email
        $mail->Password='';  //16 keys

        //send by
        $mail->setFrom('sample@mail.com', 'Password Reset');

        // get email from input
        $mail->addAddress($_POST["email"]);

        $mail->isHTML(true);
        $mail->Subject = "Recovery Account";
        $mail->Body = "<b>Dear User</b>
        <h3>We received a request to reset your password.</h3>
        <p>Kindly click the below link to reset your password</p>
        http://localhost/aaa/PHP1.1/php_project/reset_psw.php"; //change this to correct path

        if (!$mail->send()) {
        ?>
            <script>
                alert("<?php echo " Invalid Email " ?>");
            </script>
        <?php
        } else {
        ?>
            <script>
                alert("<?php echo "To recover you account, kindly check your email to " .$email ?>");
                window.location.replace("login.php");
            </script>
<?php
        }
    }
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="icon" href="storage/farmer.png" type="image/x-icon">
    <link rel="stylesheet" href="style/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="style/icon.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-success">
    <div class="container">
        <a class="navbar-brand" href="#">Password Recovery</a>
    </div>
</nav>

<main class="login-form mt-md-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 mt-3">
                <div class="card">
                    <div class="card-header">
                        Password Recovery
                    </div>
                    <div class="card-body">
                        <form action="#" method="POST" name="recover_psw">
                            <div class="form-group row">
                                <label for="email_address" class="col-md-4 col-form-label text-md-right"><span class="material-icons">email</span> Email Address</label>
                                <div class="col-md-8">
                                    <input type="email" id="email_address" class="form-control" name="email" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary mt-3" name="recover">Recover</button>
                                    <a href="login.php" class="btn btn-secondary mt-3">Cancel</a>
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


