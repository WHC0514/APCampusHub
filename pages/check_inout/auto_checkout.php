<?php

session_start();

require_once("../../config/db.php");

date_default_timezone_set(
    "Asia/Kuala_Lumpur"
);

if(!isset($_POST['booking_id']))
{
    exit("Invalid");
}

$bookingID =
intval($_POST['booking_id']);

/* Check check-in data */

$sql = "SELECT rc.*, rb.room_id FROM room_checkin rc
INNER JOIN room_booking rb ON rc.booking_id = rb.booking_id
WHERE rc.booking_id = ? LIMIT 1";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $bookingID);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0)
{
    exit("No session");
}

$data = $result->fetch_assoc();

/* Already check-out */

if(!empty($data['actual_checkout']))
{
    exit("Already checked out");
}

/* Update check-out */

$checkoutTime =
date("Y-m-d H:i:s");

$sqlUpdate = "UPDATE room_checkin SET actual_checkout = ?, occupancy_status = 'Check Out' WHERE booking_id = ?";

$stmtUpdate =
$conn->prepare($sqlUpdate);

$stmtUpdate->bind_param("si", $checkoutTime, $bookingID);

$stmtUpdate->execute();

/* Update booking status */

$sqlBooking = "UPDATE room_booking SET booking_status = 'Completed' WHERE booking_id = ?";

$stmtBooking = $conn->prepare($sqlBooking);

$stmtBooking->bind_param("i", $bookingID);

$stmtBooking->execute();

/* Update room status */

$sqlRoom = "UPDATE room_status SET status = 'Available' WHERE room_id = ?";

$stmtRoom = $conn->prepare($sqlRoom);

$stmtRoom->bind_param("i", $data['room_id']);

$stmtRoom->execute();

echo "success";

/* Reset room's IOT device to default */

$sqlResetIOT = "UPDATE room_iot_state SET projector = 'ON', lights_brightness = 100, ac_temperature = 24 WHERE room_id = ?";

$stmtResetIOT = $conn->prepare($sqlResetIOT);

$stmtResetIOT->bind_param("i", $data['room_id']);

$stmtResetIOT->execute();

?>