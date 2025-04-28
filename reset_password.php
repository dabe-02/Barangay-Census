<?php
session_start();
include '../config/database.php'; // Include database connection

if (!isset($_GET['token'])) {
    die("Invalid request.");
}

$token = $_GET['token'];

// Check if token is valid
$stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expiry > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    die("Invalid or expired token.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="row w-100">
        <div class="col-md-6 offset-md-3">
            <div class="card p-4">
                <h4 class="text-center">Set a New Password</h4>
                <form action="../controllers/process_reset_password.php" method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Enter new password" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Reset Password</button>
                </form>
                
                <div class="text-center mt-3">
                    <a href="login.php" class="text-muted">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
