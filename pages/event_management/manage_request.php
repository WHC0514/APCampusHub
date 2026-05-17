<?php
session_start();
date_default_timezone_set("Asia/Kuala_Lumpur");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

/* Pending venue bookings */
$stmtPending = $conn->prepare("SELECT vb.*,
           ev.venue_name,
           ev.cover_image,
           s.name AS student_name,
           l.name AS lecturer_name,
           u.role
    FROM venue_booking vb
    JOIN event_venue ev ON vb.venue_id = ev.venue_id
    JOIN user u ON vb.user_id = u.user_id
    LEFT JOIN student s ON vb.user_id = s.user_id
    LEFT JOIN lecturer l ON vb.user_id = l.user_id
    WHERE vb.booking_status = 'Pending'
    ORDER BY vb.created_at ASC");
$stmtPending->execute();
$pendingResult = $stmtPending->get_result();

/* Approved or Rejected venue bookings */
$stmtHistory = $conn->prepare("SELECT vb.*,
           ev.venue_name,
           ev.cover_image,
           s.name AS student_name,
           l.name AS lecturer_name,
           u.role
    FROM venue_booking vb
    JOIN event_venue ev ON vb.venue_id = ev.venue_id
    JOIN user u ON vb.user_id = u.user_id
    LEFT JOIN student s ON vb.user_id = s.user_id
    LEFT JOIN lecturer l ON vb.user_id = l.user_id
    WHERE vb.booking_status IN ('Approved', 'Rejected')
    ORDER BY vb.created_at DESC");
$stmtHistory->execute();
$historyResult = $stmtHistory->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APCampusHub</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../../assets/css/booking_room/my_bookings.css">
    <link rel="stylesheet" href="../../assets/css/event_management/manage_event_request.css">
</head>
<body>

<!-- Topbar -->
<div class="topbar profile-topbar">
    <div class="profile-topbar-left">
        <a href="../admin/event_management.php" class="back-btn">
            <img src="../../assets/icons/back.png" class="back-icon">
        </a>
        <h2>Manage Event Requests</h2>
    </div>
</div>

<div class="container">

    <div class="tab-toggle">
        <button class="tab-btn active" onclick="switchTab('pending', this)">
            Pending
        </button>
        <button class="tab-btn" onclick="switchTab('history', this)">
            Approved / Rejected
        </button>
    </div>

    <!-- Pending Tab -->
    <div id="tab-pending" class="tab-panel active">

        <?php if($pendingResult->num_rows > 0): ?>

            <div class="booking-list">

            <?php while($b = $pendingResult->fetch_assoc()): ?>

                <?php
                $image = "../../uploads/event/default-venue.jpg";
                if(!empty($b['cover_image'])) {
                    $image = "../../uploads/event/" . $b['cover_image'];
                }

                $requesterName = $b['role'] === 'lecturer'
                    ? ($b['lecturer_name'] ?? 'Unknown')
                    : ($b['student_name'] ?? 'Unknown');
                ?>

                <!-- Clickable card goes to detail page -->
                <a href="event_booking_detail.php?booking_id=<?php echo $b['booking_id']; ?>"
                   class="booking-card-link">

                    <div class="booking-card pending-card">

                        <img src="<?php echo $image; ?>"
                             class="booking-room-img"
                             alt="venue">

                        <div class="booking-info">

                            <h4><?php echo htmlspecialchars($b['venue_name']); ?></h4>

                            <p class="booking-type">
                                Requested by: <?php echo htmlspecialchars($requesterName); ?>
                                (<?php echo ucfirst($b['role']); ?>)
                            </p>

                            <p class="booking-time">
                                <?php echo date("D, d M Y g:i A", strtotime($b['start_time'])); ?>
                            </p>

                            <p class="booking-time">
                                to <?php echo date("D, d M Y g:i A", strtotime($b['end_time'])); ?>
                            </p>

                            <p class="booking-submitted">
                                Submitted: <?php echo date("d M Y, g:i A", strtotime($b['created_at'])); ?>
                            </p>

                            <span class="booking-status status-pending">
                                Pending
                            </span>

                        </div>

                        <div class="arrow-icon">›</div>

                    </div>

                </a>

            <?php endwhile; ?>

            </div>

        <?php else: ?>
            <div class="empty-state">No pending requests.</div>
        <?php endif; ?>

    </div>

    <!-- History Tab -->
    <div id="tab-history" class="tab-panel">

        <?php if($historyResult->num_rows > 0): ?>

            <div class="booking-list">

            <?php while($b = $historyResult->fetch_assoc()): ?>

                <?php
                $image = "../../uploads/event/default-venue.jpg";
                if(!empty($b['cover_image'])) {
                    $image = "../../uploads/event/" . $b['cover_image'];
                }

                $requesterName = $b['role'] === 'lecturer'
                    ? ($b['lecturer_name'] ?? 'Unknown')
                    : ($b['student_name'] ?? 'Unknown');

                switch($b['booking_status']) {
                    case 'Approved':
                        $statusClass = "status-completed";
                        break;
                    case 'Rejected':
                        $statusClass = "status-canceled";
                        break;
                    default:
                        $statusClass = "status-scheduled";
                }
                ?>

                <div class="booking-card history">

                    <img src="<?php echo $image; ?>"
                         class="booking-room-img"
                         alt="venue">

                    <div class="booking-info">

                        <h4><?php echo htmlspecialchars($b['venue_name']); ?></h4>

                        <p class="booking-type">
                            Requested by: <?php echo htmlspecialchars($requesterName); ?>
                            (<?php echo ucfirst($b['role']); ?>)
                        </p>

                        <p class="booking-time">
                            <?php echo date("D, d M Y g:i A", strtotime($b['start_time'])); ?>
                        </p>

                        <p class="booking-time">
                            to <?php echo date("D, d M Y g:i A", strtotime($b['end_time'])); ?>
                        </p>

                        <p class="booking-submitted">
                            Submitted: <?php echo date("d M Y, g:i A", strtotime($b['created_at'])); ?>
                        </p>

                        <span class="booking-status <?php echo $statusClass; ?>">
                            <?php echo $b['booking_status']; ?>
                        </span>

                    </div>

                </div>

            <?php endwhile; ?>

            </div>

        <?php else: ?>
            <div class="empty-state">No approved or rejected requests yet.</div>
        <?php endif; ?>

    </div>

</div>

<script>
function switchTab(tab, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    btn.classList.add('active');
}
</script>

</body>
</html>