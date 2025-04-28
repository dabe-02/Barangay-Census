<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Barangay Census</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/barangay_census/public/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="row w-100">
        <div class="col-md-6 offset-md-3">
            <div class="card p-4">
                <h4 class="text-center">Reset Your Password</h4>
                <p class="text-center text-muted">Enter your registered email to receive a password reset link.</p>
                
                <?php
                if (isset($_SESSION['message'])) {
                    echo '<div class="alert alert-info">'.$_SESSION['message'].'</div>';
                    unset($_SESSION['message']);
                }
                ?>

<form action="/barangay_census/src/controllers/process_forgot_password.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
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
