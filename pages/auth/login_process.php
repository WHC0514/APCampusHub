<?php

session_start();
require_once("../../config/db.php");

/* Initial Attempts */
if(!isset($_SESSION['login_attempts']))
{
    $_SESSION['login_attempts'] = 0;
}

/* Initial Lock Timer */
if(!isset($_SESSION['lock_time'])) {
    $_SESSION['lock_time'] = 0;
}

/* Check Cooldown for Lock Timer */
if($_SESSION['lock_time'] > time())
{
    $remaining = $_SESSION['lock_time'] - time();

    echo "<script>alert('Too many attempts. Please wait $remaining seconds.'); window.location.href='login.php'; </script>";
    exit();
}

if(isset($_POST['loginBtn']))
{
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if(empty($username) || empty($password)) {
        echo "<script>alert('Please fill in all fields.'); window.location.href='login.php';</script>";
        exit();
    }

    /* Get User Data */
    $sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if(!$stmt){
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    /* User Found */
    if($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        /* Check Password */
        if(password_verify($password, $user['password'])) {
            /* Reset Everything */    
            $_SESSION['login_attempts'] = 0;
            $_SESSION['lock_time'] = 0;

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            switch($user['role']) {
                case "student":
                    header("Location: ../student/dashboard.php");
                    break;
                
                case "lecturer":
                    header("Location: ../lecturer/dashboard.php");
                    break;

                case "admin":
                    header("Location: ../admin/dashboard.php");
                    break;

                case "staff":
                    header("Location: ../staff/dashboard.php");
                    break;

                default:
                    echo "<script>alert('Invalid role'); window.location.href='login.php';</script>";
                    exit();
            }
        } else {
            /* Wrong Password */
            $_SESSION['login_attempts']++;

            if($_SESSION['login_attempts'] >= 3) {
                $_SESSION['login_attempts'] = 0;
                $_SESSION['lock_time'] = time() + 10;

                echo "<script>alert('Too many failed attempts. Please wait 10 seconds.'); window.location.href='login.php'; </script>";
                exit();
            }

            $remain = 3 - $_SESSION['login_attempts'];

            echo "<script>alert('Wrong password. Attempts left: $remain'); window.location.href='login.php';</script>";
            exit();
        }
    } else {
        /* User Not Found */
        $_SESSION['login_attempts']++;

        if($_SESSION['login_attempts'] >= 3) {
                $_SESSION['login_attempts'] = 0;
                $_SESSION['lock_time'] = time() + 10;

                echo "<script>alert('Too many failed attempts. Please wait 10 seconds.'); window.location.href='login.php'; </script>";
                exit();
            }

        $remain = 3 - $_SESSION['login_attempts'];

        echo "<script>alert('User not found. Attempts left: $remain'); window.location.href='login.php';</script>";
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>