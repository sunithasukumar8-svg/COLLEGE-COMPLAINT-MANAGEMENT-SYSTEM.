<?php

$host = "db01.dbhost.dev";
$port = 5051;
$username = "user_44zxqnzj3";
$password = "p44zxqnzj3";
$database = "db_44zxqnzj3";

try {
    $conn = new PDO(
        "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>