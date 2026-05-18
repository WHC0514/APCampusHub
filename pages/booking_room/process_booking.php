<?php

session_start();

date_default_timezone_set("Asia/Kuala_Lumpur");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ["student", "lecturer"]))
{
    header("Location: ../auth/login.php");
    exit();
}

$role = $_SESSION['role'] ?? 'student';

$room_booking = ($role === "lecturer")
    ? "../lecturer/room_booking.php"
    : "../student/room_booking.php";

require_once("../../config/db.php");

// Validate incoming data
$userID    = $_SESSION['user_id'];
$roomID    = intval($_POST['room_id']    ?? 0);
$date      = $_POST['date']        ?? '';
$startTime = $_POST['start_time']  ?? '';
$endTime   = $_POST['end_time']    ?? '';

// Basic presence check
if(!$roomID || !$date || !$startTime || !$endTime) {
    die("Missing booking details. Please go back and try again.");
}

// Validate date format
if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    die("Invalid date format.");
}

// Reject past dates
if($date < date("Y-m-d")) {
    die("You cannot book a room for a past date.");
}

// Validate time format
if(!preg_match('/^\d{2}:\d{2}$/', $startTime) || !preg_match('/^\d{2}:\d{2}$/', $endTime)) {
    die("Invalid time format.");
}

// End must be after start
if($startTime >= $endTime) {
    die("End time must be after start time.");
}

// Operating hours: 08:00 to 22:00
if($startTime < "08:00" || $endTime > "22:00") {
    die("Booking must be within operating hours (8:00 AM - 10:00 PM).");
}

// For today, start time must not be in the past
if($date == date("Y-m-d") && $startTime < date("H:i")) {
    die("Start time has already passed.");
}

// Reject if any approved booking overlaps with the requested slot
$stmt = $conn->prepare("SELECT COUNT(*) as conflicts FROM room_booking WHERE room_id = ? AND booking_date = ? AND booking_status = 'Approved' AND start_time < ? AND end_time > ?");
$stmt->bind_param("isss", $roomID, $date, $endTime, $startTime);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if($result['conflicts'] > 0) {
    die("This time slot has already been booked. Please go back and choose another time.");
}

// Insert booking
$stmt = $conn->prepare("INSERT INTO room_booking (room_id, user_id, booking_date, start_time, end_time) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisss", $roomID, $userID, $date, $startTime, $endTime);

if($stmt->execute()) {

    /* Get room name */
    $roomName = "Room";

    $stmtRoom = $conn->prepare("SELECT room_name FROM room WHERE room_id = ?");
    $stmtRoom->bind_param("i", $roomID);
    $stmtRoom->execute();
    $roomResult = $stmtRoom->get_result();

    if($row = $roomResult->fetch_assoc()) {
        $roomName = $row['room_name'];
    }

    /* Create notification */
    $title = "Booking Room Successful";
    $message = "Your booking for " . $roomName . " is successful, please check in to the room in 30 minutes after the booking start time or else your booking will be auto canceled by the system.";

    $stmtNotif = $conn->prepare("INSERT INTO notification (user_id, title, message, is_read, created_at)
        VALUES (?, ?, ?, 0, NOW())");

    $stmtNotif->bind_param("iss", $userID, $title, $message);
    $stmtNotif->execute();

    /* Success response */
?>
<script>
    alert("Booking successful!");
    window.location.href = "<?php echo $room_booking; ?>";
</script>
<?php
    exit();
} else {
    die("Something went wrong while saving your booking. Please try again.");
}

?>