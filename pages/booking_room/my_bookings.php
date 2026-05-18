<?php

session_start();

date_default_timezone_set("Asia/Kuala_Lumpur");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ["student", "lecturer"]))
{
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

$role = $_SESSION['role'] ?? 'student';

$room_booking = ($role === "lecturer")
    ? "../lecturer/room_booking.php"
    : "../student/room_booking.php";

$userID = $_SESSION['user_id'];

if(isset($_POST['cancel_booking_id'])) {
    $cancelID = intval($_POST['cancel_booking_id']);

    // Make sure the booking belongs to this user before cancelling
    $stmtCancel = $conn->prepare("UPDATE room_booking SET booking_status = 'Canceled' WHERE booking_id = ? AND user_id = ? AND booking_status = 'Approved'");
    $stmtCancel->bind_param("ii", $cancelID, $userID);
    $stmtCancel->execute();

    header("Location: my_bookings.php");
    exit();
}

/* Show upcoming bookings only */
$stmtUpcoming = $conn->prepare("
    SELECT rb.*, 
           r.room_name,
           r.block,
           r.level,
           r.room_number,
           r.room_type,
           r.cover_image
    FROM room_booking rb
    JOIN room r ON rb.room_id = r.room_id
    WHERE rb.user_id = ?
    AND rb.booking_status = 'Approved'
    ORDER BY rb.booking_date ASC, rb.start_time ASC
");

$stmtUpcoming->bind_param("i", $userID);
$stmtUpcoming->execute();
$upcomingResult = $stmtUpcoming->get_result();


/* Show completed and canceled bookings */
$stmtHistory = $conn->prepare("
    SELECT rb.*, 
           r.room_name,
           r.block,
           r.level,
           r.room_number,
           r.room_type,
           r.cover_image,
           rc.actual_checkin,
           rc.actual_checkout
    FROM room_booking rb
    JOIN room r ON rb.room_id = r.room_id
    LEFT JOIN room_checkin rc ON rb.booking_id = rc.booking_id
    WHERE rb.user_id = ?
    AND (
        rb.booking_status = 'Completed'
        OR rb.booking_status = 'Canceled'
    )
    ORDER BY rb.booking_date DESC, rb.start_time DESC
");

$stmtHistory->bind_param("i", $userID);
$stmtHistory->execute();
$historyResult = $stmtHistory->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../../assets/css/booking_room/my_bookings.css">
</head>

<body>

    <!-- Topbar -->
    <div class="topbar profile-topbar">
        <div class="profile-topbar-left">

            <a href="<?php echo $room_booking; ?>" class="back-btn">
                <img src="../../assets/icons/back.png" class="back-icon">
            </a>

            <h2>My Bookings</h2>

        </div>
    </div>

    <div class="container">

        <div class="tab-toggle">

            <button class="tab-btn active" onclick="switchTab('upcoming', this)">
                Upcoming
            </button>

            <button class="tab-btn" onclick="switchTab('history', this)">
                History
            </button>

        </div>

        <div id="tab-upcoming" class="tab-panel active">

            <?php if($upcomingResult->num_rows > 0): ?>

                <div class="booking-list">

                    <?php while($b = $upcomingResult->fetch_assoc()): ?>

                        <?php
                        $image = "../../uploads/room/default-room.jpg";

                        if(!empty($b['cover_image'])) {
                            $image = "../../uploads/room/" . $b['cover_image'];
                        }
                        ?>

                        <div class="booking-card upcoming">

                            <img src="<?php echo $image; ?>" class="booking-room-img" alt="room">

                            <div class="booking-info">

                                <h4>
                                    <?php echo htmlspecialchars($b['room_name']); ?>
                                </h4>

                                <p class="booking-type">
                                    <?php echo htmlspecialchars($b['room_type']); ?>
                                </p>

                                <p class="booking-location">
                                    Block <?php echo htmlspecialchars($b['block']); ?>
                                    -
                                    Level <?php echo htmlspecialchars($b['level']); ?>
                                    -
                                    Room <?php echo htmlspecialchars($b['room_number']); ?>
                                </p>

                                <p class="booking-date">
                                    <?php echo date("D, d M Y", strtotime($b['booking_date'])); ?>
                                </p>

                                <p class="booking-time">
                                    <?php
                                    echo date("g:i A", strtotime($b['start_time']));
                                    echo " - ";
                                    echo date("g:i A", strtotime($b['end_time']));
                                    ?>
                                </p>

                                <span class="booking-status status-scheduled">
                                    Upcoming
                                </span>

                            </div>

                            <button class="cancel-btn" onclick="event.stopPropagation(); confirmCancel(<?php echo $b['booking_id']; ?>)">
                                <img src="../../assets/icons/delete.png" alt="Cancel">
                            </button>

                        </div>

                    <?php endwhile; ?>

                </div>

            <?php else: ?>

                <div class="empty-state">
                    No upcoming bookings.
                </div>

            <?php endif; ?>

        </div>

        <div id="tab-history" class="tab-panel">

            <?php if($historyResult->num_rows > 0): ?>

                <div class="booking-list">

                    <?php while($b = $historyResult->fetch_assoc()): ?>

                        <?php
                        $image = "../../uploads/room/default-room.jpg";

                        if(!empty($b['cover_image'])) {
                            $image = "../../uploads/room/" . $b['cover_image'];
                        }

                        $statusClass = "status-completed";

                        if($b['booking_status'] == 'Canceled') {
                            $statusClass = "status-canceled";
                        }
                        ?>

                        <div class="booking-card history">

                            <img src="<?php echo $image; ?>" class="booking-room-img" alt="room">

                            <div class="booking-info">

                                <h4>
                                <?php echo htmlspecialchars($b['room_name']); ?>
                                </h4>

                                <p class="booking-type">
                                    <?php echo htmlspecialchars($b['room_type']); ?>
                                </p>

                                <p class="booking-location">
                                    Block <?php echo htmlspecialchars($b['block']); ?>
                                    -
                                    Level <?php echo htmlspecialchars($b['level']); ?>
                                    -
                                    Room <?php echo htmlspecialchars($b['room_number']); ?>
                                </p>

                                <p class="booking-date">
                                    <?php echo date("D, d M Y", strtotime($b['booking_date'])); ?>
                                </p>

                                <p class="booking-time">
                                    Booked:
                                    <?php
                                    echo date("g:i A", strtotime($b['start_time']));
                                    echo " - ";
                                    echo date("g:i A", strtotime($b['end_time']));
                                    ?>
                                </p>

                                <?php if(!empty($b['actual_checkin']) && !empty($b['actual_checkout'])): ?>

                                    <p class="booking-actual">
                                        Actual:
                                        <?php echo date("g:i A", strtotime($b['actual_checkin'])); ?>
                                        -
                                        <?php echo date("g:i A", strtotime($b['actual_checkout'])); ?>
                                    </p>

                                <?php endif; ?>

                                <span class="booking-status <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($b['booking_status']); ?>
                                </span>

                            </div>

                        </div>

                    <?php endwhile; ?>

                </div>

            <?php else: ?>

                <div class="empty-state">
                    No booking history yet.
                </div>

            <?php endif; ?>

        </div>

    </div>

    <!-- Cancel Confirmation -->
    <div id="cancelModal" class="modal-overlay">
        <div class="modal-box">
            <h3>Cancel Booking?</h3>
            <p>Are you sure you want to cancel this booking? This cannot be undone.</p>
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
function switchTab(tab, btn) {

    document.querySelectorAll('.tab-panel')
        .forEach(panel => panel.classList.remove('active'));

    document.querySelectorAll('.tab-btn')
        .forEach(button => button.classList.remove('active'));

    document.getElementById('tab-' + tab)
        .classList.add('active');

    btn.classList.add('active');
}

function confirmCancel(bookingId) {
    document.getElementById("cancelBookingId").value = bookingId;
    document.getElementById("cancelModal").classList.add("active");
}

function closeModal() {
    document.getElementById("cancelModal").classList.remove("active");
}

// Close modal if user clicks outside the box
document.getElementById("cancelModal").addEventListener("click", function(e) {
    if(e.target === this) closeModal();
});
</script>

</body>
</html>