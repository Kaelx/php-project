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
        } else {
            header("location: unauth.php");
            exit;
        }
    }
}

require_once 'controller/config.php';

$email_value = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, password, login_as, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row["password"])) {
            if ($row["status"] == 0) {
                $_SESSION['otp'] = $row["id"];
                $_SESSION['mail'] = $email;
                echo '<script>alert("Account not verified yet. Please verify first!"); window.location.replace("verification.php");</script>';

            }elseif ($row["login_as"] === 'seller') {
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $row["id"];
                $_SESSION["login_as"] = $row["login_as"];

                logAction($_SESSION["id"], '(seller) has logged in');
                header("Location: main-seller.php");
                exit();

            } elseif ($row["login_as"] === 'buyer') {
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $row["id"];
                $_SESSION["login_as"] = $row["login_as"];

                logAction($_SESSION["id"], '(buyer) has logged in');
                header("Location: main-buyer.php");
                exit();

            } elseif ($row["login_as"] === 'banned') {
                header("Location: banned.php");
                exit();

            } else {
                echo '<script>alert("Something went wrong!");</script>';
                $email_value = $email;
            }
        } else {
            echo '<script>alert("Wrong Password!");</script>';
            $email_value = $email;
        }
    } else {
        echo '<script>alert("Invalid, email does not exist!");</script>';
        $email_value = $email;
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
    <title>Login</title>
    <link rel="icon" href="storage/farmer.png" type="image/x-icon">
    <link rel="stylesheet" href="style/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="style/icon.css">
</head>
<body>
    <header class="bg-success text-white text-center py-3">
        <h1><a href="index.php" class="text-decoration-none text-white">Farmers Assistant Web Service</a></h1>
    </header>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                <div class="card mt-4 mb-4">
                    <div class="card-header">
                        <h4>Login</h4>
                    </div>
                    <form class="m-3" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
                        <div class="form-group">
                            <label for="email" class="form-label mt-4"><i class="material-icons">email</i> Email address</label>
                            <input type="email" name="email" class="form-control" id="email" aria-describedby="emailHelp" value="<?php echo $email_value; ?>" placeholder="Enter email" autocomplete="on" required autofocus>
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label mt-4"><i class="material-icons">lock</i> Password</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Password" autocomplete="on" required>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary"><i class="material-icons">login</i> LOGIN</button>
                            <p class="mt-3">Don't have an account? <a class="text-success signup-link" href="register.php">Sign Up <i class="material-icons">person_add</i> </a></p>
                            <a class="text-success" href="recover_acc.php">Forgot Password <i class="material-icons">help</i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>