<?php
    require_once 'controller/config.php';
    session_start();
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: login.php");
        exit;
    }
    if ($_SESSION["login_as"] !== 'seller') {
        header("location: unauth.php");
        exit;
    }

    $userId = $_SESSION["id"];

    function getProducts($userId) {
        global $conn;
    
    
        $stmt = $conn->prepare("SELECT * FROM products WHERE user_id = ? AND status = 'available'");
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
    $products = getProducts($userId);




    function addProductToDatabase($productName, $productImage, $productQuantity, $productPrice, $userId) {
        global $conn;
    
        $stmt = $conn->prepare("INSERT INTO products (product_name, product_image, product_quantity, product_price, user_id) 
                                VALUES (?, ?, ?, ?, ?)");
    
        $stmt->bind_param("ssddi", $productName, $productImage, $productQuantity, $productPrice, $userId);
        $stmt->execute();
    
        $stmt->close();
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["productName"]) && isset($_POST["productQuantity"]) && isset($_POST["productPrice"])) {
            $productName = $_POST["productName"];
            $productQuantity = $_POST["productQuantity"];
            $productPrice = $_POST["productPrice"];
            $userId = $_SESSION["id"];
    
            $target_dir = "storage/";
            $target_file = $target_dir . basename($_FILES["productImage"]["name"]);

            if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $target_file)) {
                addProductToDatabase($productName, $target_file, $productQuantity, $productPrice, $userId);
            } else {
                echo '<script>alert("Sorry, there was an error uploading your file.");</script>';
            }
            
        }
    }
    
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
                        <a class="nav-link active" href="pending.php">Pending Transaction <?php echo '('.$pendingOrdersCount.')'; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="transac.php">Transaction History</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-3">
        <div class="row justify-content-between">
            <div class="col-md-4 mb-3 mb-md-0">
            <button type="button" class="btn btn-success" id="addProductButton">Add Product</button>
            </div>
            <div class="col-md-4">
                <div class="d-flex">
                    <input class="form-control me-2" type="search" id="searchInput" placeholder="Search products...">
                    <button class="btn btn-success" id="searchButton" onclick="searchProducts()">Search</button>
                </div>
            </div>
        </div>






        <div class="overlay" id="overlay"></div>
        <div class="floating-form container" id="floatingForm">
            <h3 class="mb-4">Add Product</h3>
            <form class="form-container" id="productForm" method="post" enctype="multipart/form-data" onsubmit="event.preventDefault(); submitProductForm();">
                <div class="mb-3">
                    <label for="productName" class="form-label">Product Name:</label>
                    <input type="text" class="form-control" id="productName" name="productName" required>
                </div>

                <div class="mb-3">
                    <label for="productImage" class="form-label">Product Image:</label>
                    <input type="file" class="form-control" id="productImage" name="productImage" accept="image/*" required>
                </div>

                <div class="mb-3">
                    <label for="productQuantity" class="form-label">Quantity (Kilograms):</label>
                    <input type="number" class="form-control" id="productQuantity" name="productQuantity" step="0.01" required>
                </div>

                <div class="mb-3">
                    <label for="productPrice" class="form-label">Price (Php):</label>
                    <input type="number" class="form-control" id="productPrice" name="productPrice" step="0.01" required>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary" onclick="cancelAddProduct()">Cancel</button>
            </form>
        </div>




            <div class="row mt-3">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table table-hover" id="table-products">
                            <thead class="table-success">
                                <tr>
                                    <th scope="col">Product Name</th>
                                    <th scope="col">Product Image</th>
                                    <th scope="col">Quantity (Kilograms)</th>
                                    <th scope="col">Price(Php)</th>
                                    <th scope="col">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product) : ?>
                                    <tr>
                                        <td><?php echo $product['product_name']; ?></td>
                                        <td><img src="<?php echo $product['product_image']; ?>" alt="Product Image" style="width: 100px; height: 100px;"></td>
                                        <td><?php echo $product['product_quantity']; ?></td>
                                        <td><?php echo $product['product_price']; ?></td>
                                        <td>
                                            <button class="btn btn-outline-danger mt-2" onclick="confirmEdit('<?php echo $product['id']; ?>')">Edit</button>
                                            <button class="btn btn-danger mt-2" onclick="confirmDelete('<?php echo $product['id'];?>')">Delete</button>
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
    
    <script src="js/main.js"></script>
    <script src="js/search.js"></script>
    <script src="js/button.js"></script>

</body>
</html>
