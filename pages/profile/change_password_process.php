<?php

session_start();

if(!isset($_SESSION['user_id']))
{
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

if(isset($_POST['changePasswordBtn']))
{
    $userID = $_SESSION['user_id'];

    $currentPassword = trim($_POST['current_password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    /* Empty Check */
    if(empty($currentPassword) || empty($newPassword) || empty($confirmPassword))
    {
        echo "<script>alert('Please fill in all fields.'); window.location.href='change_password.php'; </script>";
        exit();
    }

    /* Get Current Password From Database */
    $sql = "SELECT password FROM user WHERE user_id = ?";

    $stmt = $conn->prepare($sql);

    if(!$stmt)
    {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $userID);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows == 1)
    {
        $user = $result->fetch_assoc();

        /* Verify Current Password */
        if(password_verify($currentPassword, $user['password']))
        {
            /* Check New Password Match With Confirm Password */
            if($newPassword != $confirmPassword)
            {
                echo "<script>alert('New password and confirm password do not match.'); window.location.href='change_password.php'; </script>";
                exit();
            }

            /* Hash New Password */
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            /* Update Password */
            $updateSql = "UPDATE user SET password = ? WHERE user_id = ?";
            $updateStmt = $conn->prepare($updateSql);

            if(!$updateStmt)
            {
                die("Prepare failed: " . $conn->error);
            }

            $updateStmt->bind_param("si", $hashedPassword, $userID);

            if($updateStmt->execute())
            {
                echo "<script>alert('Password updated successfully.'); window.location.href='../auth/login.php'; </script>";
            } else {
                echo "<script>alert('Failed to update password.'); window.location.href='change_password.php'; </script>";
            }

            $updateStmt->close();
        } else {
            echo "<script>alert('Current password does not match the new password.'); window.location.href='change_password.php'; </script>";
            exit();
        }
    }

    $stmt->close();
    $conn->close();
}
?>