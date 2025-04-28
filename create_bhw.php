<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>window.location.href = '../../public/login.html';</script>";
    exit();
}

include('../config/database.php');

// Fetch sitios
$sitioStmt = $conn->prepare("SELECT id, sitio_name FROM sitio");
$sitioStmt->execute();
$sitios = $sitioStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch BHW accounts
$bhwStmt = $conn->prepare("SELECT u.id, CONCAT(u.first_name, ' ', u.last_name) AS full_name, u.username, u.created_at, s.sitio_name 
                           FROM users u LEFT JOIN sitio s ON u.sitio_id = s.id WHERE u.role = 'bhw'");
$bhwStmt->execute();
$bhwAccounts = $bhwStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-md-12">
      <h2 class="mb-3">BHW Account Management</h2>
      <p>Create and manage Barangay Health Worker accounts for different sitios.</p>

      <!-- BHW Accounts Table -->
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>BHW Accounts</strong>
          <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">+ Create BHW Account</button>
        </div>
        <div class="card-body">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Assigned Sitio</th>
                <th>Created Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($bhwAccounts as $bhw): ?>
                <tr>
                  <td><?= htmlspecialchars($bhw['full_name']) ?></td>
                  <td><?= htmlspecialchars($bhw['username']) ?></td>
                  <td><?= htmlspecialchars($bhw['sitio_name']) ?></td>
                  <td><?= date('M j, Y', strtotime($bhw['created_at'])) ?></td>
                  <td>
                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#updatePasswordModal"
                            data-user-id="<?= $bhw['id'] ?>">
                      🔒
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Create Modal -->
      <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form id="createForm">
              <div class="modal-header">
                <h5 class="modal-title">Create BHW Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3"><label>First Name</label><input type="text" name="first_name" class="form-control" required></div>
                <div class="mb-3"><label>Last Name</label><input type="text" name="last_name" class="form-control" required></div>
                <div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" required></div>
                <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="mb-3">
                  <label>Assign Sitio</label>
                  <select name="sitio_id" class="form-select" required>
                    <option disabled selected>Select Sitio</option>
                    <?php foreach ($sitios as $sitio): ?>
                      <option value="<?= $sitio['id'] ?>"><?= htmlspecialchars($sitio['sitio_name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-success">Create Account</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Update Password Modal -->
      <div class="modal fade" id="updatePasswordModal" tabindex="-1" aria-labelledby="updatePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form id="updatePasswordForm">
              <input type="hidden" name="user_id" id="modalUserId">
              <div class="modal-header">
                <h5 class="modal-title">Update BHW Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label>New Password</label>
                  <input type="password" name="new_password" class="form-control" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-warning">Update Password</button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- Include Bootstrap JS and SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  // Pass user ID to modal
  var updatePasswordModal = document.getElementById('updatePasswordModal');
  updatePasswordModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var userId = button.getAttribute('data-user-id');
    document.getElementById('modalUserId').value = userId;
  });

  // Handle Create Form
  document.getElementById('createForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('../controllers/create_bhw_api.php', {
      method: 'POST',
      body: formData
    }).then(res => res.text()).then(response => {
      if (response.toLowerCase().includes('success')) {
        Swal.fire({
          icon: 'success',
          title: 'Account Created!',
          text: response,
          confirmButtonColor: '#198754'
        }).then(() => {
          location.reload();
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error Creating Account',
          text: response,
          confirmButtonColor: '#dc3545'
        });
      }
    });
  });

  // Handle Password Update
  document.getElementById('updatePasswordForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('../controllers/update_bhw_password_api.php', {
      method: 'POST',
      body: formData
    }).then(res => res.text()).then(response => {
      if (response.toLowerCase().includes('success')) {
        Swal.fire({
          icon: 'success',
          title: 'Password Updated!',
          text: response,
          confirmButtonColor: '#ffc107'
        }).then(() => {
          location.reload();
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error Updating Password',
          text: response,
          confirmButtonColor: '#dc3545'
        });
      }
    });
  });
</script>




