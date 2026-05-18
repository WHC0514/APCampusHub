<?php

session_start();
require_once("../../config/db.php");

/* Only admin allowed */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== "staff") {
    echo "<script>
        alert('Access denied. Staff only.');
        window.location.href = '../auth/login.php';
    </script>";
    exit();
}

/* Validate POST */
if(!isset($_POST['room_id'], $_POST['status'])) {
    echo "<script>
        alert('Invalid request.');
        window.location.href = '../staff/manage_rooms.php';
    </script>";
    exit();
}

$roomID = intval($_POST['room_id']);
$status = trim($_POST['status']);

/* Validate allowed status */
$allowed = ["Active", "Maintenance", "Inactive"];

if(!in_array($status, $allowed)) {
    echo "<script>
        alert('Invalid status selected.');
        window.history.back();
    </script>";
    exit();
}

/* Check current room state */
$sqlCheck = "SELECT r.status AS room_status, rs.status AS live_status FROM room r
             LEFT JOIN room_status rs ON r.room_id = rs.room_id
             WHERE r.room_id = ? LIMIT 1";

$stmt = $conn->prepare($sqlCheck);
$stmt->bind_param("i", $roomID);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    echo "<script>
        alert('Room not found.');
        window.location.href = '../staff/manage_rooms.php';
    </script>";
    exit();
}

$row = $result->fetch_assoc();

/* Block update if room is in use */
if($row['live_status'] === "In Use") {
    echo "<script>
        alert('Room is currently IN USE. Cannot change status.');
        window.location.href = '../staff/manage_rooms.php';
    </script>";
    exit();
}

$status = trim($status);
$status = strtolower($status);

/* Normalize input */
switch($status) {

    case "active":
        $roomStatus = "Active";
        $liveStatus = "Available";
        break;

    case "maintenance":
        $roomStatus = "Maintenance";
        $liveStatus = "Closed";
        break;

    case "inactive":
        $roomStatus = "Inactive";
        $liveStatus = "Closed";
        break;

    default:
        $roomStatus = "Active";
        $liveStatus = "Available";
}

$sql = "UPDATE room SET status = ? WHERE room_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $roomStatus, $roomID);
$stmt->execute();

$sqlCheck = "SELECT room_id FROM room_status WHERE room_id = ?";
$stmt = $conn->prepare($sqlCheck);
$stmt->bind_param("i", $roomID);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows > 0) {

    $sql = "UPDATE room_status SET status = ? WHERE room_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $liveStatus, $roomID);
    $stmt->execute();

} else {

    $sql = "INSERT INTO room_status (room_id, status) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $roomID, $liveStatus);
    $stmt->execute();
}

/* If room becomes unavailable, cancel future approved bookings */
if($roomStatus === "Maintenance" || $roomStatus === "Inactive")
{
    $currentDate = date("Y-m-d");
    $currentTime = date("H:i:s");

    /* Get room name */
    $stmtRoom = $conn->prepare("SELECT room_name FROM room WHERE room_id = ? LIMIT 1");

    $stmtRoom->bind_param("i", $roomID);
    $stmtRoom->execute();

    $roomData = $stmtRoom->get_result()->fetch_assoc();

    $roomName = $roomData['room_name'] ?? 'Room';

    /* Get all future approved bookings */
    $stmtBooking = $conn->prepare("SELECT booking_id, user_id, booking_date, start_time FROM room_booking WHERE room_id = ? AND booking_status = 'Approved'
        AND (
            booking_date > ?
            OR (
                booking_date = ?
                AND start_time > ?
            )
        )
    ");

    $stmtBooking->bind_param("isss", $roomID, $currentDate, $currentDate, $currentTime);

    $stmtBooking->execute();

    $bookingResult = $stmtBooking->get_result();

    while($booking = $bookingResult->fetch_assoc())
    {
        $bookingID = $booking['booking_id'];
        $bookUserID = $booking['user_id'];

        /* Cancel booking */
        $stmtCancel = $conn->prepare("UPDATE room_booking SET booking_status = 'Canceled' WHERE booking_id = ?");

        $stmtCancel->bind_param("i", $bookingID);
        $stmtCancel->execute();

        /* Notification title */
        $title = "Room Booking Canceled";

        /* Notification message */
        $message = "Your booking for " . $roomName . " has been canceled because the room is now under " . strtolower($roomStatus) . ".";

        /* Send notification */
        $stmtNotif = $conn->prepare("INSERT INTO notification (user_id, title, message, is_read, created_at)
            VALUES (?, ?, ?, 0, NOW())");

        $stmtNotif->bind_param("iss", $bookUserID, $title, $message);

        $stmtNotif->execute();
    }
}

/* Success */
echo "<script>
    alert('Room status updated successfully!');
    window.location.href = '../staff/manage_rooms.php';
</script>";

exit();

?>