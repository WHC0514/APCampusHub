<?php

session_start();
require_once("../../config/db.php");

$userID = $_SESSION['user_id'];

/* Check active booking */
$sql = "SELECT rb.booking_id FROM room_booking rb
INNER JOIN room_checkin rc ON rb.booking_id = rc.booking_id
WHERE rb.user_id = ? AND rc.actual_checkout IS NULL LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows > 0)
{
    $row = $result->fetch_assoc();

    /* Active session exists */
    header("Location: room_session.php?booking_id=" . $row['booking_id']);
    exit();
    
} else {
    /* No active sessions */
    $role = $_SESSION['role'] ?? 'student';

    if($role === "lecturer")
    {
        header("Location: ../lecturer/checkin.php");
    }
    else
    {
        header("Location: ../student/checkin.php");
    }

    exit();
}
?>