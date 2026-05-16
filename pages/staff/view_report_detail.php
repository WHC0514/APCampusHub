<?php

session_start();
require_once("../../config/db.php");

/* Staff only */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== "staff")
{
    header("Location: ../auth/login.php");
    exit();
}

/* Validate ID */
if(!isset($_GET['id']))
{
    header("Location: view_activity.php");
    exit();
}

$reportID = intval($_GET['id']);

/* Get report detail */
$sql = "SELECT 
            rir.*,
            r.room_name
        FROM room_issue_report rir
        LEFT JOIN room r 
        ON rir.room_id = r.room_id
        WHERE rir.report_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $reportID);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0)
{
    echo "<script>
        alert('Report not found.');
        window.location.href='view_activity.php';
    </script>";
    exit();
}

$report = $result->fetch_assoc();

$sqlUser = "SELECT role FROM user WHERE user_id = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("i", $report['user_id']);
$stmtUser->execute();
$userResult = $stmtUser->get_result();

$userRole = null;
$userName = "Unknown User";

if($userResult->num_rows > 0)
{
    $userRow = $userResult->fetch_assoc();
    $userRole = $userRow['role'];
}

if($userRole === "student")
{
    $sqlName = "SELECT name FROM student WHERE student_id = ?";
}
elseif($userRole === "lecturer")
{
    $sqlName = "SELECT name FROM lecturer WHERE lecturer_id = ?";
}
else
{
    $sqlName = null;
}

if($sqlName)
{
    $stmtName = $conn->prepare($sqlName);
    $stmtName->bind_param("i", $report['user_id']);
    $stmtName->execute();

    $nameResult = $stmtName->get_result();

    if($nameResult->num_rows > 0)
    {
        $nameRow = $nameResult->fetch_assoc();
        $userName = $nameRow['name'];
    }
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
    <link rel="stylesheet" href="../../assets/css/staff/view_report_detail.css">
</head>
<body>

    <!-- Topbar -->
    <div class="topbar profile-topbar">

        <div class="profile-topbar-left">

            <a href="view_activity.php" class="back-btn">
                <img src="../../assets/icons/back.png" class="back-icon">
            </a>

            <h2>Report Details</h2>

        </div>
    </div>

    <div class="detail-container">

        <div class="detail-card">

            <div class="detail-header">

                <h1>Report Details</h1>

                <?php 
                    $statusClass = strtolower(str_replace(' ', '-', $report['status']));
                ?>

                <div class="status-badge status-<?php echo $statusClass; ?>">
                    <?php echo $report['status']; ?>
                </div>

            </div>

            <div class="detail-grid">

                <div class="detail-item">
                    <label>Report ID</label>
                    <p>#<?php echo $report['report_id']; ?></p>
                </div>

                <div class="detail-item">
                    <label>Room</label>
                    <p>
                        <?php echo $report['room_name']; ?>
                        (ID: <?php echo $report['room_id']; ?>)
                    </p>
                </div>

                <div class="detail-item">
                    <label>Booking ID</label>
                    <p><?php echo $report['booking_id']; ?></p>
                </div>

                <div class="detail-item">
                    <label>User ID</label>
                    <p>
                        <?php echo $userName; ?>
                        (ID: <?php echo $report['user_id']; ?>)
                    </p>
                </div>

                <div class="detail-item">
                    <label>Issue Type</label>
                    <p><?php echo $report['issue_type']; ?></p>
                </div>

                <div class="detail-item">
                    <label>Severity</label>
                    <p><?php echo $report['severity']; ?></p>
                </div>

                <div class="detail-item full-width">
                    <label>Description</label>
                    <div class="description-box">
                        <?php echo nl2br(htmlspecialchars($report['description'])); ?>
                    </div>
                </div>

                <div class="detail-item">
                    <label>Created At</label>
                    <p><?php echo $report['created_at']; ?></p>
                </div>

            </div>

            <!-- Update Status -->
            <?php if($report['status'] !== "Resolved") { ?>

            <form method="POST" action="update_report_status.php">

                <input type="hidden" name="report_id" value="<?php echo $report['report_id']; ?>">

                <div class="action-section">

                    <select name="status" class="status-select">

                        <option value="Pending" <?php if($report['status']=="Pending") echo "selected"; ?>>
                            Pending
                        </option>

                        <option value="In Progress" <?php if($report['status']=="In Progress") echo "selected"; ?>>
                            In Progress
                        </option>

                        <option value="Resolved">
                            Resolved
                        </option>

                    </select>

                    <button type="submit" class="update-btn">
                        Update Status
                    </button>

                </div>

            </form>

            <?php } ?>

        </div>

    </div>

</body>
</html>