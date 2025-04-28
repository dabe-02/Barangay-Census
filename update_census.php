<?php
include('../config/db.php');

if (!isset($_POST['census_id']) || empty($_POST['census_id'])) {
    echo "Census ID missing";
    exit();
}

$census_id = $_POST['census_id'];
$household_head = $_POST['household_head'];
$household_number = $_POST['household_number'];
$address = $_POST['address'];
$contact_number = $_POST['contact_number'];
$birthdate = $_POST['birthdate'];
$occupation = $_POST['occupation'];
$sitio_name = $_POST['sitio'];
$bhw_incharge = $_POST['bhw_incharge'];

// Fetch sitio ID from sitio name
$sitio_query = "SELECT id FROM sitio WHERE sitio_name = ?";
$stmt_sitio = $conn->prepare($sitio_query);
$stmt_sitio->bind_param("s", $sitio_name);
$stmt_sitio->execute();
$sitio_result = $stmt_sitio->get_result();
$sitio_data = $sitio_result->fetch_assoc();
$sitio_id = $sitio_data ? $sitio_data['id'] : null;

if (!$sitio_id) {
    echo "Invalid sitio name.";
    exit;
}

// Update the record
$query = "UPDATE census_data SET 
          household_head=?, household_number=?, address=?, contact_number=?, 
          birthdate=?, occupation=?, sitio_id=? 
          WHERE id=?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ssssssii", $household_head, $household_number, $address, $contact_number,
                  $birthdate, $occupation, $sitio_id, $census_id);

if ($stmt->execute()) {
    echo "Update successful";
} else {
    echo "Error updating record";
}
