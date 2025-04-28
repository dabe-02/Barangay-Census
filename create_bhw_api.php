<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Unauthorized access.";
    exit();
}

include('../config/database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $sitio_id = $_POST['sitio_id'];
    $role = 'bhw';

    $query = "INSERT INTO users (username, password, role, first_name, last_name, email, created_at, sitio_id) 
              VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($query);

    try {
        if ($stmt->execute([$username, $password, $role, $first_name, $last_name, $email, $sitio_id])) {
            echo "BHW account created successfully.";
        } else {
            echo "Failed to create BHW account.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
