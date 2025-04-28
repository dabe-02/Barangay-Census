<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'bhw'])) {
    header("Location: ../../public/login.html");
    exit();
}

include('../config/database.php');

// Fetch sitios with assigned BHW
$sitioStmt = $conn->prepare("SELECT id, sitio_name, bhw_incharge FROM sitio ORDER BY sitio_name");
$sitioStmt->execute();
$sitios = [];
while ($row = $sitioStmt->fetch(PDO::FETCH_ASSOC)) {
    $sitios[$row['id']] = [
        'name' => $row['sitio_name'],
        'bhw' => $row['bhw_incharge'],
        'residents' => []
    ];
}

// Fetch census data
$censusStmt = $conn->prepare("SELECT c.id AS census_id, c.household_head AS name, c.household_number AS household,
                              COALESCE(c.date_recorded, c.created_at) AS date_recorded, c.sitio_id
                              FROM census_data c
                              LEFT JOIN sitio s ON c.sitio_id = s.id
                              ORDER BY c.sitio_id, date_recorded DESC");
$censusStmt->execute();
while ($row = $censusStmt->fetch(PDO::FETCH_ASSOC)) {
    if (isset($sitios[$row['sitio_id']])) {
        $sitios[$row['sitio_id']]['residents'][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Census Data Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/barangay_census/public/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/barangay_census/public/css/censusdata.css?v=<?php echo time(); ?>"> 
</head>
<body>
<div class="census-wrapper container-fluid mt-4">
    <h2 class="mb-3">Census Data Management</h2>
    <p>Manage and update census data records grouped by Sitio.</p>

    <!-- Search + Refresh + Add -->
    <div class="d-flex gap-2 mb-3">
        <input type="text" id="search" placeholder="Search by Name, Household, or ID..." class="form-control" style="width: 300px;">
        <button class="btn btn-primary" onclick="location.reload();">Refresh</button>
        <a href="http://localhost/BARANGAY_CENSUS/src/views/census_form.php" class="btn btn-success">+ Add Census Record</a>

    </div>

    <!-- Success Toast -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Record updated successfully!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Sitio Grouped Data (Slider Version) -->
    <div class="sitio-slider-container position-relative">
        <div class="sitio-slider">
            <?php foreach ($sitios as $sitioId => $sitio): ?>
                <div class="sitio-box-wrapper">
                    <div class="sitio-box shadow-sm">
                        <div class="box-header">
                            <strong><?= htmlspecialchars($sitio['name']) ?></strong><br>
                            <small><?= htmlspecialchars($sitio['bhw']) ?> (BHW)</small>
                        </div>
                        <div class="box-body">
                            <div class="scrollable-tbody">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Household</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($sitio['residents'])): ?>
                                        <?php foreach ($sitio['residents'] as $row): ?>
                                            <tr class="census-row">
                                                <td><?= htmlspecialchars($row['census_id']) ?></td>
                                                <td><?= htmlspecialchars($row['name']) ?></td>
                                                <td><?= htmlspecialchars($row['household']) ?></td>
                                                <td><?= date('M j, Y', strtotime($row['date_recorded'])) ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info view-details" data-id="<?= $row['census_id'] ?>" data-bs-toggle="modal" data-bs-target="#detailsModal">View</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center text-muted">No census records.</td></tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Navigation Buttons -->
        <div class="d-flex justify-content-between mt-3">
            <button id="prevSitio" class="btn btn-outline-secondary">&laquo; Previous</button>
            <button id="nextSitio" class="btn btn-outline-secondary">Next &raquo;</button>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Census Record Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- AJAX-loaded content -->
            </div>
            <div class="modal-footer">
                <button id="editBtn" class="btn btn-primary">Edit</button>
                <button id="saveBtn" class="btn btn-success" style="display: none;">Save</button>
                <button id="cancelBtn" class="btn btn-secondary" style="display: none;">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    let originalData = {};

    // View details
    $(".view-details").click(function () {
        const censusId = $(this).data("id");
        $.ajax({
            url: "fetch_census_details.php",
            type: "GET",
            data: { id: censusId },
            success: function (data) {
                $("#modalBody").html(data);
                $("#editBtn").show();
                $("#saveBtn, #cancelBtn").hide();
            }
        });
    });

    // Edit
    $("#editBtn").click(function () {
        originalData = {};
        $("#modalBody td").each(function () {
            const label = $(this).prev("th").text().trim();
            const val = $(this).text().trim();
            originalData[label] = val;

            if (label !== "Census ID:") {
                $(this).html(`<input type='text' class='form-control' id='${$(this).attr("id")}' value='${val}'>`);
            }
        });

        $("#editBtn").hide();
        $("#saveBtn, #cancelBtn").show();
    });

    // Save
    $("#saveBtn").click(function () {
        const updatedData = {
            census_id: $("#census_id").val(),
            household_head: $("#household_head input").val(),
            household_number: $("#household_number input").val(),
            address: $("#address input").val(),
            contact_number: $("#contact_number input").val(),
            birthdate: $("#birthdate input").val(),
            occupation: $("#occupation input").val(),
            sitio: $("#sitio input").val(),
            bhw_incharge: $("#bhw_incharge input").val()
        };

        $.ajax({
            url: "update_census.php",
            type: "POST",
            data: updatedData,
            success: function (response) {
                const toastEl = new bootstrap.Toast(document.getElementById('successToast'));
                toastEl.show();

                const updatedRow = $(".census-row").filter(function () {
                    return $(this).find("td").first().text() == updatedData.census_id;
                });

                updatedRow.find("td").eq(1).text(updatedData.household_head);
                updatedRow.find("td").eq(2).text(updatedData.household_number);
                updatedRow.find("td").eq(3).text(updatedData.date_recorded || 'Updated');

                $("#detailsModal").modal('hide');
            },
            error: function () {
                alert("Error updating record.");
            }
        });
    });

    // Cancel
    $("#cancelBtn").click(function () {
        $("#editBtn").show();
        $("#saveBtn, #cancelBtn").hide();
        $("#modalBody td").each(function () {
            const label = $(this).prev("th").text().trim();
            const val = originalData[label];
            if (label !== "Census ID:") {
                $(this).text(val);
            }
        });
    });

    // Search
    $("#search").on("keyup", function () {
        let value = $(this).val().toLowerCase();
        $(".census-row").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    $(".btn-close").click(function () {
        const toastEl = new bootstrap.Toast(document.getElementById('successToast'));
        toastEl.hide();
    });

    // Sitio Navigation
    let currentIndex = 0;
    const sitios = document.querySelectorAll(".sitio-box-wrapper");
    const slider = document.querySelector(".sitio-slider");

    function updateSlider() {
        const offset = -currentIndex * 100;
        slider.style.transform = `translateX(${offset}%)`;
        updateButtons();
    }

    function updateButtons() {
        document.getElementById("prevSitio").disabled = currentIndex === 0;
        document.getElementById("nextSitio").disabled = currentIndex === sitios.length - 1;
    }

    document.getElementById("prevSitio").addEventListener("click", function () {
        if (currentIndex > 0) {
            currentIndex--;
            updateSlider();
        }
    });

    document.getElementById("nextSitio").addEventListener("click", function () {
        if (currentIndex < sitios.length - 1) {
            currentIndex++;
            updateSlider();
        }
    });

    updateSlider();
});
</script>
</body>
</html>
