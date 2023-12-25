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


$usersCount = usersCount($conn);
$productsCount = productsCount($conn);
$soldProductsCount = soldProductsCount($conn);
$totalSales = totalSales($conn);
$users = getUsers($conn);


function usersCount($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE login_as = 'buyer' OR login_as = 'seller';");
    $stmt->execute();
    $result = $stmt->get_result();
    $usersCount = $result->fetch_row()[0];
    return $usersCount;
}

function productsCount($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products;");
    $stmt->execute();
    $result = $stmt->get_result();
    $productsCount = $result->fetch_row()[0];
    return $productsCount;
}

function soldProductsCount($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products where status = 'completed';");
    $stmt->execute();
    $result = $stmt->get_result();
    $soldProductsCount = $result->fetch_row()[0];
    return $soldProductsCount;
}

function totalSales($conn) {
    $stmt = $conn->prepare("SELECT SUM(product_price) FROM products where status = 'completed';");
    $stmt->execute();
    $result = $stmt->get_result();
    $totalSales = $result->fetch_row()[0];
    return $totalSales;
}


function getUsers($conn) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE login_as = 'buyer' OR login_as = 'seller';;");
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    return $users;
}
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
<script src="validate.js"></script>
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

    <div class="container mt-4">
    <div class="card p-2">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card">
                    <div class="card-body text-white bg-primary m-2">
                        <h5 class="card-title">Users</h5>
                        <p class="card-text display-5 text-center"><?php echo $usersCount; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card">
                    <div class="card-body text-white bg-success m-2">
                        <h5 class="card-title">Products</h5>
                        <p class="card-text display-5 text-center"><?php echo $productsCount; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card">
                    <div class="card-body text-white bg-danger m-2">
                        <h5 class="card-title">Transactions</h5>
                        <p class="card-text display-5 text-center"><?php echo $soldProductsCount; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card">
                    <div class="card-body text-white bg-info m-2">
                        <h5 class="card-title">Total Sales</h5>
                        <p class="card-text display-5 text-center"><?php echo number_format($totalSales, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="container mt-5">
    <div class="card p-2">
        <h2 class="text-center mb-4">Users</h2>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm">
                <thead class="thead-dark text-center">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Contact</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['firstname'].' '.$user['lastname'];?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['contact_number']; ?></td>
                            <td class="text-center">
                                <button class="btn btn-outline-danger action-button" onclick="banUser('<?php echo $user['id'];?>')">Ban User</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>




    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
