<?php
    require_once 'controller/config.php';

    session_start();
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: login.php");
        exit;
    }
    if ($_SESSION["login_as"] !== 'buyer') {
        header("location: unauth.php");
        exit;
    }

    $userId = $_SESSION["id"];

    function getProducts($conn) {
        $stmt = $conn->prepare("SELECT
            u.id as xx,
            u.firstname,
            u.lastname,
            u.gender,
            u.birthdate,
            u.address,
            u.contact_number,
            u.email,
            u.login_as,
            p.id,
            p.product_name,
            p.product_image,
            p.product_quantity,
            p.product_price,
            p.status,
            t.id as xxx,
            t.sold_date
            FROM products p
            LEFT JOIN users u ON u.id = p.user_id
            LEFT JOIN transactions t ON p.id = t.product_id
            WHERE p.status = 'available' and u.login_as = 'seller';");
    
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
    
        return $products;
    }
    $products = getProducts($conn);



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
                        <a class="nav-link active" href="order.php">My Orders <?php echo '('.$pendingOrdersCount.')'; ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    




    <div class="container">
        <div class="overlay" id="overlay"></div>
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 floating-form" id="detailsForm">
                <h2>Product Details</h2>
                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <td><strong>Product ID:</strong></td>
                            <td> <span id="productId"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Product Name:</strong></td>
                            <td> <span id="productName"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Quantity(Kilograms):</strong></td>
                            <td> <span id="productQuantity"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Price(Php):</strong></td>
                            <td> <span id="productPrice"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Seller Name:</strong></td>
                            <td> <span id="sellerName"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Address:</strong></td>
                            <td> <span id="address"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Contact Number:</strong></td>
                            <td> <span id="contactNumber"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td> <span id="email"></span></td>
                        </tr>
                    </table>
                </div>
                <div class="button-container text-center">
                    <button class="btn btn-primary" onclick="hideDetailsForm()">Close</button>
                </div>
            </div>
        </div>
    </div>






        
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="d-flex">
                    <input class="form-control me-2" type="search" id="searchInput" placeholder="Search products...">
                    <button class="btn btn-success" id="searchButton" onclick="searchProducts()">Search</button>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col">
                <h3 class="text-center">Available Products</h3>
                <div class="table-responsive">
                    <table class="table table-hover" id="table-products">
                        <thead class="table-success">
                            <tr>
                                <th scope="col">Product Name</th>
                                <th scope="col">Product Image</th>
                                <th scope="col">Quantity (kg)</th>
                                <th scope="col">Price(Php)</th>
                                <th scope="col">Options</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($products as $product) : ?>
                            <tr>
                                <td style="font-weight: bolder;"><?php echo $product['product_name']; ?></td>
                                <td><img src="<?php echo $product['product_image']; ?>" alt="Product Image" style="width: 100px; height: 100px;"></td>
                                <td><?php echo $product['product_quantity']; ?></td>
                                <td><?php echo $product['product_price']; ?></td>
                                <td>
                                    <button class="btn btn-success action-button mt-2" onclick="buyProduct('<?php echo $product['id'];?>')">BUY</button>
                                    <button class="btn btn-primary action-button purchase-button mt-2" onclick="showDetailsForm('<?php echo $product['id'];?>', '<?php echo $product['product_name'];?>','<?php echo $product['product_quantity'];?>', '<?php echo $product['product_price'];?>', '<?php echo $product['firstname'] . ' ' . $product['lastname'];?>', '<?php echo $product['address'];?>', '<?php echo $product['contact_number'];?>', '<?php echo $product['email'];?>')">DETAILS</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/search.js"></script>
    <script src="js/button.js"></script>
    <script src="js/details.js"></script>
</body>
</html>
