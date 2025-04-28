<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - ICMS Control Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/barangay_census/public/css/settings.css?v=<?php echo time(); ?>"> 
</head>
<body class="bg-gradient-to-r from-green-300 to-green-500 min-h-screen flex justify-center items-center">

    <div class="bg-white p-8 rounded-lg shadow mt-6 max-w-3xl w-full">
        <h3 class="text-2xl font-semibold text-gray-800">Account Information</h3>
        <p class="text-gray-500 text-sm mb-4">Manage your account details and preferences.</p>

        <form id="settings-form">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($_SESSION['user_id']) ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-600 text-sm font-medium">First Name</label>
                    <input type="text" name="first_name" class="w-full mt-1 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400" 
                        value="<?= isset($_SESSION['fname']) ? htmlspecialchars($_SESSION['fname']) : '' ?>" required>
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-medium">Last Name</label>
                    <input type="text" name="last_name" class="w-full mt-1 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400" 
                        value="<?= isset($_SESSION['lname']) ? htmlspecialchars($_SESSION['lname']) : '' ?>" required>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-gray-600 text-sm font-medium">Email</label>
                <input type="email" name="email" class="w-full mt-1 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400" 
                    value="<?= isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : '' ?>" required>
            </div>

            <h3 class="text-xl font-semibold text-gray-800 mt-6">Change Password</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-600 text-sm font-medium">Current Password</label>
                    <input type="password" name="current_password" class="w-full mt-1 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400">
                </div>
                <div></div>
                <div>
                    <label class="block text-gray-600 text-sm font-medium">New Password</label>
                    <input type="password" name="new_password" class="w-full mt-1 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400">
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-medium">Confirm Password</label>
                    <input type="password" name="confirm_password" class="w-full mt-1 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400">
                </div>
            </div>

            <div class="flex justify-end mt-6 space-x-4">
                <button type="reset" class="px-6 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-200">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Save Changes</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $("#settings-form").submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: "update_settings.php",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(response) {
                    alert(response.message);
                    if (response.status === "success") {
                        location.reload();
                    }
                },
                error: function(xhr) {
                    alert("Error: " + xhr.responseText);
                }
            });
        });
    });
    </script>

</body>
</html>
