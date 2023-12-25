<?php
require_once "controller/config.php";

session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if (isset($_SESSION["login_as"])) {
        if ($_SESSION["login_as"] === "seller") {
            header("location: main-seller.php");
            exit;
        } elseif ($_SESSION["login_as"] === "buyer") {
            header("location: main-buyer.php");
            exit;
        }elseif ($_SESSION["login_as"] === "admin") {
            header("location: admin/index.php");
            exit;
        }else {
            header("location: banned.php");
            exit;
        }
        
        
    }
}