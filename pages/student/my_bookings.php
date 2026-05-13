<?php
session_start();

date_default_timezone_set("Asia/Kuala_Lumpur");

if(!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

$userID = $_SESSION['user_id'];

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
    <title>My Bookings</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../../assets/css/student/my_bookings.css">
</head>

<body>

<!-- TOPBAR -->
<div class="topbar profile-topbar">
    <div class="profile-topbar-left">

        <a href="room_booking.php" class="back-btn">
            <img src="../../assets/icons/back.png" class="back-icon">
        </a>

        <h2>My Bookings</h2>

    </div>
</div>

<div class="container">

    <div class="tab-toggle">

        <button class="tab-btn active"
                onclick="switchTab('upcoming', this)">
            Upcoming
        </button>

        <button class="tab-btn"
                onclick="switchTab('history', this)">
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

                        <img src="<?php echo $image; ?>"
                             class="booking-room-img"
                             alt="room">

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

                        <img src="<?php echo $image; ?>"
                             class="booking-room-img"
                             alt="room">

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
</script>

</body>
</html>