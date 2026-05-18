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

/* Validate user ID */
if(!isset($_GET['user_id']))
{
    echo "<script>
        alert('Missing user ID.');
        window.location.href='../admin/user_management.php';
    </script>";
    exit();
}

$userID = intval($_GET['user_id']);

/* Get user role */
$sqlUser = "SELECT * FROM user WHERE user_id = ? LIMIT 1";

$stmt = $conn->prepare($sqlUser);
$stmt->bind_param("i", $userID);
$stmt->execute();

$userResult = $stmt->get_result();

if($userResult->num_rows == 0)
{
    echo "<script>
        alert('User not found.');
        window.location.href='../admin/user_management.php';
    </script>";
    exit();
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
        echo "<script>
            alert('Invalid role.');
            window.location.href='../admin/user_management.php';
        </script>";
        exit();
}

/* Get profile */
$sql = "
SELECT
    u.user_id,
    u.username,
    u.role,

    p.name,
    p.identical_number,
    p.email,
    p.phone_number,
    p.gender,
    p.profile_photo

FROM user u
INNER JOIN $table p
ON u.user_id = p.user_id

WHERE u.user_id = ?
LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0)
{
    echo "<script>
        alert('User profile not found.');
        window.location.href='../admin/user_management.php';
    </script>";
    exit();
}

$user = $result->fetch_assoc();

/* Profile image */
$photo = "../../uploads/profile_photo/default.png";

if(!empty($user['profile_photo']))
{
    $photo = "../../uploads/profile_photo/" . $user['profile_photo'];
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APCampusHub</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../../assets/css/user_management/edit_user.css">
</head>
<body>

<!-- Topbar -->
    <div class="topbar profile-topbar">

        <div class="profile-topbar-left">

            <!-- Back Button -->
            <a href="../admin/user_management.php" class="back-btn">

                <img src="../../assets/icons/back.png" alt="Back" class="back-icon">

            </a>

            <!-- Page Title -->
            <h2>Edit User</h2>

        </div>

    </div>

    <div class="container">

        <!-- Header -->
        <div class="page-header">

            <div class="page-header-left">

                <h1>Edit User</h1>

                <p>
                    Update user information and account details
                </p>

            </div>

            <button type="button" class="delete-btn" onclick="confirmDelete()">
                Delete User
            </button>

        </div>

        <!-- Form -->
        <form method="POST" action="process_edit_user.php" enctype="multipart/form-data" class="form-card">

            <input type="hidden" name="user_id" value="<?php echo $userID; ?>">

            <!-- Profile Photo -->
            <div class="photo-section">

                <img src="<?php echo $photo; ?>" class="profile-photo">

                <input type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp">

            </div>

            <!-- Title -->
            <div class="section-title">
                User Information
            </div>

            <!-- Form Grid -->
            <div class="form-grid">

                <!-- Name -->
                <div class="form-group">

                    <label>Name</label>

                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

                </div>

                <!-- IC -->
                <div class="form-group">

                    <label>IC Number</label>

                    <input type="text" name="identical_number" value="<?php echo htmlspecialchars($user['identical_number']); ?>" required>

                </div>

                <!-- Email -->
                <div class="form-group">

                    <label>Email</label>

                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                </div>

                <!-- Phone Number -->
                <div class="form-group">

                    <label>Phone Number</label>

                    <input type="text" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>

                </div>

                <!-- Gender -->
                <div class="form-group">

                    <label>Gender</label>

                    <select name="gender" required>

                        <option value="Male" <?php echo ($user['gender'] === "male") ? "selected" : ""; ?>>
                            Male
                        </option>

                        <option value="Female" <?php echo ($user['gender'] === "female") ? "selected" : ""; ?>>
                            Female
                        </option>

                    </select>

                </div>

                <!-- Username -->
                <div class="form-group">

                    <label>Username</label>

                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

                </div>

                <!-- User Role -->
                <div class="form-group">

                    <label>User Role</label>

                    <input type="text" value="<?php echo ucfirst($user['role']); ?>" disabled>

                </div>

                <!-- Password -->
                <div class="form-group">

                    <label>New Password (Optional)</label>

                    <input type="password" name="password" placeholder="Leave blank to keep current password">

                </div>

            </div>

            <!-- Buttons -->
            <div class="btn-group">

                <a href="../admin/user_management.php" class="cancel-btn">
                    Cancel
                </a>

                <button type="submit" class="submit-btn">
                    Save Changes
                </button>

            </div>

        </form>

    </div>

<script>

function confirmDelete()
{
    if(confirm("Are you sure you want to delete this user?"))
    {
        window.location.href = "delete_user.php?user_id=<?php echo $userID; ?>";
    }
}

</script>

</body>
</html>