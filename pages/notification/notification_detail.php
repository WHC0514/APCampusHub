<?php

session_start();

require_once("../../config/db.php");

if(!isset($_SESSION['user_id']))
{
    header("Location: ../auth/login.php");
    exit();
}

$userID = $_SESSION['user_id'];

if(!isset($_GET['id']))
{
    header("Location: notification.php");
    exit();
}

$notificationID = intval($_GET['id']);

/* Get notification (must belong to user OR global) */
$sql = "SELECT * FROM notification WHERE notification_id = ? AND (user_id = ? OR user_id IS NULL)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $notificationID, $userID);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0)
{
    echo "<script>
        alert('Notification not found');
        window.location.href='notification.php';
    </script>";
    exit();
}

$notif = $result->fetch_assoc();

/* Mark as read (ONLY if unread) */
if($notif['is_read'] == 0)
{
    $update = $conn->prepare("UPDATE notification SET is_read = 1 WHERE notification_id = ?");
    $update->bind_param("i", $notificationID);
    $update->execute();
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
    <link rel="stylesheet" href="../../assets/css/notification/notification.css">
</head>
<body>

    <!-- Topbar -->
    <div class="topbar profile-topbar">

        <div class="profile-topbar-left">

            <!-- Back Button -->
            <a href="notification.php" class="back-btn">

                <img src="../../assets/icons/back.png" alt="Back" class="back-icon">

            </a>

            <!-- Page Title -->
            <h2>Notification</h2>

        </div>

    </div>

    <div class="container">

        <div class="top-section">
            <div class="title">Notification Detail</div>
        </div>

        <div class="notification-detail-box">

            <div class="detail-title">
                <?php echo htmlspecialchars($notif['title']); ?>
            </div>

            <div class="detail-meta">
                <?php echo $notif['created_at']; ?>
            </div>

            <div class="detail-message">
                <?php echo nl2br(htmlspecialchars($notif['message'])); ?>
            </div>

        </div>

    </div>

</body>
</html>