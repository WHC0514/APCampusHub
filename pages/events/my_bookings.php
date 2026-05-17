<?php

session_start();

date_default_timezone_set("Asia/Kuala_Lumpur");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "student")
{
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

$userID = $_SESSION['user_id'];

/* Cancel booking for booking in Pending status */
if(isset($_POST['cancel_booking_id'])) {
    $cancelID = intval($_POST['cancel_booking_id']);

    $stmt = $conn->prepare("UPDATE venue_booking SET booking_status = 'Canceled' WHERE booking_id = ? AND user_id = ? AND booking_status = 'Pending'");
    $stmt->bind_param("ii", $cancelID, $userID);
    $stmt->execute();

    header("Location: my_bookings.php");
    exit();
}

/* Get all bookings data */
$stmt = $conn->prepare("SELECT vb.*,
           ev.venue_name,
           ev.cover_image,
           ev.description AS venue_description
    FROM venue_booking vb
    JOIN event_venue ev ON vb.venue_id = ev.venue_id
    WHERE vb.user_id = ? ORDER BY vb.created_at DESC");
$stmt->bind_param("i", $userID);
$stmt->execute();
$bookings = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APCampusHub</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../../assets/css/events/my_bookings.css">
</head>
<body>

    <!-- Topbar -->
    <div class="topbar profile-topbar">
        <div class="profile-topbar-left">
            <a href="../student/events.php" class="back-btn">
                <img src="../../assets/icons/back.png" class="back-icon">
            </a>
            <h2>My Event Bookings</h2>
        </div>
    </div>

    <div class="container">

        <?php if($bookings->num_rows > 0): ?>

            <div class="booking-list">

            <?php while($b = $bookings->fetch_assoc()): ?>

                <?php
                $image = "../../uploads/event/default-venue.jpg";
                if(!empty($b['cover_image'])) {
                    $image = "../../uploads/event/" . $b['cover_image'];
                }

                // Status badge class
                switch($b['booking_status']) {
                    case 'Approved':
                        $statusClass = "status-approved";
                        break;
                    case 'Rejected':
                        $statusClass = "status-rejected";
                        break;
                    case 'Canceled':
                        $statusClass = "status-canceled";
                        break;
                    default:
                        $statusClass = "status-pending";
                }

                $canCancel = ($b['booking_status'] === 'Pending');
                ?>

                <div class="booking-card">

                    <img src="<?php echo $image; ?>" class="booking-venue-img" alt="venue">

                    <div class="booking-info">

                        <h4><?php echo htmlspecialchars($b['venue_name']); ?></h4>

                        <p class="booking-desc">
                            <?php echo htmlspecialchars($b['description'] ?: '—'); ?>
                        </p>

                        <p class="booking-time">
                            <span class="label">From</span>
                            <?php echo date("D, d M Y g:i A", strtotime($b['start_time'])); ?>
                        </p>

                        <p class="booking-time">
                            <span class="label">To</span>
                            <?php echo date("D, d M Y g:i A", strtotime($b['end_time'])); ?>
                        </p>

                        <p class="booking-submitted">
                            Submitted: <?php echo date("d M Y, g:i A", strtotime($b['created_at'])); ?>
                        </p>

                        <span class="booking-status <?php echo $statusClass; ?>">
                            <?php echo $b['booking_status']; ?>
                        </span>

                    </div>

                    <?php if($canCancel): ?>
                        <button type="button" class="cancel-btn" onclick="event.stopPropagation(); confirmCancel(<?php echo $b['booking_id']; ?>)">
                            <img src="../../assets/icons/delete.png" alt="Cancel">
                        </button>
                    <?php endif; ?>

                </div>

            <?php endwhile; ?>

            </div>

        <?php else: ?>

            <div class="empty-state">
                You have not made any venue bookings yet.
            </div>

        <?php endif; ?>

    </div>

    <!-- Cancel Confirmation -->
    <div id="cancelModal" class="modal-overlay">
        <div class="modal-box">
            <h3>Cancel Proposal?</h3>
            <p>Are you sure you want to cancel this booking proposal? This cannot be undone.</p>
            <div class="modal-actions">
                <button class="modal-btn btn-no" onclick="closeModal()">
                    No, Keep It
                </button>
                <form method="POST" id="cancelForm">
                    <input type="hidden" name="cancel_booking_id" id="cancelBookingId">
                    <button type="submit" class="modal-btn btn-yes">
                        Yes, Cancel
                    </button>
                </form>
            </div>
        </div>
    </div>

<script>
function confirmCancel(bookingId) {
    document.getElementById("cancelBookingId").value = bookingId;
    document.getElementById("cancelModal").classList.add("active");
}

function closeModal() {
    document.getElementById("cancelModal").classList.remove("active");
}

document.getElementById("cancelModal").addEventListener("click", function(e) {
    if(e.target === this) closeModal();
});
</script>

</body>
</html>