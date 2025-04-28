<?php
require_once '../config/db.php'; // Ensure this file has the correct PDO connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (!empty($email)) {
        try {
            // Establish a PDO connection
            $pdo = new PDO("mysql:host=localhost;dbname=barangay_census", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare and execute the statement
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            // Fetch the user data
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // User exists, process password reset (send email or generate reset token)
                echo "Password reset instructions have been sent.";
            } else {
                echo "No account found with that email.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Please enter a valid email.";
    }
}
?>
s