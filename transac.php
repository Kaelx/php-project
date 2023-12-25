<?php
require_once "controller/config.php";

session_start();

if (!isset($_SESSION["loggedin"]) || empty($_SESSION["loggedin"])) {
    header("location: login.php");
    exit;
}

if ($_SESSION["login_as"] !== 'seller') {
    header("location: unauth.php");
    exit;
}


$id = $_SESSION["id"];

function getTransactionHistory($userId)
{
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM products 
                            JOIN transactions ON products.id = transactions.product_id 
                            WHERE user_id = ?");


    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
}

$transactionHistory = getTransactionHistory($id);

function getPendingOrdersCount($userId) {
    global $conn;

    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE user_id = ? AND status = 'pending'");
    if (!$stmt) {
        die("Error in statement preparation: " . $conn->error);
    }

    $stmt->bind_param("i", $userId);

    if (!$stmt->execute()) {
        die("Error in statement execution: " . $stmt->error);
    }

    $stmt->bind_result($pendingCount);
    $stmt->fetch();
    $stmt->close();

    return $pendingCount;
}
$userId = $_SESSION["id"];
$pendingOrdersCount = getPendingOrdersCount($userId);

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmers Assistant Web Service</title>
    <link rel="stylesheet" href="style/bootstrap.min.css">
    <link rel="stylesheet" href="style/main.css">
</head>

<body>

    <header class="bg-success text-white text-center py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1><a href="index.php" class="text-decoration-none text-white">Farmers Assistant Web Service</a></h1>
            <div>
                <a href="profile.php" class="btn btn-outline-light m-2">Profile</a>
                <a href="logout.php" class="btn btn-outline-light m-2">Logout</a>
            </div>
        </div>
    </header>

    <nav class="navbar navbar-expand-lg bg-info">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="pending.php">Pending Transaction <?php echo '('.$pendingOrdersCount.')'; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="transac.php">Transaction History</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <div class="row mt-3">
        <div class="col">
            <h3 class="text-center">TRANSACTION</h3>
            <div class="table-responsive">
                <table class="table table-hover" id="table-products">
                    <thead class="table-success">
                        <tr>
                            <th scope="col">Transaction No.</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">Quantity (Kilograms)</th>
                            <th scope="col">Price (Php)</th>
                            <th scope="col">Date Sold</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactionHistory as $transac) : ?>
                            <tr>
                                <td style="font-weight: bolder;"><?php echo $transac['id']; ?></td>
                                <td><?php echo $transac['product_name']; ?></td>
                                <td><?php echo $transac['product_quantity']; ?></td>
                                <td><?php echo $transac['product_price']; ?></td>
                                <td><?php echo date('F d, Y', strtotime($transac['sold_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>