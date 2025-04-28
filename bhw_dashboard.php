<?php
session_start();
// Allow access for both 'admin' and 'bhw' roles
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'bhw') {
    header("Location: ../../public/login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BHW Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/barangay_census/public/css/style.css?v=<?php echo time(); ?>"> 
</head>
<body>
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="/barangay_census/public/images/logo.png" class="sidebar-logo">
        <button class="toggle-btn" id="toggleSidebar">
            <i class="fas fa-chevron-left"></i>
        </button>
    </div>

    <ul>
        <li><a href="#" class="nav-link content-link" data-page="dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
        <li><a href="#" class="nav-link content-link" data-page="census_data.php"><i class="fas fa-database"></i> <span>Census Data</span></a></li>
        <li><a href="#" class="nav-link content-link" data-page="qr_management.php"><i class="fas fa-qrcode"></i> <span>QR Management</span></a></li>
        <li><a href="#" class="nav-link content-link" data-page="reports.php"><i class="fas fa-file-alt"></i> <span>Reports</span></a></li>
        <li><a href="#" class="nav-link content-link" data-page="households.php"><i class="fas fa-home"></i> <span>Households</span></a></li>
        <li><a href="#" class="nav-link content-link" data-page="residents.php"><i class="fas fa-users"></i> <span>Residents</span></a></li>
        <li><a href="#" class="nav-link content-link" data-page="help_support.php"><i class="fas fa-info-circle"></i> <span>Help & Support</span></a></li>
        <!-- Logout Button -->
        <li><a href="login.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content" id="main-content">
    <!-- Content for the selected page will be loaded here -->
</div>

<!-- Toast Notification -->
<?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'bhw'): ?>
    <div id="toastNotification" class="toast align-items-center text-bg-success border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Welcome BHW! You are logged in successfully.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/barangay_census/public/js/sidebar.js?v=<?php echo time(); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Show toast notification for BHW after login
    document.addEventListener('DOMContentLoaded', function () {
        if (document.getElementById('toastNotification')) {
            const toast = new bootstrap.Toast(document.getElementById('toastNotification'));
            toast.show();
        }
    });
</script>

</body>
</html>

<script>
    $(document).ready(function () {
        $(".content-link").click(function (e) {
            e.preventDefault();
            var page = $(this).data("page");

            // Load the selected page into the content area without reloading
            $("#main-content").load("views/" + page);
        });
    });
</script>
