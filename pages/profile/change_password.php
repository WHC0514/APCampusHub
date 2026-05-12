<?php

session_start();

if(!isset($_SESSION['user_id']))
{
    header("Location: ../auth/login.php");
    exit();
}

$userID = $_SESSION['user_id'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APCampusHub</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../../assets/css/change_password.css">

</head>
<body>
    
    <!-- Topbar -->
    <div class="topbar profile-topbar">

        <div class="profile-topbar-left">

            <!-- Back Button -->    
            <a href="profile.php" class="back-btn">

                <img src="../../assets/icons/back.png" alt="Back" class="back-icon">
            </a>

            <!-- Page Title -->
            <h2>Change Password</h2>
        </div>
    </div>

    <!-- Content -->
    <div class="change-password-container">

        <form action="change_password_process.php" method="POST" class="change-password-box">

            <h1>Update Your Password</h1>

            <!-- Current Password -->
            <div class="input-group">

                <label>Current Password</label>
                <input type="password" name="current_password" placeholder="Please enter your current password" required>

            </div>

            <!-- New Password -->
            <div class="input-group">

                <label>New Password</label>
                <input type="text" name="new_password" placeholder="Please enter your new password" required>

            </div>

            <!-- Confirm Password -->
            <div class="input-group">

                <label>Confirm Password</label>
                <input type="text" name="confirm_password" placeholder="Please enter your confirm password" required>

            </div>

            <button type="submit" name="changePasswordBtn" class="primary-btn">
                Change Password
            </button>
        </form>
    </div>
</body>
</html>