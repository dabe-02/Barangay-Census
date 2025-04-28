<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Unauthorized access.";
    exit();
}

include('../config/database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $query = "UPDATE users SET password = ? WHERE id = ? AND role = 'bhw'";
    $stmt = $conn->prepare($query);

    try {
        if ($stmt->execute([$new_password, $user_id])) {
            echo "Password updated successfully.";
        } else {
            echo "Failed to update password.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
