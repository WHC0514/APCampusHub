<?php

session_start();

require_once("../../config/db.php");

if(!isset($_SESSION['user_id']))
{
    header("Location: ../auth/login.php");
    exit();
}

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


/* Filters */
$order = $_GET['order'] ?? 'desc';
$unreadOnly = isset($_GET['unread']) ? 1 : 0;

$where = "WHERE (user_id = ? OR user_id IS NULL)";

if($unreadOnly == 1)
{
    $where .= " AND is_read = 0";
}

$orderSql = ($order === "asc") ? "ASC" : "DESC";

$sql = "SELECT * FROM notification $where ORDER BY created_at $orderSql";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
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
            <a href="<?php echo $dashboardPage; ?>" class="back-btn">

                <img src="../../assets/icons/back.png" alt="Back" class="back-icon">

            </a>

            <!-- Page Title -->
            <h2>Notification</h2>

        </div>

    </div>

    <div class="container">

        <!-- Header -->
        <div class="top-section">
            <div class="title">Notifications</div>
        </div>

        <!-- Filter Bar -->
        <form method="GET" class="search-box">

            <!-- Order -->
            <select name="order" class="filter-select">
                <option value="desc" <?php if($order=='desc') echo 'selected'; ?>>
                    Newest First
                </option>
                <option value="asc" <?php if($order=='asc') echo 'selected'; ?>>
                    Oldest First
                </option>
            </select>

            <!-- Unread Toggle -->
            <label class="toggle-box">
                <input type="checkbox" name="unread" value="1"
                    <?php if($unreadOnly==1) echo 'checked'; ?>>
                Unread Only
            </label>

            <!-- Apply Button -->
            <button type="submit">Apply</button>

        </form>

        <!-- List -->
        <div class="notification-list">

        <?php if($result->num_rows == 0) { ?>
            <div class="empty-result">
                No notifications available.
            </div>
        <?php } ?>

        <?php while($n = $result->fetch_assoc()) { ?>

            <?php $isRead = ($n['is_read'] == 1); ?>

            <a href="notification_detail.php?id=<?php echo $n['notification_id']; ?>" class="notification-card-link">

                <div class="notification-card <?php echo $isRead ? 'read' : 'unread'; ?>">

                    <div class="notification-title">
                        <?php echo htmlspecialchars($n['title']); ?>
                    </div>

                    <div class="notification-date">
                        <?php echo $n['created_at']; ?>
                    </div>

                    <div class="status-badge <?php echo $isRead ? 'status-read' : 'status-unread'; ?>">
                        <?php echo $isRead ? "Read" : "Unread"; ?>
                    </div>

                </div>

            </a>

        <?php } ?>

        </div>

    </div>

</body>
</html>