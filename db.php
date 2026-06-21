<?php
$host = "localhost";
$user = "Admin";       
$pass = "Admin@123";    
$db   = "financial_system";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

?>
