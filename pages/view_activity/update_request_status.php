<?php

session_start();
require_once("../../config/db.php");

/* Staff only */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== "staff")
{
    echo "<script>
        alert('Access denied.');
        window.location.href = '../auth/login.php';
    </script>";
    exit();
}

/* Validate POST */
if(!isset($_POST['request_id'], $_POST['status']))
{
    echo "<script>
        alert('Invalid request.');
        window.location.href = '../staff/view_activity.php';
    </script>";
    exit();
}

$requestID = intval($_POST['request_id']);
$status = trim($_POST['status']);

/* Allowed statuses */
$allowedStatus = ["Pending", "In Progress", "Done"];

if(!in_array($status, $allowedStatus))
{
    echo "<script>
        alert('Invalid status selected.');
        window.history.back();
    </script>";
    exit();
}

/* Check request exists */
$sqlCheck = "SELECT request_id FROM room_service_request WHERE request_id = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("i", $requestID);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if($result->num_rows == 0)
{
    echo "<script>
        alert('Request not found.');
        window.location.href = '../staff/view_activity.php';
    </script>";
    exit();
}

/* Update status */
$sqlUpdate = "UPDATE room_service_request SET status = ? WHERE request_id = ?";
$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param("si", $status, $requestID);

if($stmtUpdate->execute())
{
    echo "<script>
        alert('Request status updated successfully.');
        window.location.href = '../staff/view_activity.php';
    </script>";
}
else
{
    echo "<script>
        alert('Failed to update status.');
        window.history.back();
    </script>";
}

exit();

?>