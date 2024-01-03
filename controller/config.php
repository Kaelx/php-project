<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "system_proj";

define('GOOGLE_MAPS_API_KEY', 'API_KEY');

$conn = mysqli_connect($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    
}
?>
