<?php

session_start();

/* Set timezone */
date_default_timezone_set("Asia/Kuala_Lumpur");

function fail($msg)
{
    echo "<script>
        alert('$msg');
        window.location.href = '../staff/manage_rooms.php';
    </script>";

    exit();
}

if(!isset($_SESSION['user_id']))
{
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

/* Check room ID */
if(!isset($_GET['room_id']))
{
    header("Location: ../staff/manage_rooms.php");
    exit();
}

$roomID = $_GET['room_id'];

/* Selected date */
$selectedDate = date("Y-m-d");

if(isset($_GET['date']))
{
    $selectedDate = $_GET['date'];
}

/* Room info */
$sqlRoom = "SELECT * FROM room WHERE room_id = ?";
$stmtRoom = $conn->prepare($sqlRoom);
$stmtRoom->bind_param("i", $roomID);
$stmtRoom->execute();

$roomResult = $stmtRoom->get_result();

if($roomResult->num_rows == 0)
{
    fail("Room not found.");
}

$room = $roomResult->fetch_assoc();

/* LIVE STATUS (NEW - ADDED ONLY) */
$sqlStatus = "SELECT status FROM room_status WHERE room_id = ? LIMIT 1";
$stmtStatus = $conn->prepare($sqlStatus);
$stmtStatus->bind_param("i", $roomID);
$stmtStatus->execute();
$statusResult = $stmtStatus->get_result();
$statusRow = $statusResult->fetch_assoc();

$roomMainStatus = $room['status'];

if($roomMainStatus === "Maintenance" || $roomMainStatus === "Inactive") {

    // Direct override (highest priority)
    $liveStatus = $roomMainStatus;

} else {

    // Only Active rooms use live status table
    $liveStatus = $statusRow['status'] ?? 'Available';

}

/* Main image */
$mainImage = "../../uploads/room/default-room.jpg";

if(!empty($room['cover_image']))
{
    $mainImage = "../../uploads/room/" . $room['cover_image'];
}

/* Extra images */
$sqlImages = "SELECT image_name FROM room_images WHERE room_id = ?";
$stmtImages = $conn->prepare($sqlImages);
$stmtImages->bind_param("i", $roomID);
$stmtImages->execute();
$imagesResult = $stmtImages->get_result();

$sqlBooking = "SELECT start_time, end_time FROM room_booking 
WHERE room_id = ? AND booking_date = ? AND booking_status = 'Approved' 
ORDER BY start_time ASC";

$stmtBooking = $conn->prepare($sqlBooking);
$stmtBooking->bind_param("is", $roomID, $selectedDate);
$stmtBooking->execute();

$bookingResult = $stmtBooking->get_result();

$bookings = [];

while($row = $bookingResult->fetch_assoc())
{
    $bookings[] = $row;
}

$dayStart = strtotime($selectedDate . " 08:00:00");
$dayEnd   = strtotime($selectedDate . " 22:00:00");

$today = date("Y-m-d");

if($selectedDate == $today)
{
    $nowTime = strtotime(date("Y-m-d H:i"));

    $currentPointer = ($nowTime < $dayStart) ? $dayStart : $nowTime;
} else {
    $currentPointer = $dayStart;
}

$nextAvailable = null;

foreach($bookings as $b)
{
    $start = strtotime($selectedDate . " " . $b['start_time']);
    $end   = strtotime($selectedDate . " " . $b['end_time']);

    if($end <= $currentPointer) continue;

    if($currentPointer >= $start && $currentPointer < $end)
    {
        $currentPointer = $end;
        continue;
    }

    if($currentPointer < $start)
    {
        $nextAvailable = [
            "start" => $currentPointer,
            "end" => $start
        ];
        break;
    }
}

if(!$nextAvailable && $currentPointer < $dayEnd)
{
    $nextAvailable = [
        "start" => $currentPointer,
        "end" => $dayEnd
    ];
}

if($currentPointer >= $dayEnd)
{
    $nextAvailable = null;
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
    <link rel="stylesheet" href="../../assets/css/booking_room/room_detail.css">
    <link rel="stylesheet" href="../../assets/css/manage_rooms/manage_room_detail.css">
</head>
<body>

    <div class="topbar profile-topbar">

        <div class="profile-topbar-left">

            <a href="../staff/manage_rooms.php" class="back-btn">
                <img src="../../assets/icons/back.png" class="back-icon">
            </a>

            <h2>Room Details</h2>

        </div>
    </div>

    <div class="room-details-container">

        <div class="room-left">

            <img src="<?php echo $mainImage; ?>" class="main-room-image" id="mainImage">

            <div class="room-gallery">

                <img src="<?php echo $mainImage; ?>" class="thumb active" onclick="changeImage(this)">

                <?php while($img = $imagesResult->fetch_assoc()) { ?>
                    <img src="../../uploads/room/<?php echo $img['image_name']; ?>" class="thumb" onclick="changeImage(this)">
                <?php } ?>

            </div>
        </div>

        <div class="room-right">

            <h1><?php echo $room['room_name']; ?></h1>

            <div style="display:flex;gap:10px;align-items:center;">

                <div class="room-type">
                    <?php echo $room['room_type']; ?>
                </div>

                <div class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $liveStatus)); ?>">
                    <?php echo $liveStatus; ?>
                </div>

            </div>

            <p class="room-location">
                Block <?php echo $room['block']; ?> -
                Level <?php echo $room['level']; ?> -
                Room <?php echo $room['room_number']; ?>
            </p>

            <p class="room-capacity">
                Capacity: <?php echo $room['capacity']; ?> pax
            </p>

            <p class="room-description">
                <?php echo $room['description']; ?>
            </p>

            <!-- Date Picker -->
                <form method="GET" id="dataForm">

                    <input type="hidden" name="room_id" value="<?php echo $roomID; ?>">

                    <label>Select Date</label>

                    <input type="date" name="date" id="datePicker" value="<?php echo $selectedDate; ?>" min="<?php echo date('Y-m-d'); ?>" class="date-picker">
                </form>

            <div class="status-control">

                <h3>Room Status Control</h3>

                <?php if($liveStatus === "Occupied"): ?>

                    <p style="color:#f87171;">
                        Room is currently OCCUPIED. Status cannot be changed.
                    </p>

                <?php else: ?>

                    <form method="POST" action="update_room_status.php">

                        <input type="hidden" name="room_id" value="<?php echo $roomID; ?>">

                        <label><input type="radio" name="status" value="Active" <?php if($liveStatus=="Available") echo "checked"; ?>> Active</label>

                        <label><input type="radio" name="status" value="Maintenance" <?php if($liveStatus=="Maintenance") echo "checked"; ?>> Maintenance</label>

                        <label><input type="radio" name="status" value="Inactive" <?php if($liveStatus=="Inactive") echo "checked"; ?>> Inactive</label>

                        <button type="submit">Update Status</button>

                </form>

                <?php endif; ?>

            </div>

            <div class="slot-section">

                <h3>Booked Slots</h3>

                <?php if($bookingResult->num_rows > 0): ?>
                    <?php foreach($bookings as $booking): ?>
                        <div class="slot-card booked">
                            <?php
                                echo date("g:i A", strtotime($booking['start_time']));
                                echo " - ";
                                echo date("g:i A", strtotime($booking['end_time']));
                            ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="slot-card available">No bookings for this day</div>
                <?php endif; ?>

            </div>

            <div class="next-slot-box">

                <h3>Next Available Slot</h3>

                <?php if($nextAvailable): ?>

                    <p>
                        <?php 
                            echo date("g:i A", $nextAvailable['start']);
                            echo " - ";
                            echo date("g:i A", $nextAvailable['end']);
                        ?>
                    </p>

                <?php else: ?>

                    <p>No available slot today</p>

                <?php endif; ?>

            </div>

        </div>
    </div>

</body>

<script>
function changeImage(el)
{
    document.getElementById("mainImage").src = el.src;

    document.querySelectorAll(".thumb").forEach(t => {
        t.classList.remove("active");
    });

    el.classList.add("active");
}

const datePicker    = document.getElementById("datePicker");
const todayStr      = "<?php echo date('Y-m-d'); ?>";
let dateEnterPressed = false;

function submitDate() {
    if(datePicker.value < todayStr) {
        datePicker.value = todayStr;
    }
    if(datePicker.value) {
        document.getElementById("dataForm").submit();
    }
}

datePicker.addEventListener("keydown", function(e) {
    if(e.key === "Enter") {
        e.preventDefault();
        dateEnterPressed = true;
        submitDate();
    }
});

datePicker.addEventListener("blur", function() {
    if(dateEnterPressed) {
        dateEnterPressed = false;
        return;
    }
    submitDate();
});
</script>

</html>