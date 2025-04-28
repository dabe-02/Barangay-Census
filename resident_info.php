<?php
// Include database connection
include('../config/db.php');

// Get resident_id from the URL parameter
if (isset($_GET['resident_id'])) {
    $resident_id = $_GET['resident_id'];

    // Fetch resident information based on resident_id
    $residentQuery = "SELECT * FROM census_data WHERE id = ?";
    $stmt = $conn->prepare($residentQuery);
    $stmt->bind_param("i", $resident_id);
    $stmt->execute();
    $residentResult = $stmt->get_result();

    if ($residentResult->num_rows > 0) {
        $resident = $residentResult->fetch_assoc();
    } else {
        echo "Resident not found.";
        exit;
    }
} else {
    echo "Resident ID is missing.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Information</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans p-6">
    <div class="container mx-auto bg-white p-6 shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold mb-4">Resident Information</h2>

        <div class="mb-4">
            <h3 class="text-xl font-semibold">Household Information</h3>
            <p><strong>Household Head:</strong> <?php echo $resident['household_head']; ?></p>
            <p><strong>Household Number:</strong> <?php echo $resident['household_number']; ?></p>
            <p><strong>Date Recorded:</strong> <?php echo date("F j, Y", strtotime($resident['date_recorded'])); ?></p>
        </div>

        <div class="mb-4">
            <h3 class="text-xl font-semibold">Sitio Information</h3>
            <p><strong>Sitio:</strong> <?php echo $resident['sitio_id']; ?></p> <!-- You can join this with Sitio table to show the Sitio name if necessary -->
        </div>

        <a href="javascript:window.history.back();" class="text-blue-500">Back to the census page</a>
    </div>
</body>
</html>
