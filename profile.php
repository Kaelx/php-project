<?php
session_start();

if (!isset($_SESSION["loggedin"]) || empty($_SESSION["loggedin"])) {
    header("location: login.php");
    exit;
}

if ($_SESSION["login_as"] === 'admin') {
    header("location: unauth.php");
    exit;
}

require_once 'controller/config.php';

$id = $_SESSION["id"];

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userData = $result->fetch_assoc();
} else {
    echo '<script>alert("User data not found!");</script>';
    exit;
}

$stmt->close();
$conn->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmers Assistant Web Service</title>
    <link rel="icon" href="storage/farmer.png" type="image/x-icon">
    <link rel="stylesheet" href="style/bootstrap.min.css">
    <link rel="stylesheet" href="style/profile.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="style/icon.css">
</head>

<body>
    <header class="bg-success text-white text-center py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1><a href="index.php" class="text-decoration-none text-white">Farmers Assistant Web Service</a></h1>
            <div>
                <a href="index.php" class="btn btn-outline-light m-2"><span class="material-icons">home</span> HOME</a>
                <a href="controller/logout.php" class="btn btn-outline-light m-2"><span class="material-icons">logout</span> Logout</a>
            </div>
        </div>
    </header>

    <nav class="navbar navbar-expand-lg bg-info text-center">
        <div class="container ">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <h1 class="nav-link">USER PAGE</h1>
                </li>
            </ul>
        </div>
    </nav>


    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">My Profile</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                            <tbody>
                                    <tr class="table-light">
                                        <th scope="row">Name:</th>
                                        <td> <?php echo $userData['firstname'] . ' ' . $userData['lastname']; ?></td>
                                    </tr>
                                    <tr class="table-light">
                                        <th scope="row">Gender:</th>
                                        <td> <?php echo $userData['gender']; ?></td>
                                    </tr>
                                    <tr class="table-light">
                                        <th scope="row">Birthdate:</th>
                                        <td> <?php echo date('F d, Y', strtotime($userData['birthdate'])); ?></td>
                                    </tr>
                                    <tr class="table-light">
                                        <th scope="row">Address:</th>
                                        <td> <?php echo $userData['address']; ?></td>
                                    </tr>
                                    <tr class="table-light">
                                        <th scope="row">Contact Number:</th>
                                        <td> <?php echo $userData['contact_number']; ?></td>
                                    </tr>
                                    <tr class="table-light">
                                        <th scope="row">Email:</th>
                                        <td> <?php echo $userData['email']; ?></td>
                                    </tr>
                                    <tr class="table-light">
                                        <th scope="row">Account Type:</th>
                                        <td> <?php echo $userData['login_as']; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="text-center m-4">
                        <a href="update.php" class="btn btn-primary">
                            <span class="material-icons">manage_accounts</span> Update Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>



</body>

</html>