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
        }else{
            header("location: unauth.php");
            exit;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];
    $lastname = $_POST["lastname"];
    $firstname = $_POST["firstname"];
    $gender = $_POST["gender"];
    $birthdate = $_POST["birthdate"];
    $address = $_POST["address"];
    $contactNumber = $_POST["contact_number"];
    $loginAs = $_POST["login_as"];

    $checkEmailStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmailStmt->bind_param("s", $email);
    $checkEmailStmt->execute();
    $checkEmailStmt->store_result();

    if ($checkEmailStmt->num_rows > 0) {
        $checkEmailStmt->close();
        echo "<script>alert('Email already exists. Please use a different email.'); window.location.href='register.php';</script>";
        exit();
    }else{

    $checkEmailStmt->close();

    $hashKey = password_hash($password, PASSWORD_BCRYPT);

    $insertStmt = $conn->prepare("INSERT INTO users (email, password, lastname, firstname, gender, birthdate, address, contact_number, login_as) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insertStmt->bind_param("sssssssss", $email, $hashKey, $lastname, $firstname, $gender, $birthdate, $address, $contactNumber, $loginAs);

    if ($insertStmt->execute()) {
        $otp = rand(100000,999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['mail'] = $email;
        require "phpmailer/PHPMailerAutoload.php";
        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->Host='smtp.mail.com';
        $mail->Port=587;
        $mail->SMTPAuth=true;
        $mail->SMTPSecure='tls';
    
        $mail->Username='';
        $mail->Password='';

        $mail->setFrom('sample@mail.com', 'OTP Verification');
        $mail->addAddress($_POST["email"]);
    
        $mail->isHTML(true);
        $mail->Subject="Verification OTP";
        $mail->Body="<p>Dear user, </p> <h3>Your verification OTP code is $otp <br></h3>";

        if(!$mail->send()){
            ?>
                <script>
                    alert("<?php echo "Register Failed, Invalid Email "?>");
                </script>
            <?php
        }else{
            ?>
            <script>
                alert("<?php echo "Register Successfully, OTP sent to " . $email ?>");
                window.location.replace('verification.php');
            </script>
            <?php

            $userId = $insertStmt->insert_id;
            logAction($userId, 'new user registered');
            exit();
        }
    }

    $insertStmt->close();
}

}

$conn->close();


function logAction($userId, $actionType = null) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO logs (user_id, action_type) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $actionType);
    $stmt->execute();
    $stmt->close();
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
    <link rel="stylesheet" href="style/bootstrap.min.css">
    <link rel="stylesheet" href="style/register.css">
</head>
<script src="js/validate.js"></script>

<body>
    <header class="bg-success text-white text-center py-3">
        <h1><a href="index.php" class="text-decoration-none text-white">Farmers Assistant Web Service</a></h1>
    </header>


    <div class="container">
        <div class="container mt-4 mb-4 col-md-6 card">
            <div class="card-header">
                <h4>REGISTER FORM</h4>
            </div>
            <form class="m-3" action="<?php echo ($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return validateForm()">

                <div class="form-group">
                    <label class="col-form-label" for="email">Email</label>
                    <input type="email" class="form-control" placeholder="Input email" id="email" name="email" value="" autocomplete="on" required>
                </div>

                <div class="form-group">
                    <label class="col-form-label mt-4" for="password">Password</label>
                    <input type="password" class="form-control" placeholder="Input password" id="password" name="password" value="" minlength="8" required >
                </div>
                <div class="form-group">
                    <label class="col-form-label mt-4" for="confirm_password">Confirm Password</label>
                    <input type="password" class="form-control" placeholder="confirm password" id="confirm_password" name="confirm_password" value="" minlength="8" required>
                </div>
                <div class="form-group">
                    <label class="col-form-label mt-4" for="firstname">Given Name</label>
                    <input type="text" class="form-control" placeholder="Input name" id="firstname" name="firstname" value="" required>
                </div>
                <div class="form-group">
                    <label class="col-form-label mt-4" for="lastname">Family Name</label>
                    <input type="text" class="form-control" placeholder="Input family name" id="lastname" name="lastname" value="" required>
                </div>
                <div class="form-group">
                    <label class="col-form-label mt-4" for="gender">Gender</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="" disabled selected>-- Please Select --</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="col-form-label mt-4" for="birthdate">Birthdate</label>
                    <input type="date" id="birthdate" name="birthdate" class="form-control" value="" max="2020-12-31" required>
                </div>

                <div class="form-group">
                    <label class="col-form-label mt-4" for="address">Address</label>
                    <input type="text" class="form-control" placeholder="Input address" id="address" name="address" value="" autocomplete="on" required>
                </div>
                <div class="form-group">
                    <label class="col-form-label mt-4" for="contact_number">Contact Number</label>
                    <input type="number" class="form-control" placeholder="Input contact no." id="contact_number" name="contact_number" value="" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="11" required>
                </div>

                <div class="form-group">
                    <label class="col-form-label mt-4" for="login_as">Account Type</label>
                    <select id="login_as" name="login_as" class="form-control" required>
                        <option value="" disabled selected>-- Please Select --</option>
                        <option value="buyer">BUYER</option>
                        <option value="seller">SELLER</option>
                    </select>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success">Submit</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <p class="mt-3">Already have an account? <a class="text-success signup-link" href="login.php">Login</a></p>
                </div>

            </form>

        </div>
    </div>


</body>

</html>
