<?php

session_start();

require_once("../../config/db.php");

$userID = $_SESSION['user_id'];

$bookingID = $_POST['booking_id'] ?? null;
$roomID = $_POST['room_id'] ?? null;
$issueType = $_POST['issue_type'] ?? null;
$severity = $_POST['severity'] ?? null;
$description = $_POST['description'] ?? null;

/* Alert function */
function backWithAlert($msg)
{
    echo "<script>
        alert('$msg');
        window.history.back();
    </script>";
    exit();
}

/* Validate input */
if(empty($bookingID) || empty($roomID) || empty($issueType) || empty($severity) || empty($description)
)
{
    backWithAlert("Please fill in all required fields.");
}

/* Save into database */
$sql = "INSERT INTO room_issue_report (booking_id, room_id, user_id, issue_type, description, severity)
VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

/* Prepare failed fallback */
if(!$stmt)
{
    backWithAlert(
        "System error: Unable to prepare report. Please try again."
    );
}

/* Bind data */
$stmt->bind_param("iiisss", $bookingID, $roomID, $userID, $issueType, $description, $severity);

/* Execute failed fallback */
if(!$stmt->execute())
{
    error_log(
        "Issue Report Insert Failed: " .
        $stmt->error
    );

    backWithAlert("Failed to submit report. Please try again.");
}

/* Save successful */
$stmt->close();

echo "<script>

    alert('Issue report submitted successfully!');

    setTimeout(() =>
    {
        window.location.href =
        'room_session.php?booking_id=$bookingID&report_sent=1';

    }, 800);

</script>";

exit();

?>