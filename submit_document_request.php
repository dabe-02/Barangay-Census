<?php
include_once __DIR__ . "/../config/db.php";

header('Content-Type: application/json'); // Set header to return JSON

if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $complete_address = isset($_POST['complete_address']) ? trim($_POST['complete_address']) : '';
    $contact_number = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : '';
    $document_type = isset($_POST['document_type']) ? trim($_POST['document_type']) : '';
    $purpose = isset($_POST['purpose']) ? trim($_POST['purpose']) : '';

    if (empty($full_name) || empty($email) || empty($complete_address) || empty($contact_number) || empty($document_type) || empty($purpose)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    $sql = "INSERT INTO document_requests (full_name, email, complete_address, contact_number, document_type, purpose) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'SQL error: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("ssssss", $full_name, $email, $complete_address, $contact_number, $document_type, $purpose);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'document_type' => $document_type,
            'message' => 'Request submitted successfully.'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database insert error: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
