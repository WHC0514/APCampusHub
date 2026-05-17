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
    header("Location: ../staff/view_activity.php");
    exit();
}

$requestID = intval($_GET['id']);

/* Get request detail */
$sql = "SELECT 
            rsr.*,
            r.room_name
        FROM room_service_request rsr
        LEFT JOIN room r 
        ON rsr.room_id = r.room_id
        WHERE rsr.request_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $requestID);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0)
{
    echo "<script>
        alert('Request not found.');
        window.location.href='../staff/view_activity.php';
    </script>";
    exit();
}

$request = $result->fetch_assoc();

$sqlUser = "SELECT role FROM user WHERE user_id = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("i", $request['user_id']);
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
    $stmtName->bind_param("i", $request['user_id']);
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
    <link rel="stylesheet" href="../../assets/css/view_activity/view_request_detail.css">
</head>
<body>

    <!-- Topbar -->
    <div class="topbar profile-topbar">

        <div class="profile-topbar-left">

            <a href="../staff/view_activity.php" class="back-btn">
                <img src="../../assets/icons/back.png" class="back-icon">
            </a>

            <h2>Request Details</h2>

        </div>
    </div>

    <!-- Content -->
    <div class="detail-container">

        <div class="detail-card">

            <div class="detail-header">

                <h1>Request Details</h1>

                <?php
                    $statusClass = strtolower(str_replace(' ', '-', $request['status']));
                ?>

                <div class="status-badge status-<?php echo $statusClass; ?>">
                    <?php echo $request['status']; ?>
                </div>

            </div>

            <div class="detail-grid">

                <div class="detail-item">
                    <label>Request ID</label>
                    <p>#<?php echo $request['request_id']; ?></p>
                </div>

                <div class="detail-item">
                    <label>Booking ID</label>
                    <p><?php echo $request['booking_id']; ?></p>
                </div>

                <div class="detail-item">
                    <label>Room</label>
                    <p>
                        <?php echo $request['room_name']; ?>
                        (ID: <?php echo $request['room_id']; ?>)
                    </p>
                </div>

                <div class="detail-item">
                    <label>User ID</label>
                    <p>
                        <?php echo $userName; ?>
                        (ID: <?php echo $request['user_id']; ?>)
                    </p>
                </div>

                <div class="detail-item">
                    <label>Request Type</label>
                    <p><?php echo $request['request_type']; ?></p>
                </div>

                <div class="detail-item">
                    <label>Resource Type</label>
                    <p>
                        <?php 
                        echo !empty($request['resource_type']) 
                            ? $request['resource_type'] 
                            : "-";
                        ?>
                    </p>
                </div>

                <div class="detail-item full-width">
                    <label>Description</label>
                    <div class="description-box">
                        <?php echo nl2br(htmlspecialchars($request['description'])); ?>
                    </div>
                </div>

                <div class="detail-item">
                    <label>Created At</label>
                    <p><?php echo $request['created_at']; ?></p>
                </div>

            </div>

            <!-- Action Button -->
            <?php if($request['status'] !== "Done") { ?>

            <form method="POST" action="update_request_status.php">

                <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">

                <div class="action-section">

                    <select name="status" class="status-select">

                        <option value="Pending" <?php if($request['status']=="Pending") echo "selected"; ?>>
                            Pending
                        </option>

                        <option value="In Progress" <?php if($request['status']=="In Progress") echo "selected"; ?>>
                            In Progress
                        </option>

                        <option value="Done">
                            Done
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