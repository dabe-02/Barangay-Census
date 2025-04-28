<?php
include('../config/db.php');

// Check if 'id' is passed in the URL and is valid
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p>Error: Invalid request.</p>";
    exit;
}

$census_id = $_GET['id'];

// Query to get census data
$query = "SELECT c.id, c.household_head, c.household_number, c.address, c.contact_number, c.birthdate, 
                 c.occupation, c.sitio_id, s.sitio_name, s.bhw_incharge
          FROM census_data c
          LEFT JOIN sitio s ON c.sitio_id = s.id
          WHERE c.id = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo "<p>Error preparing the query: " . $conn->error . "</p>";
    exit;
}

$stmt->bind_param("i", $census_id);
$stmt->execute();
$result = $stmt->get_result();
$census = $result->fetch_assoc();

if (!$census) {
    echo "<p>Error: Census record not found for ID: $census_id.</p>";
    exit;
}
?>

<!-- Hidden input to store the census ID -->
<input type="hidden" id="census_id" value="<?= htmlspecialchars($census['id']) ?>">

<table class="table table-bordered">
    <tr><th>Census ID:</th><td><?= htmlspecialchars($census['id']) ?></td></tr>
    <tr><th>Household Head:</th><td id="household_head"><?= htmlspecialchars($census['household_head']) ?></td></tr>
    <tr><th>Household Number:</th><td id="household_number"><?= htmlspecialchars($census['household_number']) ?></td></tr>
    <tr><th>Address:</th><td id="address"><?= htmlspecialchars($census['address']) ?></td></tr>
    <tr><th>Contact Number:</th><td id="contact_number"><?= htmlspecialchars($census['contact_number']) ?></td></tr>
    <tr><th>Birthdate:</th><td id="birthdate"><?= htmlspecialchars($census['birthdate']) ?></td></tr>
    <tr><th>Occupation:</th><td id="occupation"><?= htmlspecialchars($census['occupation']) ?></td></tr>
    <tr><th>Sitio:</th><td id="sitio"><?= htmlspecialchars($census['sitio_name']) ?></td></tr>
    <tr><th>Barangay Health Worker:</th><td id="bhw_incharge"><?= htmlspecialchars($census['bhw_incharge']) ?></td></tr>
</table>

<!-- Family Member Information Table -->
<h4 class="mt-4">Family Member Information</h4>
<div class="table-responsive">
    <table class="table table-bordered align-middle text-center" id="familyTable">
        <thead class="table-success">
            <tr>
                <th>Surname</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Family Position</th>
                <th>Sex</th>
                <th>Age</th>
                <th>Marital Status</th>
                <th>Sector</th>
                <th>PWD (If Yes, Specify)</th>
                <th>Senior ID No.</th>
                <th>Education</th>
                <th>Contact No.</th>
                <th>Religion</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Fetch family member details based on census_id -->
            <?php
            $familyQuery = "SELECT * FROM family_members WHERE census_id = ?";
            $familyStmt = $conn->prepare($familyQuery);
            if (!$familyStmt) {
                echo "<p>Error preparing the family query: " . $conn->error . "</p>";
                exit;
            }

            $familyStmt->bind_param("i", $census_id);
            $familyStmt->execute();
            $familyResult = $familyStmt->get_result();

            if ($familyResult->num_rows == 0) {
                echo "<tr><td colspan='14'>No family members found for this census record.</td></tr>";
            } else {
                while ($family = $familyResult->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><input name='surname[]' class='form-control' value='" . htmlspecialchars($family['surname']) . "'></td>";
                    echo "<td><input name='firstname[]' class='form-control' value='" . htmlspecialchars($family['firstname']) . "'></td>";
                    echo "<td><input name='middlename[]' class='form-control' value='" . htmlspecialchars($family['middlename']) . "'></td>";
                    echo "<td><input name='family_position[]' class='form-control' value='" . htmlspecialchars($family['family_position']) . "'></td>";
                    echo "<td><select name='sex[]' class='form-select'>";
                    echo "<option value='M'" . ($family['sex'] == 'M' ? ' selected' : '') . ">M</option>";
                    echo "<option value='F'" . ($family['sex'] == 'F' ? ' selected' : '') . ">F</option>";
                    echo "</select></td>";
                    echo "<td><input type='number' name='age[]' class='form-control' value='" . htmlspecialchars($family['age']) . "'></td>";
                    echo "<td><select name='marital_status[]' class='form-select'>";
                    echo "<option value='Single'" . ($family['marital_status'] == 'Single' ? ' selected' : '') . ">Single</option>";
                    echo "<option value='Married'" . ($family['marital_status'] == 'Married' ? ' selected' : '') . ">Married</option>";
                    echo "<option value='Widowed'" . ($family['marital_status'] == 'Widowed' ? ' selected' : '') . ">Widowed</option>";
                    echo "<option value='Divorced'" . ($family['marital_status'] == 'Divorced' ? ' selected' : '') . ">Divorced</option>";
                    echo "</select></td>";
                    echo "<td><input name='sector[]' class='form-control' value='" . htmlspecialchars($family['sector']) . "'></td>";
                    echo "<td><input name='pwd_info[]' class='form-control' value='" . htmlspecialchars($family['pwd_info']) . "'></td>";
                    echo "<td><input name='senior_id[]' class='form-control' value='" . htmlspecialchars($family['senior_id']) . "'></td>";
                    echo "<td><input name='education[]' class='form-control' value='" . htmlspecialchars($family['education']) . "'></td>";
                    echo "<td><input name='member_contact[]' class='form-control' value='" . htmlspecialchars($family['member_contact']) . "'></td>";
                    echo "<td><input name='religion[]' class='form-control' value='" . htmlspecialchars($family['religion']) . "'></td>";
                    echo "<td><button type='button' class='btn btn-danger btn-sm removeRow'>Remove</button></td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
</div>
