<?php

session_start();
require_once("../../config/db.php");

/* Check Session */
if(!isset($_SESSION['reset_user_id']))
{
    header("Location: forget_password.php");
    exit();
}

if(isset($_POST['loginBtn']))
{
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);

    /* Empty Check */
    if(empty($password) || empty($confirmPassword))
    {
        echo "<script>alert('Please fill in all fields.'); window.location.href='reset_password.php'; </script>";
        exit();
    }

    /* Password Match Check */
    if($password !== $confirmPassword)
    {
        echo "<script>alert('Password and Confirm Password do not match.'); window.location.href='reset_password.php'; </script>";
        exit();
    }

    /* Hash Password */
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $userID = $_SESSION['reset_user_id'];

    /* Update Password */
    $sql = "UPDATE user SET password = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if(!$stmt)
    {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("si", $hashedPassword, $userID);

    if($stmt->execute())
    {
        /* Remove Reset Session */
        unset($_SESSION['reset_user_id']);
        echo "<script>alert('Password reset successful. Please use the new password for login next time.'); window.location.href='login.php'; </script>";
        exit();
    } else {
        echo "<script>alert('Failed to reset password. Please try again.'); window.location.href='reset_password.php'; </script>";
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>