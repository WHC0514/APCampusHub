<?php

session_start();
require_once("../../config/db.php");

if (isset($_POST['loginBtn']))
{
    $username = trim($_POST['username']);
    $icnumber = trim($_POST['ic_number']);

    /* Empty Check */
    if (empty($username) || empty($icnumber))
    {
        echo "<script>alert('Please fill in all fields.'); window.location.href='forget_password.php'; </script>";
        exit();
    }

    /* Find User */
    $sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if(!$stmt)
    {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    /* User Exists */
    if($result->num_rows == 1)
    {
        $user = $result->fetch_assoc();

        $userID = $user['user_id'];
        $role = $user['role'];

        /* Determine Which Table To Get Data */
        switch($role)
        {
            case "student":
                $table = "student";
                break;

            case "lecturer":
                $table = "lecturer";
                break;

            case "admin":
                $table = "admin";
                break;

            case "staff":
                $table = "staff";
                break;

            default:
                echo "<script>alert('Invalid role detected.'); window.location.href='forget_password.php'; </script>";
                exit();
        }

        /* Check IC Number */
        $verifySql = "SELECT * FROM $table WHERE user_id = ? AND identical_number = ?";
        $verifyStmt = $conn->prepare($verifySql);

        if(!$verifyStmt)
        {
            die("Prepare failed: " . $conn->error);
        }

        $verifyStmt->bind_param("is", $userID, $icnumber);
        $verifyStmt->execute();

        $verifyResult = $verifyStmt->get_result();

        /* Match Found */
        if($verifyResult->num_rows == 1)
        {
            $_SESSION['reset_user_id'] = $userID;
            header("Location: reset_password.php");
            exit();
        } else {
            echo "<script>alert('Identity Card Number does not match.'); window.location.href='forget_password.php'; </script>";
            exit();
        }
    } else {
        echo "<script>alert('Username not found.'); window.location.href='forget_password.php'; </script>";
        exit();
    }
}
?>