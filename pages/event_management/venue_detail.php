<?php

session_start();

if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin")
{
    header("Location: ../auth/login.php");
    exit();
}

/* Set timezone */
date_default_timezone_set("Asia/Kuala_Lumpur");

function fail($msg)
{
    echo "<script>
        alert('$msg');
        window.location.href = '../student/events.php';
    </script>";

    exit();
}

if(!isset($_SESSION['user_id']))
{
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

/* Check venue ID */
if(!isset($_GET['venue_id']))
{
    header("Location: $events_page");
    exit();
}

$venueID = $_GET['venue_id'];

/* Selected date */
$selectedDate = date("Y-m-d");

if(isset($_GET['date']))
{
    $selectedDate = $_GET['date'];
}

/* Get venue info */
$sqlVenue = "SELECT * FROM event_venue WHERE venue_id = ?";
$stmtVenue = $conn->prepare($sqlVenue);
$stmtVenue->bind_param("i", $venueID);
$stmtVenue->execute();

$venueResult = $stmtVenue->get_result();

if($venueResult->num_rows == 0)
{
    fail("Venue not found.");
}

$venue = $venueResult->fetch_assoc();

/* Default image */
$mainImage = "../../uploads/event/default-venue.jpg";

/* Cover image */
if(!empty($venue['cover_image']))
{
    $mainImage = "../../uploads/event/" . $venue['cover_image'];
}

/* Extra images */
$sqlImages = "SELECT image_name FROM venue_images WHERE venue_id = ?";
$stmtImages = $conn->prepare($sqlImages);
$stmtImages->bind_param("i", $venueID);
$stmtImages->execute();
$imagesResult = $stmtImages->get_result();

/* Get bookings */
$sqlBooking = "SELECT start_time, end_time FROM venue_booking WHERE venue_id = ? AND booking_status = 'Approved' AND DATE(start_time) <= ? AND DATE(end_time) >= ? ORDER BY start_time ASC";

$stmtBooking = $conn->prepare($sqlBooking);
$stmtBooking->bind_param("iss", $venueID, $selectedDate, $selectedDate);
$stmtBooking->execute();

$bookingResult = $stmtBooking->get_result();

$bookings = [];

while($row = $bookingResult->fetch_assoc())
{
    $bookings[] = $row;
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
    <link rel="stylesheet" href="../../assets/css/events/venue_detail.css">
</head>
<body>

    <!-- Topbar -->
    <div class="topbar profile-topbar">

        <div class="profile-topbar-left">

            <a href="../admin/event_management.php" class="back-btn">

                <img src="../../assets/icons/back.png" class="back-icon">

            </a>

            <h2>Venue Details</h2>

        </div>

    </div>

    <!-- Content -->
    <div class="venue-details-container">

        <div class="venue-left">

            <img src="<?php echo $mainImage; ?>" class="main-venue-image" id="mainImage">

            <div class="venue-gallery">

                <!-- Cover Image -->
                <img src="<?php echo $mainImage; ?>" class="thumb active" onclick="changeImage(this)">

                <!-- Extra Images -->
                <?php while($img = $imagesResult->fetch_assoc()) { ?>

                    <img src="../../uploads/event/<?php echo $img['image_name']; ?>" class="thumb" onclick="changeImage(this)">

                <?php } ?>

            </div>

        </div>

        <div class="venue-right">

            <h1>
                <?php echo $venue['venue_name']; ?>
            </h1>

            <div class="venue-type">
                Event Venue
            </div>

            <p class="venue-description">

                <?php echo $venue['description']; ?>

            </p>

            <!-- Date Picker -->
            <form method="GET" id="dateForm">

                <input type="hidden" name="venue_id" value="<?php echo $venueID; ?>">

                <label>Select Date</label>

                <input type="date" name="date" id="datePicker" value="<?php echo $selectedDate; ?>" min="<?php echo date('Y-m-d'); ?>" class="date-picker">

            </form>

            <!-- Approved Events -->
            <div class="booking-section">

                <h3>Approved Events</h3>

                <?php

                if(count($bookings) > 0)
                {
                    foreach($bookings as $booking)
                    {
                        ?>

                        <div class="booking-card booked">

                            <?php
                            echo date("d M Y g:i A", strtotime($booking['start_time']));
                            echo " - ";
                            echo date("d M Y g:i A", strtotime($booking['end_time']));
                            ?>

                        </div>

                        <?php
                    }
                } else
                {
                    echo '<div class="booking-card available">No approved event on this date</div>';
                }

                ?>

            </div>

        </div>

    </div>

</body>

<script>

function changeImage(el)
{
    const main = document.getElementById("mainImage");

    main.src = el.src;

    document.querySelectorAll(".thumb").forEach(t => {
        t.classList.remove("active");
    });

    el.classList.add("active");
}

const datePicker = document.getElementById("datePicker");

datePicker.addEventListener("change", function(){

    if(this.value)
    {
        document.getElementById("dateForm").submit();
    }

});

</script>

</html>