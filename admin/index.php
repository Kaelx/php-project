<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if (isset($_SESSION["login_as"])) {
        if ($_SESSION["login_as"] === "admin") {
            header("location: main.php");
            exit;
        } else {
            header("location: ../unauth.php");
            exit;
        }
    }
}

require_once '../controller/config.php';

$email_value = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, password, login_as FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $stmt->close();
    } else {
        echo '<script>alert("Error: ' . $stmt->error . '");</script>';
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row["password"])) {
            if ($row["login_as"] == 'admin') {
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $row["id"];
                $_SESSION["login_as"] = $row["login_as"];

                logAction($_SESSION["id"], '(admin) has logged in');

                header("Location: main.php");
                exit();
            } else {
                echo '<script>alert("Access restricted. Only admins can log in.");</script>';
            }
        } else {
            echo '<script>alert("Wrong Password!");</script>';
            $email_value = $email;
        }
    } else {
        echo '<script>alert("Invalid, Email not found!");</script>';
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
    <title>ADMIN LOGIN</title>
    <link rel="stylesheet" href="../style/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="../style/icon.css">
</head>
<script src="validate.js"></script>
<body>
    <header class="bg-warning text-white text-center py-3">
        <h1><a href="index.php" class="text-decoration-none text-white">Farmers Assistant Web Service</a></h1>
    </header>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 mt-4 mb-4 card">
                <div class="card-header">
                    <h4>Admin Login</h4>
                </div>
                <form class="m-3" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" onsubmit="return validatelogin()">
                    <div class="form-group">
                        <label for="email" class="form-label mt-4"><i class="material-icons">email</i> Email address</label>
                        <input type="email" name="email" class="form-control" id="email" aria-describedby="emailHelp" value="<?php echo $email_value; ?>" placeholder="Enter email" autocomplete="on">
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label mt-4"><i class="material-icons">lock</i> Password</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="Password" autocomplete="on">
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="material-icons">login</i> LOGIN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>