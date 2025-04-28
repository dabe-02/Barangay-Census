<?php
session_start();
include '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if the token is still valid
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id);
        $stmt->fetch();

        // Update password
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
        $stmt->bind_param("si", $new_password, $user_id);
        $stmt->execute();

        $_SESSION['message'] = "Password reset successful. You can now login.";
        header("Location: ../views/login.php");
        exit();
    } else {
        $_SESSION['message'] = "Invalid or expired token.";
        header("Location: ../views/forgot_password.php");
        exit();
    }
}
?>
