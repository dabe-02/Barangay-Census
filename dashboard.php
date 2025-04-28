<?php
session_start();
// Allow access for both 'admin' and 'bhw' roles
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'bhw'])) {
    header("Location: ../../public/login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/barangay_census/public/css/style.css?v=<?php echo time(); ?>">

    <!-- jQuery (Moved inside head for proper loading) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <?php
    // Include database connection
    include('../config/db.php');

    // Fetch statistics
    $residentCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM residents"))['total'];
    $householdCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM households"))['total'];
    $pendingRequests = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM document_requests WHERE status='Pending'"))['total'];
    $dataCompletion = 89; // Example percentage, update this with real calculation
    ?>

    <div class="container mt-4">
        <h2>Dashboard</h2>
        <p>Welcome to the Barangay Census System.</p>

        <div class="row">
            <!-- Residents -->
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <i class="fas fa-users fa-2x text-success"></i>
                    <h3 id="residentCount"><?= $residentCount; ?></h3>
                    <p>Total Residents</p>
                </div>
            </div>

            <!-- Households -->
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <i class="fas fa-home fa-2x text-primary"></i>
                    <h3 id="householdCount"><?= $householdCount; ?></h3>
                    <p>Total Households</p>
                </div>
            </div>

            <!-- Pending Requests -->
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <i class="fas fa-file-alt fa-2x text-warning"></i>
                    <h3 id="pendingRequests"><?= $pendingRequests; ?></h3>
                    <p>Pending Requests</p>
                </div>
            </div>

            <!-- Data Completion -->
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <i class="fas fa-chart-line fa-2x text-danger"></i>
                    <h3 id="dataCompletion"><?= $dataCompletion; ?>%</h3>
                    <p>Data Completion</p>
                </div>
            </div>
        </div>
    </div>

    <!-- AJAX Script for Real-time Dashboard Updates -->
    <script>
        function loadDashboardStats() {
            $.ajax({
                url: "fetch_dashboard.php", // Ensure this file exists and is correct
                type: "GET",
                dataType: "json",
                success: function(response) {
                    $("#residentCount").text(response.residents);
                    $("#householdCount").text(response.households);
                    $("#pendingRequests").text(response.pending_requests);
                    $("#dataCompletion").text(response.data_completion + "%");
                },
                error: function() {
                    console.error("Error loading dashboard stats.");
                }
            });
        }

        // Load stats when page loads and refresh every 30 seconds
        $(document).ready(function () {
            loadDashboardStats();
            setInterval(loadDashboardStats, 30000); // Refresh every 30 seconds
        });
    </script>

</body>
</html>
