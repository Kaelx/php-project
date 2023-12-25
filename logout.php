<?php
session_start();

require_once 'controller/config.php';


$userType = isset($_SESSION["login_as"]) ? $_SESSION["login_as"] : '';
logAction($_SESSION["id"], 'has logged out', $userType);

$_SESSION = array();
session_destroy();

header("Location: index.php");
exit();

function logAction($userId, $actionType = null, $userType = null) {
    global $conn;

    $actionType = '(' . $userType . ') '. $actionType;

    $stmt = $conn->prepare("INSERT INTO logs (user_id, action_type) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $actionType);
    $stmt->execute();
    $stmt->close();
}


?>