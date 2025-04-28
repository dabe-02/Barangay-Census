<?php
include('../config/db.php');

header('Content-Type: application/json'); // Ensure JSON response

$query = "SELECT * FROM document_requests ORDER BY date_requested DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(["error" => "Query failed: " . mysqli_error($conn)]);
    exit;
}

$requests = [];
while ($row = mysqli_fetch_assoc($result)) {
    $requests[] = $row;
}

echo json_encode($requests);
?>
