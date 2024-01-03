<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "system_proj";

define('GOOGLE_MAPS_API_KEY', 'AIzaSyAr8QQLheQpIlMvXQfEf_0AKOULg7RhsLY');

$conn = mysqli_connect($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    
}
?>
