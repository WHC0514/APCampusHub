<?php

session_start();
require_once("../../config/db.php");

/* Admin only */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== "admin")
{
    echo "<script>
        alert('Access denied.');
        window.location.href='../auth/login.php';
    </script>";
    exit();
}

/* Validate */
if(!isset($_GET['user_id']))
{
    echo "<script>
        alert('Missing user ID.');
        window.location.href='../admin/user_management.php';
    </script>";
    exit();
}

$userID = intval($_GET['user_id']);

/* Prevent deleting own account */
if($userID == $_SESSION['user_id'])
{
    echo "<script>
        alert('You cannot delete your own account.');
        window.location.href='../admin/user_management.php';
    </script>";
    exit();
}

/* Get user data */
$stmt = $conn->prepare("SELECT * FROM user WHERE user_id = ? LIMIT 1");

$stmt->bind_param("i", $userID);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0)
{
    echo "<script>
        alert('User not found.');
        window.location.href='../admin/user_management.php';
    </script>";
    exit();
}

$user = $result->fetch_assoc();

$role = $user['role'];

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
        echo "<script>
            alert('Invalid role.');
            window.location.href='../admin/user_management.php';
        </script>";
        exit();
}

/* Get profile photo */
$stmt = $conn->prepare("
    SELECT profile_photo
    FROM $table
    WHERE user_id = ?
");

$stmt->bind_param("i", $userID);
$stmt->execute();

$data = $stmt->get_result()->fetch_assoc();

/* Delete image */
if(!empty($data['profile_photo']))
{
    $path = "../../uploads/profile_photo/" . $data['profile_photo'];

    if(file_exists($path))
    {
        unlink($path);
    }
}

/* Delete user's data */
$stmt = $conn->prepare("
    DELETE FROM $table WHERE user_id = ?");

$stmt->bind_param("i", $userID);
$stmt->execute();

/* Delete user account */
$stmt = $conn->prepare("DELETE FROM user WHERE user_id = ?");

$stmt->bind_param("i", $userID);

if(!$stmt->execute())
{
    echo "<script>
        alert('Failed to delete user.');
        window.location.href='../admin/user_management.php';
    </script>";
    exit();
}

/* Success */
echo "<script>
    alert('User deleted successfully!');
    window.location.href='../admin/user_management.php';
</script>";

exit();

?>