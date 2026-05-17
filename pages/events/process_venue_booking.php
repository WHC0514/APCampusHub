<?php

session_start();

date_default_timezone_set("Asia/Kuala_Lumpur");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "student")
{
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

$userID = $_SESSION['user_id'];

function fail($msg, $redirect) {
    echo "<script>
        alert('$msg');
        window.location.href = '../student/events.php';
    </script>";
    exit();
}

/* Validate incoming data */
$venueID     = intval($_POST['venue_id']    ?? 0);
$startTime   = trim($_POST['start_time']   ?? '');
$endTime     = trim($_POST['end_time']     ?? '');
$description = trim($_POST['description']  ?? '');
$resourcesRaw = $_POST['resources']        ?? '[]';

if(!$venueID || !$startTime || !$endTime) {
    fail("Missing required fields. Please go back and try again.", $redirect);
}

// Validate datetime format (Y-m-dTH:i or Y-m-d H:i)
$startTime = str_replace("T", " ", $startTime);
$endTime   = str_replace("T", " ", $endTime);

if(!strtotime($startTime) || !strtotime($endTime)) {
    fail("Invalid date/time format.", $redirect);
}

// Start must not be in the past
if(strtotime($startTime) < time()) {
    fail("Start time cannot be in the past.", $redirect);
}

// End must be after start
if(strtotime($endTime) <= strtotime($startTime)) {
    fail("End time must be after start time.", $redirect);
}

$stmt = $conn->prepare("SELECT * FROM event_venue WHERE venue_id = ? AND status = 'Active'");
$stmt->bind_param("i", $venueID);
$stmt->execute();
$venue = $stmt->get_result()->fetch_assoc();

if(!$venue) {
    fail("Venue not found or is inactive.", $redirect);
}

/* Check for booking conflicts */
$stmt = $conn->prepare("SELECT COUNT(*) as conflicts FROM venue_booking WHERE venue_id = ? AND booking_status = 'Approved' AND start_time < ? AND end_time > ?");
$stmt->bind_param("iss", $venueID, $endTime, $startTime);
$stmt->execute();
$conflict = $stmt->get_result()->fetch_assoc();

if($conflict['conflicts'] > 0) {
    fail("This time slot overlaps with an existing approved booking. Please choose another time.", $redirect);
}

/* Insert to database */
$stmt = $conn->prepare("INSERT INTO venue_booking (venue_id, user_id, description, start_time, end_time)
    VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisss", $venueID, $userID, $description, $startTime, $endTime);

if(!$stmt->execute()) {
    fail("Failed to submit booking. Please try again.", $redirect);
}

$bookingID = $stmt->insert_id;

/* Insert request resource */
$resources = json_decode($resourcesRaw, true);

if(!empty($resources) && is_array($resources)) {

    $stmtResource = $conn->prepare("INSERT INTO event_resource_request (booking_id, user_id, venue_id, resource_type, description)
        VALUES (?, ?, ?, ?, ?)");

    foreach($resources as $r) {
        $resourceType = trim($r['type'] ?? '');
        $resourceDesc = trim($r['desc'] ?? '');

        // Skip if resource type is empty
        if(empty($resourceType)) continue;

        $stmtResource->bind_param("iiiss", $bookingID, $userID, $venueID, $resourceType, $resourceDesc);
        $stmtResource->execute();
    }
}

/* Success */
echo "<script>
    alert('Proposal submitted successfully! It is now pending approval.');
    window.location.href = '../student/events.php';
</script>";
exit();
?>