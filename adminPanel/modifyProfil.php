<?php
session_start();
include("../conf.php");
include("../database.php");
include("../functions.php");

if (!isset($_SESSION['user_cuid'], $_SESSION['is_admin'], $_SESSION['user_firstname'], $_SESSION['user_email']) || $_SESSION["is_admin"] != 1) {
    header("Location: ../../login");
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["userCuid"])) {
    $_SESSION["modify_cuid"] = $_POST["userCuid"];
}