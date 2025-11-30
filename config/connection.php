<?php
session_start();

$host = 'localhost';
$dbname = 'home_ayurveda';
$username = 'root';
$password = '';

$mysqli = mysqli_connect($host, $username, $password, $dbname);

if (!$mysqli) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($mysqli, "utf8mb4");
?>
