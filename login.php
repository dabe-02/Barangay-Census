<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Barangay Census</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="/barangay_census/public/css/login.css?v=<?php echo time(); ?>">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <!-- Loading Screen -->
  <div id="loadingScreen">
    <div class="logo-container">
      <div class="spinner-ring"></div>
      <img src="/barangay_census/public/images/logo.png" class="loading-logo" alt="Loading">
    </div>
  </div>

  <div class="container-fluid login-wrapper d-flex align-items-center justify-content-center">
  <div class="row bg-white rounded-4 overflow-hidden shadow-lg w-100 mx-2" style="max-width: 1100px;">
    
    <!-- LEFT PANEL -->
    <div class="col-md-6 p-5 left-panel">
      <h2 class="fw-bold text-primary mb-2">QR Data Collection</h2>
      <h5 class="text-primary">Barangay San Juan, Tiaong, Quezon</h5>
      <p class="text-muted mt-3 mb-4">A modern approach to census data collection and management using QR code technology.</p>

      <div class="row g-3">
        <div class="col-6">
          <div class="icon-box">
            <img src="/barangay_census/public/images/qr.png">
            <h6>QR Registration</h6>
            <p class="text-muted small">Quick and accurate household registration</p>
          </div>
        </div>
        <div class="col-6">
          <div class="icon-box">
            <img src="/barangay_census/public/images/map.png">
            <h6>Sitio Management</h6>
            <p class="text-muted small">Organized by geographical areas</p>
          </div>
        </div>
        <div class="col-6">
          <div class="icon-box">
            <img src="/barangay_census/public/images/residents.png">
            <h6>Resident Tracking</h6>
            <p class="text-muted small">Comprehensive resident information</p>
          </div>
        </div>
        <div class="col-6">
          <div class="icon-box">
            <img src="/barangay_census/public/images/housenum.png">
            <h6>House Numbering</h6>
            <p class="text-muted small">Systematic household identification</p>
          </div>
        </div>
      </div>
    </div>


      <!-- Right Column -->
      <div class="col-md-6 form-panel bg-white p-5">
        <div class="text-center mb-4">
          <img src="/barangay_census/public/images/logo.png" alt="Logo" class="barangay-logo">
          <h4 class="fw-bold mt-3">Welcome Back</h4>
          <p class="text-muted">Sign in to continue</p>
        </div>

        <!-- Error Message -->
        <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
          <div class="alert alert-danger" role="alert">
            Invalid credentials or role. Please try again.
          </div>
        <?php endif; ?>

        <!-- Role Toggle -->
        <div class="d-flex justify-content-center mb-3 role-toggle">
          <div class="role-tab active" id="adminTab">Admin</div>
          <div class="role-tab" id="bhwTab">BHW</div>
        </div>

        <!-- Login Form -->
        <form action="../controllers/authenticate.php" method="POST" id="loginForm">
          <input type="hidden" name="role" id="role" value="admin">

          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Enter username" required>
          </div>

          <div class="mb-3 position-relative">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
            <span class="toggle-password" onclick="togglePassword()">👁</span>
          </div>

          <button type="submit" class="btn btn-success w-100">Login</button>
        </form>

        <div class="text-center mt-3">
          <a href="../views/forgot_password.php" class="text-muted">Forgot Password?</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    const adminTab = document.getElementById('adminTab');
    const bhwTab = document.getElementById('bhwTab');
    const roleInput = document.getElementById('role');
    const loadingScreen = document.getElementById('loadingScreen');
    const form = document.getElementById('loginForm');

    adminTab.addEventListener('click', () => {
      adminTab.classList.add('active');
      bhwTab.classList.remove('active');
      roleInput.value = 'admin';
    });

    bhwTab.addEventListener('click', () => {
      bhwTab.classList.add('active');
      adminTab.classList.remove('active');
      roleInput.value = 'bhw';
    });

    form.addEventListener('submit', function(e) {
      e.preventDefault();
      loadingScreen.style.display = 'flex';
      setTimeout(() => form.submit(), 1500);
    });

    // Toggle password visibility
    function togglePassword() {
      const passInput = document.getElementById('password');
      passInput.type = passInput.type === 'password' ? 'text' : 'password';
    }
  </script>
</body>
</html>  