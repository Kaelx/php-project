<?php

session_start();
if (isset($_POST["recover"])) {
    require_once 'controller/config.php';
    $email = $_POST["email"];

    $sql = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $query = mysqli_num_rows($sql);
    $fetch = mysqli_fetch_assoc($sql);

    if (mysqli_num_rows($sql) <= 0) {
?>
        <script>
            alert("<?php echo "Sorry, no emails exists " ?>");
        </script>
    <?php
    } else if ($fetch["status"] == 0) {
    ?>
        <script>
            alert("Sorry, your account must verify first, before you recover your password !");
            window.location.replace("index.php");
        </script>
        <?php
    } else {
        // generate token by binaryhexa 
        $token = bin2hex(random_bytes(50));

        //session_start ();
        $_SESSION['token'] = $token;
        $_SESSION['email'] = $email;

        require "phpmailer/PHPMailerAutoload.php";
        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';

        //account
        $mail->Username='000phpmailer@gmail.com';
        $mail->Password='qbrz dvmt otmf sjly';

        //send by
        $mail->setFrom('no-reply@mail.com', 'Password Reset');

        // get email from input
        $mail->addAddress($_POST["email"]);

        $mail->isHTML(true);
        $mail->Subject = "Recovery Account";
        $mail->Body = "<b>Dear User</b>
        <h3>We received a request to reset your password.</h3>
        <p>Kindly click the below link to reset your password</p>
        http://localhost/aaa/PHP1.1/php_project/reset_psw.php";

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
    <link rel="stylesheet" href="style/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="#">Password Recovery</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<main class="login-form mt-md-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Password Recovery
                    </div>
                    <div class="card-body">
                        <form action="#" method="POST" name="recover_psw">
                            <div class="form-group row">
                                <label for="email_address" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                <div class="col-md-6">
                                    <input type="text" id="email_address" class="form-control" name="email" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
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


