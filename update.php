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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $gender = $_POST["gender"];
    $birthdate = $_POST["birthdate"];
    $address = $_POST["address"];
    $contact_number = $_POST["contact_number"];

    $sql = "UPDATE users SET firstname=?, lastname=?, gender=?, birthdate=?, address=?, contact_number=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $firstname, $lastname, $gender, $birthdate, $address, $contact_number, $id);

    if ($stmt->execute()) {
        header("location: profile.php");
        exit;
    } else {
        echo '<script>alert("Error updating profile. Please try again.");</script>';
    }

    $stmt->close();
}

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
    <title>Farmers Assistant Web Service - Update Profile</title>
    <link rel="icon" href="storage/farmer.png" type="image/x-icon">
    <link rel="stylesheet" href="style/bootstrap.min.css">
    <link rel="stylesheet" href="style/profile.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="style/icon.css">
</head>
<script src="js/google-map.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAr8QQLheQpIlMvXQfEf_0AKOULg7RhsLY&libraries=places&callback=initialize"></script>

<body>
    <header class="bg-success text-white text-center py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1><a href="index.php" class="text-decoration-none text-white">Farmers Assistant Web Service</a></h1>
            <div>
                <a href="index.php" class="btn btn-outline-light m-2"><span class="material-icons">home</span> HOME</a>
                <a href="logout.php" class="btn btn-outline-light m-2"><span class="material-icons">logout</span> Logout</a>
            </div>
        </div>
    </header>

    <nav class="navbar navbar-expand-lg bg-info text-center">
        <div class="container">
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
            <form action="<?php echo ($_SERVER["PHP_SELF"]); ?>" method="POST" onsubmit="return validateForm()">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">Edit Profile</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="firstname" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $userData['firstname']; ?>" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="lastname" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $userData['lastname']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="birthdate">Birthdate</label>
                                <input type="date" id="birthdate" name="birthdate" class="form-control" value="<?php echo $userData['birthdate']; ?>" max="2020-12-31" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="gender">Gender</label>
                                <select id="gender" name="gender" class="form-control" required>
                                    <option value="" disabled>-- Please select --</option>
                                    <option value="Male" <?php echo ($userData['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo ($userData['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="address">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?php echo $userData['address'] ?>" autocomplete="off" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="contact_number">Contact Number</label>
                                <input type="number" class="form-control" id="contact_number" name="contact_number" value="<?php echo $userData['contact_number'] ?>" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="11" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $userData['email']; ?>" disabled autocomplete="off">
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="profile.php" class="btn btn-secondary"> Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<script src="js/update.js"></script>

</html>