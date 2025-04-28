<?php
include '../config/db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $household_head = trim($_POST['household_head']);
    $address = $_POST['address'];
    $household_members = $_POST['household_members'];
    $contact_number = $_POST['contact_number'];
    $birthdate = $_POST['birthdate'];
    $occupation = $_POST['occupation'];
    $housing_type = $_POST['housing_type'];
    $house_ownership = $_POST['house_ownership'];
    $water_source = $_POST['water_source'];
    $electricity = $_POST['electricity'];
    $toilet = $_POST['toilet'];
    $pwd = $_POST['pwd'];
    $senior_citizen = $_POST['senior_citizen'];
    $sitio_id = $_POST['sitio_id'];

    // Validate Contact Number (must be 11 digits)
    if (!preg_match('/^\d{11}$/', $contact_number)) {
        echo json_encode(["status" => "error", "message" => "Contact number must be exactly 11 digits."]);
        exit;
    }

    // Handle income sources
    $income_source_array = isset($_POST['income_source']) ? (array)$_POST['income_source'] : [];
    if (!empty($_POST['other_income'])) {
        $income_source_array[] = $_POST['other_income'];
    }
    $income_source = !empty($income_source_array) ? implode(', ', $income_source_array) : 'None';

    // Check for duplicate household head
    $stmt_check = $conn->prepare("SELECT * FROM census_data WHERE household_head = ?");
    $stmt_check->bind_param("s", $household_head);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Household head '$household_head' already exists."]);
        exit;
    }
    $stmt_check->close();

    // Generate next household number
    $result = $conn->query("SELECT household_number FROM census_data ORDER BY household_number DESC LIMIT 1");
    $row = $result->fetch_assoc();
    $newHouseholdNumber = ($row) ? intval($row['household_number']) + 1 : 1001;

    // Insert household data
    $stmt = $conn->prepare("INSERT INTO census_data (household_head, household_number, address, household_members, contact_number, birthdate, occupation, income_source, housing_type, house_ownership, water_source, electricity, toilet, pwd, senior_citizen, sitio_id) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssssssss", $household_head, $newHouseholdNumber, $address, $household_members, $contact_number, $birthdate, $occupation, $income_source, $housing_type, $house_ownership, $water_source, $electricity, $toilet, $pwd, $senior_citizen, $sitio_id);

    if ($stmt->execute()) {
        $household_id = $stmt->insert_id;

        // Family member arrays
        $surnames      = $_POST['surname'];
        $firstnames    = $_POST['firstname'];
        $middlenames   = $_POST['middlename'];
        $positions     = $_POST['family_position'];
        $sexes         = $_POST['sex'];
        $ages          = $_POST['age'];
        $statuses      = $_POST['marital_status'];
        $sectors       = $_POST['sector'];
        $is_pwds       = $_POST['pwd_info'];
        $senior_ids    = $_POST['senior_id'];
        $educations    = $_POST['education'];
        $contacts      = $_POST['member_contact'];
        $religions     = $_POST['religion'];

        // Validate family arrays length
        $count = count($surnames);
        if ($count !== count($firstnames) || $count !== count($middlenames)) {
            echo json_encode(["status" => "error", "message" => "Mismatch in family member fields."]);
            exit;
        }

        $stmt_family = $conn->prepare("INSERT INTO family_members (household_id, surname, firstname, middlename, family_position, sex, age, marital_status, sector, is_pwd, senior_id, education, contact_number, religion) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        for ($i = 0; $i < $count; $i++) {
            $stmt_family->bind_param(
                "isssssssssssss",
                $household_id,
                $surnames[$i],
                $firstnames[$i],
                $middlenames[$i],
                $positions[$i],
                $sexes[$i],
                $ages[$i],
                $statuses[$i],
                $sectors[$i],
                $is_pwds[$i],
                $senior_ids[$i],
                $educations[$i],
                $contacts[$i],
                $religions[$i]
            );
            if (!$stmt_family->execute()) {
                echo json_encode(["status" => "error", "message" => "Error inserting family member: " . $stmt_family->error]);
                exit;
            }
        }
        $stmt_family->close();

        echo json_encode(["status" => "success", "message" => "Census data submitted successfully!", "household_number" => $newHouseholdNumber]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error inserting household data: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
