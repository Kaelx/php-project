<?php
require_once "controller/config.php";

session_start();

if (!isset($_SESSION["loggedin"]) || empty($_SESSION["loggedin"])) {
    header("location: login.php");
    exit;
}

if ($_SESSION["login_as"] !== 'buyer') {
    header("location: unauth.php");
    exit;
}


$userId = $_SESSION["id"];

$ordersHistory = getOrderProducts($userId);
function getOrderProducts($userId)
{
    global $conn;

    $query = "SELECT o.id,
                 p.id AS product_id, 
                 p.product_name, 
                 p.product_quantity, 
                 p.product_price, 
                 p.status as status,
                 s.firstname AS seller_firstname, 
                 s.lastname AS seller_lastname, 
                 s.address AS seller_address, 
                 s.contact_number AS seller_contact_number, 
                 s.email AS seller_email, 
                 o.ordered_date,
                 t.sold_date
          FROM orders o
          left JOIN products p ON o.product_id = p.id
          left JOIN users s ON p.user_id = s.id
          left JOIN transactions t ON o.product_id = t.product_id
          WHERE o.user_id = ?";


    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $result = $stmt->get_result();
    $ordersHistory = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    return $ordersHistory;
}

    function getPendingOrdersCount($userId) {
        global $conn;
        $stmt = $conn->prepare("SELECT COUNT(*)
        FROM orders o
        JOIN products p ON o.product_id = p.id
        WHERE o.user_id = ? AND p.status = 'pending';");

        $stmt->bind_param("i", $userId);

        if (!$stmt->execute()) {
            die("Error in statement execution: " . $stmt->error);
        }

        $stmt->bind_result($pendingCount);
        $stmt->fetch();
        $stmt->close();

        return $pendingCount;
    }

    $pendingOrdersCount = getPendingOrdersCount($userId);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmers Assistant Web Service</title>
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
                        <a class="nav-link active" href="order.php">My Orders <?php echo '('.$pendingOrdersCount.')'; ?></a></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="row mt-3">
        <div class="col">
            <h3 class="text-center">Orders History</h3>
            <div class="table-responsive">
                <table class="table table-hover" id="table-products">
                    <thead class="table-success">
                        <tr>
                            <th scope="col">Transaction No.</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">Quantity (Kilograms)</th>
                            <th scope="col">Price (Php)</th>
                            <th scope="col">Seller Name</th>
                            <th scope="col">Seller Address</th>
                            <th scope="col">Contact Number</th>
                            <th scope="col">Email</th>
                            <th scope="col">Ordered Date</th>
                            <th scope="col">Sold Date</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ordersHistory as $orders) : ?>
                            <tr>
                                <td style="font-weight: bolder;"><?php echo $orders['id']; ?></td>
                                <td><?php echo $orders['product_name']; ?></td>
                                <td><?php echo $orders['product_quantity']; ?></td>
                                <td><?php echo $orders['product_price']; ?></td>
                                <td><?php echo $orders['seller_firstname'] . ' ' . $orders['seller_lastname']; ?></td>
                                <td><?php echo $orders['seller_address']; ?></td>
                                <td><?php echo $orders['seller_contact_number']; ?></td>
                                <td><?php echo $orders['seller_email']; ?></td>
                                <td><?php echo date('F d, Y', strtotime($orders['ordered_date'])); ?></td>
                                <td><?php echo !empty($orders['sold_date']) ? date('F d, Y', strtotime($orders['sold_date'])) : ''; ?></td>
                                <td><?php echo $orders['status']; ?></td>
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