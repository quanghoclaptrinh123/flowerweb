<?php
    $host = "127.0.0.1:3307";
    $user = "root";
    $password = "";
    $dbname = "shop_db";

    $conn = new mysqli($host, $user, $password , $dbname);

    if($conn->connect_error) {
        die("Connection failed: " .$conn->connect_error);
    }
?>
