<?php
$servername = "localhost"; 
$username = "root"; // Default XAMPP user
$password = ""; // Default XAMPP password (empty)
$database = "barangay_census"; // Ensure this matches your database name

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
