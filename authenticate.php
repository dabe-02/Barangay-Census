<?php
session_start();
require '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Fetch role from form submission

    // Use PDO to fetch user with matching username and role
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = :username AND role = :role");
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":role", $role);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Set session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($_SESSION['role'] == 'admin') {
            header("Location: ../views/admin_dashboard.php");
        } elseif ($_SESSION['role'] == 'bhw') {
            header("Location: ../views/bhw_dashboard.php");
        }
        exit();
    } else {
        // Redirect with error message
        header("Location: ../views/login.php?error=1");
        exit();
    }
}
?>
