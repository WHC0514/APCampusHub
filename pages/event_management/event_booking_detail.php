<?php

session_start();

date_default_timezone_set("Asia/Kuala_Lumpur");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

function createNotification($conn, $userID, $title, $message)
{
    $stmt = $conn->prepare("INSERT INTO notification (user_id, title, message, is_read, created_at)
        VALUES (?, ?, ?, 0, NOW())");

    $stmt->bind_param("iss", $userID, $title, $message);
    $stmt->execute();
}

if(!isset($_GET['booking_id'])) {
    header("Location: manage_request.php");
    exit();
}

$bookingID = intval($_GET['booking_id']);

/* Get booking data */
$stmt = $conn->prepare("SELECT vb.*,
           ev.venue_name,
           ev.cover_image,
           ev.description AS venue_description,
           s.name AS student_name,
           l.name AS lecturer_name,
           u.role
    FROM venue_booking vb
    JOIN event_venue ev ON vb.venue_id = ev.venue_id
    JOIN user u ON vb.user_id = u.user_id
    LEFT JOIN student s ON vb.user_id = s.user_id
    LEFT JOIN lecturer l ON vb.user_id = l.user_id
    WHERE vb.booking_id = ?
    LIMIT 1");
$stmt->bind_param("i", $bookingID);
$stmt->execute();
$b = $stmt->get_result()->fetch_assoc();

if(!$b) {
    header("Location: manage_event_requests.php");
    exit();
}

/* Handle approved */
if(isset($_POST['action'])) {
    $action = $_POST['action'];

    if($action === 'approve') {
        $stmt = $conn->prepare("UPDATE venue_booking SET booking_status = 'Approved' WHERE booking_id = ? AND booking_status = 'Pending'");
        $stmt->bind_param("i", $bookingID);
        $stmt->execute();

         if($stmt->affected_rows > 0)
        {
            $title = "Event Approved";
            $message = "Your event booking for " . $b['venue_name'] . " has been approved.";

            createNotification($conn, $b['user_id'], $title, $message);
        }

        echo "<script>
            alert('Booking approved successfully.');
            window.location.href = 'manage_request.php';
        </script>";
        exit();

        /* Handle rejected */
    } elseif($action === 'reject') {
        $stmt = $conn->prepare("UPDATE venue_booking SET booking_status = 'Rejected' WHERE booking_id = ? AND booking_status = 'Pending'");
        $stmt->bind_param("i", $bookingID);
        $stmt->execute();

        if($stmt->affected_rows > 0)
        {
            $title = "Event Rejected";
            $message = "Your event booking for " . $b['venue_name'] . " has been rejected.";

            createNotification($conn, $b['user_id'], $title, $message);
        }

        echo "<script>
            alert('Booking rejected.');
            window.location.href = 'manage_request.php';
        </script>";
        exit();
    }
}

/* Get resource request */
$stmtRes = $conn->prepare("SELECT * FROM event_resource_request WHERE booking_id = ? ORDER BY created_at ASC");
$stmtRes->bind_param("i", $bookingID);
$stmtRes->execute();
$resourceResult = $stmtRes->get_result();

$image = "../../uploads/event/default-venue.jpg";
if(!empty($b['cover_image'])) {
    $image = "../../uploads/event/" . $b['cover_image'];
}

$requesterName = $b['role'] === 'lecturer'
    ? ($b['lecturer_name'] ?? 'Unknown')
    : ($b['student_name'] ?? 'Unknown');

switch($b['booking_status']) {
    case 'Approved': $statusClass = "status-completed"; break;
    case 'Rejected': $statusClass = "status-canceled";  break;
    default:         $statusClass = "status-pending";
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
    <link rel="stylesheet" href="../../assets/css/event_management/event_booking_detail.css">
</head>
<body>

    <!-- Topbar -->
    <div class="topbar profile-topbar">
        <div class="profile-topbar-left">
            <a href="manage_request.php" class="back-btn">
                <img src="../../assets/icons/back.png" class="back-icon">
            </a>
            <h2>Booking Detail</h2>
        </div>
    </div>

    <div class="container">

        <!-- Venue Info -->
        <div class="detail-venue-card">
            <img src="<?php echo $image; ?>" class="detail-venue-img" alt="venue">
            <div class="detail-venue-info">
                <h2><?php echo htmlspecialchars($b['venue_name']); ?></h2>
                <p><?php echo htmlspecialchars($b['venue_description']); ?></p>
            </div>
        </div>

        <!-- Booking Info -->
        <div class="detail-section">
            <h3>Booking Information</h3>

            <div class="detail-row">
                <span class="detail-label">Requested By</span>
                <span class="detail-value">
                    <?php echo htmlspecialchars($requesterName); ?>
                    (<?php echo ucfirst($b['role']); ?>)
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Start</span>
                <span class="detail-value">
                    <?php echo date("D, d M Y g:i A", strtotime($b['start_time'])); ?>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">End</span>
                <span class="detail-value">
                    <?php echo date("D, d M Y g:i A", strtotime($b['end_time'])); ?>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Submitted</span>
                <span class="detail-value">
                    <?php echo date("d M Y, g:i A", strtotime($b['created_at'])); ?>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="booking-status <?php echo $statusClass; ?>">
                    <?php echo $b['booking_status']; ?>
                </span>
            </div>
        </div>

        <!-- Event description -->
        <?php if(!empty($b['description'])): ?>
        <div class="detail-section">
            <h3>Event Description</h3>
            <p class="detail-desc"><?php echo htmlspecialchars($b['description']); ?></p>
        </div>
        <?php endif; ?>

        <!-- Resource Requests -->
        <div class="detail-section">
            <h3>Resource Requests</h3>

            <?php if($resourceResult->num_rows > 0): ?>
                <div class="resource-list">
                <?php while($r = $resourceResult->fetch_assoc()): ?>
                    <div class="resource-item">
                        <span class="resource-type">
                            <?php echo htmlspecialchars($r['resource_type']); ?>
                        </span>
                        <?php if(!empty($r['description'])): ?>
                            <span class="resource-desc">
                                <?php echo htmlspecialchars($r['description']); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="no-resource">No resource requests for this booking.</p>
            <?php endif; ?>
        </div>

        <!-- Action Button -->
        <?php if($b['booking_status'] === 'Pending'): ?>
        <div class="action-buttons">

            <form method="POST">
                <input type="hidden" name="action" value="approve">
                <button type="submit" class="btn btn-approve">
                    Approve
                </button>
            </form>

            <form method="POST">
                <input type="hidden" name="action" value="reject">
                <button type="submit" class="btn btn-reject" onclick="return confirm('Are you sure you want to reject this booking?')">
                    Reject
                </button>
            </form>

        </div>
        <?php endif; ?>

    </div>

</body>
</html>