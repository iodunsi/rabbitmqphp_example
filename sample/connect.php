<?php
$servername = "localhost";
$username = "it490user";
$password = "securepassword";
$dbname = "it490";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

