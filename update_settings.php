<?php
session_start();
require_once('../config/db.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['user_id'];

$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// ✅ Validate
if (empty($first_name) || empty($last_name) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit();
}

// ✅ Update basic info
$stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=? WHERE id=?");
$stmt->bind_param("sssi", $first_name, $last_name, $email, $user_id);

if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Error updating info: ' . $stmt->error]);
    exit();
}

// ✅ If changing password
if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
    if ($new_password !== $confirm_password) {
        echo json_encode(['status' => 'error', 'message' => 'New passwords do not match.']);
        exit();
    }

    $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user || !password_verify($current_password, $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect.']);
        exit();
    }

    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->bind_param("si", $hashed, $user_id);

    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating password.']);
        exit();
    }
}

// ✅ Update session vars
$_SESSION['fname'] = $first_name;
$_SESSION['lname'] = $last_name;
$_SESSION['email'] = $email;

echo json_encode(['status' => 'success', 'message' => 'Settings updated successfully.']);
exit();
?>
