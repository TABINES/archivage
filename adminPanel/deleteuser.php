<?php
include("../conf.php");
include("../database.php");
include("../functions.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["userCuid"])) {
    echo deleteUser($conn, $_POST["userCuid"]);
}