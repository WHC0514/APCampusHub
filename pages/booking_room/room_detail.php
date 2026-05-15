<?php

session_start();

/* Set timezone */
date_default_timezone_set("Asia/Kuala_Lumpur");

$role = $_SESSION['role'] ?? 'student';

$room_booking = ($role === "lecturer")
    ? "../lecturer/room_booking.php"
    : "../student/room_booking.php";

function fail($msg)
{
    $role = $_SESSION['role'] ?? 'student';

    if($role === "lecturer") {
        $redirect = "../lecturer/room_booking.php";
    } else {
        $redirect = "../student/room_booking.php";
    }

    echo "<script>
        alert('$msg');
        window.location.href = '$redirect';
    </script>";

    exit();
}

if(!isset($_SESSION['user_id']))
{
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

/* Check the room ID */
if(!isset($_GET['room_id']))
{
    header("Location: $room_booking");
    exit();
}

$roomID = $_GET['room_id'];

/* Selected date */
$selectedDate = date("Y-m-d");

if(isset($_GET['date']))
{
    $selectedDate = $_GET['date'];
}

/* Get room info */
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

/* Room default image */
$mainImage = "../../uploads/room/default-room.jpg";

/* Get room cover photo from database */
if(!empty($room['cover_image']))
{
    $mainImage = "../../uploads/room/" . $room['cover_image'];
}

$sqlImages = "SELECT image_name FROM room_images WHERE room_id = ?";
$stmtImages = $conn->prepare($sqlImages);
$stmtImages->bind_param("i", $roomID);
$stmtImages->execute();
$imagesResult = $stmtImages->get_result();

/* Get bookings data for the room */
$sqlBooking = "SELECT start_time, end_time FROM room_booking WHERE room_id = ? AND booking_date = ? AND booking_status = 'Approved' ORDER BY start_time ASC";

$stmtBooking = $conn->prepare($sqlBooking);
$stmtBooking->bind_param("is", $roomID, $selectedDate);
$stmtBooking->execute();

$bookingResult = $stmtBooking->get_result();

$bookings = [];

while($row = $bookingResult->fetch_assoc())
{
    $bookings[] = $row;
}

/* Operating hours */
$dayStart = strtotime($selectedDate . " 08:00:00");
$dayEnd   = strtotime($selectedDate . " 22:00:00");

$today = date("Y-m-d");

/* Current time */
if($selectedDate == $today)
{
    $nowTime = strtotime(date("Y-m-d H:i"));

    if($nowTime < $dayStart)
    {
        $currentPointer = $dayStart;
    } else {
        $currentPointer = $nowTime;
    }
} else {
    $currentPointer = $dayStart;
}

$nextAvailable = null;

/* Find the next available slot */
foreach($bookings as $b)
{
    $start = strtotime($selectedDate . " " . $b['start_time']);
    $end   = strtotime($selectedDate . " " . $b['end_time']);

    /* Skip ended booking */
    if($end <= $currentPointer)
    {
        continue;
    }

    /* Current time is inside booking */
    if($currentPointer >= $start && $currentPointer < $end)
    {
        $currentPointer = $end;
        continue;
    }

    /* Found free slot */
    if($currentPointer < $start)
    {
        $nextAvailable = [
            "start" => $currentPointer,
            "end" => $start
        ];

        break;
    }
}

/* After last booking */
if(!$nextAvailable)
{
    if($currentPointer < $dayEnd)
    {
        $nextAvailable = [
            "start" => $currentPointer,
            "end" => $dayEnd
        ];
    }
}

/* No slot left */
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
    <link rel="stylesheet" href="../../assets/css/student/room_detail.css">
</head>
<body>

    <!-- Topbar -->
    <div class="topbar profile-topbar">

        <div class="profile-topbar-left">

            <!-- Back Button -->
            <a href="<?php echo $room_booking; ?>" class="back-btn">

                <img src="../../assets/icons/back.png" class="back-icon">

            </a>

            <h2>Room Details</h2>

        </div>
    </div>

    <!-- Content -->
    <div class="room-details-container">

        <!-- LEFT -->
        <div class="room-left">

            <img src="<?php echo $mainImage; ?>" class="main-room-image" id="mainImage">

            <div class="room-gallery">

                <!-- Cover Image -->
                <img src="<?php echo $mainImage; ?>" 
                    class="thumb active"
                    onclick="changeImage(this)">

                <!-- Extra Images -->
                <?php while($img = $imagesResult->fetch_assoc()) { ?>

                    <img src="../../uploads/room/<?php echo $img['image_name']; ?>"
                        class="thumb"
                        onclick="changeImage(this)">

                <?php } ?>

            </div>

        </div>

        <div class="room-right">

            <h1>
                <?php echo $room['room_name']; ?>
            </h1>

            <div class="room-type">
                <?php echo $room['room_type']; ?>
            </div>

            <p class="room-location">

                Block <?php echo $room['block']; ?>
                -
                Level <?php echo $room['level']; ?>
                -
                Room <?php echo $room['room_number']; ?>

            </p>

            <p class="room-capacity">

                Capacity:
                <?php echo $room['capacity']; ?>
                pax

            </p>

            <p class="room-description">

                <?php echo $room['description']; ?>

            </p>

            <!-- Date Picker -->
            <form method="GET" id="dataForm">

                <input type="hidden"
                       name="room_id"
                       value="<?php echo $roomID; ?>">

                <label>Select Date</label>

                <input type="date"
                       name="date"
                       id="datePicker"
                       value="<?php echo $selectedDate; ?>"
                       min="<?php echo date('Y-m-d'); ?>"
                       class="date-picker">
            </form>

            <!-- Booked Slot -->
            <div class="slot-section">

                <h3>Booked Slots</h3>

                <?php
                if($bookingResult->num_rows > 0)
                {
                    foreach($bookings as $booking)
                    {
                        ?>
                        <div class="slot-card booked">
                            <?php
                                echo date("g:i A", strtotime($booking['start_time']));
                                echo " - ";
                                echo date("g:i A", strtotime($booking['end_time']));
                            ?>
                        </div>
                        <?php
                    }
                }
                else
                {
                    echo '<div class="slot-card available">No bookings for this day</div>';
                }
                ?>

            </div>

            <!-- Next Available Slot -->
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

            <!-- Button -->
            <a href="book_room.php?room_id=<?php echo $roomID; ?>"
               class="book-now-btn">

               Book This Room

            </a>

        </div>
    </div>
</body>

<script>
function changeImage(el)
{
    const main = document.getElementById("mainImage");

    // Change main image
    main.src = el.src;

    // Remove active from all thumbnails
    document.querySelectorAll(".thumb").forEach(t => {
        t.classList.remove("active");
    });

    // Set active
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