<?php

session_start();
require_once("../../config/db.php");

date_default_timezone_set("Asia/Kuala_Lumpur");

/* Admin only */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== "admin")
{
    echo "<script>
        alert('Access denied.');
        window.location.href='../auth/login.php';
    </script>";
    exit();
}

function errorBack($msg)
{
    echo "<script>
        alert('$msg');
        window.history.back();
    </script>";
    exit();
}

/* Validate */
if(!isset($_POST['user_id']))
{
    errorBack("Invalid request.");
}

$userID = intval($_POST['user_id']);

$name = trim($_POST['name'] ?? '');
$ic = trim($_POST['identical_number'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone_number'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

/* Required */
if(empty($name) || empty($ic) || empty($email) || empty($phone) || empty($gender) || empty($username))
{
    errorBack("Please fill in all required fields.");
}

/* Email validation */
$pattern = "/^[a-zA-Z0-9._%+-]+@mail\.apu\.edu\.my$/";

if(!preg_match($pattern, $email))
{
    errorBack("Invalid email format.");
}

/* Get current user */
$stmt = $conn->prepare("SELECT * FROM user WHERE user_id = ? LIMIT 1");

$stmt->bind_param("i", $userID);
$stmt->execute();

$userResult = $stmt->get_result();

if($userResult->num_rows == 0)
{
    errorBack("User not found.");
}

$userData = $userResult->fetch_assoc();

$role = $userData['role'];

/* Detect table */
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
        errorBack("Invalid role.");
}

/* Check duplicate username */
$stmt = $conn->prepare("SELECT user_id FROM user WHERE username = ? AND user_id != ?");

$stmt->bind_param("si", $username, $userID);
$stmt->execute();

if($stmt->get_result()->num_rows > 0)
{
    errorBack("Username already exists.");
}

/* Get current photo */
$stmt = $conn->prepare("SELECT profile_photo FROM $table WHERE user_id = ? LIMIT 1");

$stmt->bind_param("i", $userID);
$stmt->execute();

$currentData = $stmt->get_result()->fetch_assoc();

$profilePhoto = $currentData['profile_photo'];

/* Upload new photo */
if(isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0)
{
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

    $tmp = $_FILES['profile_photo']['tmp_name'];

    $mime = mime_content_type($tmp);

    if(!in_array($mime, $allowedTypes))
    {
        errorBack("Only image files are allowed.");
    }

    /* Delete old image */
    if(!empty($profilePhoto))
    {
        $oldPath = "../../uploads/profile_photo/" . $profilePhoto;

        if(file_exists($oldPath))
        {
            unlink($oldPath);
        }
    }

    $ext = strtolower(
        pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION)
    );

    $newName = uniqid("user_", true) . "." . $ext;

    $path = "../../uploads/profile_photo/" . $newName;

    if(!move_uploaded_file($tmp, $path))
    {
        errorBack("Failed to upload image.");
    }

    $profilePhoto = $newName;
}

/* Update username */
$stmt = $conn->prepare("UPDATE user SET username = ? WHERE user_id = ?");

$stmt->bind_param("si", $username, $userID);

if(!$stmt->execute())
{
    errorBack("Failed to update username.");
}

/* Update password if entered */
if(!empty($password))
{
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE user SET password = ? WHERE user_id = ?");

    $stmt->bind_param("si", $hashedPassword, $userID);

    $stmt->execute();
}

/* Update profile table */
$stmt = $conn->prepare("UPDATE $table SET
        name = ?,
        identical_number = ?,
        email = ?,
        phone_number = ?,
        gender = ?,
        profile_photo = ?
    WHERE user_id = ?
");

$stmt->bind_param("ssssssi", $name, $ic, $email, $phone, $gender, $profilePhoto, $userID);

if(!$stmt->execute())
{
    errorBack("Failed to update user.");
}

/* Success */
echo "<script>
    alert('User updated successfully!');
    window.location.href='../admin/user_management.php';
</script>";

exit();

?>