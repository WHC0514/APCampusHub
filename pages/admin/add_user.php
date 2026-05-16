<?php

session_start();

if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin")
{
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APCampusHub</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../../assets/css/admin/add_user.css">
</head>
<body>

    <!-- Topbar -->
    <div class="topbar profile-topbar">

        <div class="profile-topbar-left">

            <!-- Back Button -->
            <a href="user_management.php" class="back-btn">

                <img src="../../assets/icons/back.png" alt="Back" class="back-icon">

            </a>

            <!-- Page Title -->
            <h2>Add New User</h2>

        </div>

    </div>

    <div class="container">

        <div class="page-header">

            <h1>Add New User</h1>

            <p>
                Create a new APCampusHub account
            </p>

        </div>

        <form action="process_add_user.php" method="POST" enctype="multipart/form-data" class="form-card">

            <!-- Personal Information -->
            <div class="section-title">
                Personal Information
            </div>

            <div class="form-grid">

                <div class="form-group">

                    <label>Name</label>

                    <input type="text" name="name" required>

                </div>

                <div class="form-group">

                    <label>IC Number</label>

                    <input type="text" name="identical_number" required>

                </div>

                <div class="form-group">

                    <label>Email</label>

                    <input type="email" name="email" required>

                </div>

                <div class="form-group">

                    <label>Phone Number</label>

                    <input type="text" name="phone_number" required>

                </div>

                <div class="form-group">

                    <label>Gender</label>

                    <select name="gender" required>

                        <option value="" disabled selected>-- Select Gender --</option>

                        <option value="Male">Male</option>

                        <option value="Female">Female</option>

                    </select>

                </div>

                <div class="form-group">

                    <label>User Role</label>

                    <select name="role" required>

                        <option value="" disabled selected>-- Select Role --</option>

                        <option value="student">Student</option>

                        <option value="lecturer">Lecturer</option>

                        <option value="staff">Staff</option>

                        <option value="admin">Admin</option>

                    </select>

                </div>

                <div class="form-group full-width">

                    <label>Profile Photo</label>

                    <input type="file" name="profile_photo" accept="image/png, image/jpeg, image/jpg, image/webp" required>

                </div>

            </div>

            <!-- Account Information -->
            <div class="section-title">
                Account Information
            </div>

            <div class="form-grid">

                <div class="form-group">

                    <label>Username</label>

                    <input type="text" name="username" required>

                </div>

                <div class="form-group">

                    <label>Password</label>

                    <input type="password" name="password" required>

                </div>

            </div>

            <!-- Button -->
            <div class="btn-group">

                <a href="user_management.php" class="cancel-btn">
                    Cancel
                </a>

                <button type="submit" class="submit-btn">
                    Add User
                </button>

            </div>

        </form>

    </div>

</body>
</html>