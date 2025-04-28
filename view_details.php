<?php
// Include database connection
include('../config/db.php');

// Check if ID is set in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request. No census record specified.");
}

$census_id = $_GET['id'];

// Fetch census details
$query = "SELECT c.id, c.household_head, c.household_number, c.address, c.contact_number, c.birthdate, 
                 c.occupation, c.sitio_id, s.sitio_name, s.bhw_incharge
          FROM census_data c
          LEFT JOIN sitio s ON c.sitio_id = s.id
          WHERE c.id = ?";
          
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $census_id);
$stmt->execute();
$result = $stmt->get_result();
$census = $result->fetch_assoc();

if (!$census) {
    die("Census record not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Census Details</title>
    <link rel="stylesheet" href="/barangay_census/public/css/details.css">
</head>
<body>
    <div class="container">
        <h1>Census Record Details</h1>
        <table>
            <tr><th>Census ID:</th><td><?= htmlspecialchars($census['id']) ?></td></tr>
            <tr><th>Household Head:</th><td><?= htmlspecialchars($census['household_head']) ?></td></tr>
            <tr><th>Household Number:</th><td><?= htmlspecialchars($census['household_number']) ?></td></tr>
            <tr><th>Address:</th><td><?= htmlspecialchars($census['address']) ?></td></tr>
            <tr><th>Contact Number:</th><td><?= htmlspecialchars($census['contact_number']) ?></td></tr>
            <tr><th>Birthdate:</th><td><?= htmlspecialchars($census['birthdate']) ?></td></tr>
            <tr><th>Occupation:</th><td><?= htmlspecialchars($census['occupation']) ?></td></tr>
            <tr><th>Sitio:</th><td><?= htmlspecialchars($census['sitio_name']) ?></td></tr>
            <tr><th>Barangay Health Worker:</th><td><?= htmlspecialchars($census['bhw_incharge']) ?></td></tr>
        </table>

        <a href="census_data.php" class="btn">Back to Census Records</a>
    </div>
</body>
</html>
