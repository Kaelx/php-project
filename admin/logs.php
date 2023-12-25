<?php
require_once '../controller/config.php';
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}
if ($_SESSION["login_as"] !== 'admin') {
    header("location: ../unauth.php");
    exit;
}


$stmt = $conn->prepare("SELECT logs.*, users.firstname, users.lastname
                       FROM logs 
                       LEFT JOIN users ON logs.user_id = users.id order by timestamp desc");

$stmt->execute();
$result = $stmt->get_result();
$logs = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN</title>
    <link rel="stylesheet" href="../style/bootstrap.min.css">
    <link rel="stylesheet" href="main.css">
</head>
<body>

    <header class="bg-warning text-white text-center py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1><a href="index.php" class="text-decoration-none text-white">ADMIN</a></h1>
            <div class="button-container">
                <a href="profile.php" class="btn btn-outline-light m-2">Profile</a>
                <a href="../logout.php" class="btn btn-outline-light m-2">Logout</a>
            </div>
        </div>
    </header>

    <nav class="navbar navbar-expand-lg bg-dark">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav text-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="main.php">Main</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="logs.php">Logs</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <div class="container mt-5">
        <div class="card m-5 col-md-8 mx-auto">
            <div class="container mt-3">
                <h2 class="text-center">Logs</h2>
                <div class="table-responsive mt-3">
                    <table class="table table-hover">
                        <tbody>
                            <?php foreach ($logs as $log) : ?>
                                <tr>
                                    <td>
                                        <?php echo $log['firstname'] . ' ' . $log['lastname'] . ' ' . $log['action_type']; ?>
                                    </td>
                                    <td>
                                        <?php echo date('F d, Y - g:ia', strtotime($log['timestamp'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>