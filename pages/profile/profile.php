<?php

session_start();

if(!isset($_SESSION['user_id']))
{
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

$userID = $_SESSION['user_id'];

$role = $_SESSION['role'];

/* Back to Page Based on Role */
switch($role)
{
    case "student":
        $dashboardPage = "../student/dashboard.php";
        break;

    case "lecturer":
        $dashboardPage = "../lecturer/dashboard.php";
        break;

    case "admin":
        $dashboardPage = "../admin/dashboard.php";
        break;

    case "staff":
        $dashboardPage = "../staff/dashboard.php";
        break;

    default:
        $dashboardPage = "../auth/login.php";
}

/* Get User Data */
$user = null;

if($role === "student")
{
    $sql = "SELECT u.username,
                   s.name,
                   s.identical_number,
                   s.email,
                   s.phone_number,
                   s.gender,
                   s.profile_photo
            FROM user u
            INNER JOIN student s ON u.user_id = s.user_id
            WHERE u.user_id = ?";
} elseif($role === "lecturer")
{
    $sql = "SELECT u.username,
                   l.name,
                   l.identical_number,
                   l.email,
                   l.phone_number,
                   l.gender,
                   l.profile_photo
            FROM user u
            INNER JOIN lecturer l ON u.user_id = l.user_id
            WHERE u.user_id = ?";
} elseif($role === "admin")
{
    $sql = "SELECT u.username,
                   a.name,
                   a.identical_number,
                   a.email,
                   a.phone_number,
                   a.gender,
                   a.profile_photo
            FROM user u
            INNER JOIN admin a ON u.user_id = a.user_id
            WHERE u.user_id = ?";
} elseif($role === "staff")
{
    $sql = "SELECT u.username,
                   st.name,
                   st.identical_number,
                   st.email,
                   st.phone_number,
                   st.gender,
                   st.profile_photo
            FROM user u
            INNER JOIN staff st ON u.user_id = st.user_id
            WHERE u.user_id = ?";
} else
{
    header("Location: ../auth/login.php");
    exit();
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

/* Default Image */
$profilePhoto = "../../uploads/profile_photo/default.png";

if(!empty($user['profile_photo']))
{
    $profilePhoto = "../../uploads/profile_photo/" . $user['profile_photo'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APCampusHub</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
</head>
<body>
    
    <!-- Topbar -->
    <div class="topbar profile-topbar">

        <div class="profile-topbar-left">

            <!-- Back Button -->
            <a href="<?php echo $dashboardPage; ?>" class="back-btn">

                <img src="../../assets/icons/back.png" alt="Back" class="back-icon">

            </a>

            <!-- Page Title -->
            <h2>My Profile</h2>

        </div>

    </div>

    <!-- Profile Content -->
    <div class="profile-page">

        <div class="profile-page-left">

            <img src="<?php echo $profilePhoto; ?>" class="profile-page-pic">

            <h1><?php echo $user['name']; ?></h1>

            <p class="username-text">
                <?php echo $user['username']; ?>
            </p>

            <div class="id-card-container">
                <div class="id-card" id="idCard">

                    <!-- Front -->
                    <div class="id-card-front">

                        <img src="../../assets/images/app-logo.png" class="id-card-logo">
                        <h2>APCampusHub ID Card</h2>

                        <p>ID: <?php echo $userID; ?></p>

                        <p>
                            Name:
                            <?php echo $user['name']; ?>
                        </p>
                    </div>

                    <div class="id-card-back">

                        <img src="../../assets/images/university-logo.png" class="university-logo">
                        <h2>Asia Pacific University</h2>
                    </div>
                </div>

            </div>
        </div>

        <div class="profile-page-right">

            <div class="info-box">

                <label>Identity Card Number</label>

                <p><?php echo $user['identical_number']; ?></p>

            </div>

            <div class="info-box">

                <label>Phone Number</label>

                <p><?php echo $user['phone_number']; ?></p>

            </div>

            <div class="info-box">

                <label>Gender</label>

                <p><?php echo $user['gender']; ?></p>

            </div>

            <!-- Change Password -->
            <a href="change_password.php" class="change-password-btn">
                Change Password
            </a>
        </div>
    </div>
</body>

<script>

/* Flip Card */
const idCard = document.getElementById("idCard");
idCard.addEventListener("click", function(){
    idCard.classList.toggle("flipped");
});

</script>
</html>