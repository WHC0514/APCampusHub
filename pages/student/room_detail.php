<?php

session_start();

/* Set Timezone */
date_default_timezone_set("Asia/Kuala_Lumpur");

if(!isset($_SESSION['user_id']))
{
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

/* Check The Room ID */
if(!isset($_GET['room_id']))
{
    header("Location: room_booking.php");
    exit();
}

$roomID = $_GET['room_id'];

/* Selected Date */
$selectedDate = date("Y-m-d");

if(isset($_GET['date']))
{
    $selectedDate = $_GET['date'];
}

/* Get Room Info */
$sqlRoom = "SELECT * FROM room WHERE room_id = ?";
$stmtRoom = $conn->prepare($sqlRoom);
$stmtRoom->bind_param("i", $roomID);
$stmtRoom->execute();

$roomResult = $stmtRoom->get_result();

if($roomResult->num_rows == 0)
{
    die("Room not found.");
}

$room = $roomResult->fetch_assoc();

/* Room Default Image */
$mainImage = "../../uploads/room/default-room.jpg";

/* Get Room Cover Photo From Database */
if(!empty($room['cover_image']))
{
    $mainImage = "../../uploads/room/" . $room['cover_image'];
}

$sqlImages = "SELECT image_name FROM room_images WHERE room_id = ?";
$stmtImages = $conn->prepare($sqlImages);
$stmtImages->bind_param("i", $roomID);
$stmtImages->execute();
$imagesResult = $stmtImages->get_result();

/* Get Bookings Data For the Room */
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

/* Operating Hours */
$dayStart = strtotime($selectedDate . " 08:00:00");
$dayEnd   = strtotime($selectedDate . " 22:00:00");

$today = date("Y-m-d");

/* Current Time */
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

/* Find the Next Available Slot */
foreach($bookings as $b)
{
    $start = strtotime($selectedDate . " " . $b['start_time']);
    $end   = strtotime($selectedDate . " " . $b['end_time']);

    /* Skip Ended Booking */
    if($end <= $currentPointer)
    {
        continue;
    }

    /* Current Time is Inside Booking */
    if($currentPointer >= $start && $currentPointer < $end)
    {
        $currentPointer = $end;
        continue;
    }

    /* Found Free Slot */
    if($currentPointer < $start)
    {
        $nextAvailable = [
            "start" => $currentPointer,
            "end" => $start
        ];

        break;
    }
}

/* After Last Booking */
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

/* No SLot Left */
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
            <a href="room_booking.php" class="back-btn">

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
            <form method="GET">

                <input type="hidden"
                       name="room_id"
                       value="<?php echo $roomID; ?>">

                <label>Select Date</label>

                <input type="date"
                       name="date"
                       value="<?php echo $selectedDate; ?>"
                       class="date-picker"
                       onchange="this.form.submit()">

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

    // ChangeMain Image
    main.src = el.src;

    // Remove Active from All Thumbnails
    document.querySelectorAll(".thumb").forEach(t => {
        t.classList.remove("active");
    });

    // Set Active
    el.classList.add("active");
}
</script>

</html>