<?php
// Define the password you want to hash
$password = 'bhwpassword123'; // Replace this with the actual password you want to hash

// Use password_hash() to hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Output the hashed password
echo "Hashed Password: " . $hashed_password;
?>
