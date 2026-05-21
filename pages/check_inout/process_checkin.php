<?php

session_start();

require_once("../../config/db.php");

date_default_timezone_set("Asia/Kuala_Lumpur");

/* Show error and redirect user to their dashboard */
function fail($msg)
{
    $role = $_SESSION['role'] ?? 'student';

    if($role === "lecturer") {
        $redirect = "../lecturer/dashboard.php";
    } else {
        $redirect = "../student/dashboard.php";
    }

    echo "<script>
        alert('$msg');
        window.location.href = '$redirect';
    </script>";

    exit();
}

if(!isset($_POST['booking_id'], $_POST['otp'])) {
    fail("Invalid request");
}

$userID = $_SESSION['user_id'];
$bookingID = intval($_POST['booking_id']);
$userOTP = trim($_POST['otp']);

/* Get booking */
$sql = "SELECT rb.*, r.room_id, r.room_name FROM room_booking rb 
        INNER JOIN room r ON rb.room_id = r.room_id 
        WHERE rb.booking_id = ? AND rb.user_id = ? LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $bookingID, $userID);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if(!$booking) fail("Booking not found");

$roomID = $booking['room_id'];

/* OTP check */
$sqlOTP = "SELECT * FROM room_otp WHERE room_id = ? ORDER BY generated_at DESC LIMIT 1";
$stmt = $conn->prepare($sqlOTP);
$stmt->bind_param("i", $roomID);
$stmt->execute();
$otp = $stmt->get_result()->fetch_assoc();

if(!$otp) fail("OTP not found");

if($userOTP != $otp['otp_code']) {
    fail("Invalid OTP");
}

/* Insert check-in */
$time = date("Y-m-d H:i:s");

$stmt = $conn->prepare("INSERT INTO room_checkin (booking_id, actual_checkin)
VALUES (?, ?)");

$stmt->bind_param("is", $bookingID, $time);
$stmt->execute();

/* Update room status */
$stmt = $conn->prepare("UPDATE room_status SET status = 'Occupied' WHERE room_id = ?");
$stmt->bind_param("i", $roomID);
$stmt->execute();

/* Redirect user to room control */
header("Location: room_session.php?booking_id=$bookingID");
exit();
?>