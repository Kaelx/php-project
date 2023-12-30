<?php
require_once "controller/config.php";

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if ($_SESSION["login_as"] !== 'seller') {
    header("location: unauth.php");
    exit;
}


function getSellerPendingOrders($userId)
{
    global $conn;

    $query = "SELECT p.id as product_id,p.product_name, p.product_quantity, p.product_price,
                     u.firstname AS buyer_firstname, 
                     u.lastname AS buyer_lastname,
                     u.address AS buyer_address, 
                     u.contact_number AS buyer_contact_number, 
                     u.email AS buyer_email,
                     o.ordered_date,
                     p.status
              FROM products p
              JOIN orders o ON p.id = o.product_id
              JOIN users u ON o.user_id = u.id
              WHERE p.user_id = ? AND p.status = 'pending'";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error in statement preparation: " . $conn->error);
    }

    $stmt->bind_param("i", $userId);

    if (!$stmt->execute()) {
        die("Error in statement execution: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $stmt->close();

    return $result->fetch_all(MYSQLI_ASSOC);
}

$userId = $_SESSION["id"];
$pendingOrders = getSellerPendingOrders($userId);



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
    <link rel="icon" href="storage/farmer.png" type="image/x-icon">
    <link rel="stylesheet" href="style/bootstrap.min.css">
    <link rel="stylesheet" href="style/main.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="style/icon.css">
</head>

<body>

<?php
    include 'view/header.php';
    
    ?>

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
                        <a class="nav-link active" href="pending.php">Pending Transaction <?php echo '('.$pendingOrdersCount.')';?></a>
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
            <h3 class="text-center">PENDING ORDERS</h3>
            <div class="table-responsive">
                <table class="table table-hover" id="table-products">
                    <thead class="table-success">
                        <tr>
                            <th scope="col">Product Name</th>
                            <th scope="col">Quantity (Kilograms)</th>
                            <th scope="col">Price (Php)</th>
                            <th scope="col">Buyer's Name</th>
                            <th scope="col">Address</th>
                            <th scope="col">Contact Number</th>
                            <th scope="col">Email</th>
                            <th scope="col">Ordered Date</th>
                            <th scope="col">Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingOrders as $order) : ?>
                            <tr>
                                <td><?= $order['product_name'] ?></td>
                                <td><?= $order['product_quantity'] ?></td>
                                <td><?= $order['product_price'] ?></td>
                                <td><?= $order['buyer_firstname'] ?> <?= $order['buyer_lastname'] ?></td>
                                <td><?= $order['buyer_address'] ?></td>
                                <td><?= $order['buyer_contact_number'] ?></td>
                                <td><?= $order['buyer_email'] ?></td>
                                <td><?php echo date('F d, Y', strtotime($order['ordered_date'])); ?></td>
                                <td>
                                    <button class="btn btn-danger mt-2 update-button" onclick="confirmSold(<?php echo $order['product_id']; ?>)">Sold</button>
                                    <button class="btn btn-warning mt-2 update-button" onclick="confirmCancel(<?php echo $order['product_id']; ?>)">Cancel</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/button.js"></script>
</body>

</html>