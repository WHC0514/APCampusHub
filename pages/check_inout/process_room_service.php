<?php

session_start();

require_once("../../config/db.php");

$userID = $_SESSION['user_id'];

$bookingID = $_POST['booking_id'] ?? null;
$roomID = $_POST['room_id'] ?? null;
$requestType = $_POST['request_type'] ?? null;
$message = $_POST['message'] ?? null;

function backWithAlert($msg)
{
    echo "<script>
        alert('$msg');
        window.history.back();
    </script>";
    exit();
}

/* Validate input */
if(empty($bookingID) || empty($roomID) || empty($requestType) || empty($message)) {
    backWithAlert("Please fill in all required fields.");
}

/* Validate resource */
$resourceType = NULL;

if($requestType === "Borrow Resource") {

    if(empty($_POST['resource_type'])) {
        backWithAlert("Please select a resource type.");
    }

    $resourceType = $_POST['resource_type'];
}

/* Save into database */
$sql = "INSERT INTO room_service_request (booking_id, user_id, room_id, request_type, resource_type, description)
VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

/* Prepare failed fallback */
if(!$stmt) {
    backWithAlert("System error: Unable to prepare request. Please try again.");
}

$stmt->bind_param("iiisss", $bookingID, $userID, $roomID, $requestType, $resourceType, $message);

/* Execute fallback */
if(!$stmt->execute()) {

    error_log("DB Insert Failed: " . $stmt->error);

    backWithAlert("Failed to submit request. Please try again.");
}

/* Save successful */
$stmt->close();

echo "<script>alert('Request submitted successfully!');

    setTimeout(() => {
        window.location.href = 'room_session.php?booking_id=$bookingID&request_sent=1';
    }, 800);
</script>";

exit();

?>