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
if(!isset($_POST['report_id'], $_POST['status']))
{
    echo "<script>
        alert('Invalid request.');
        window.location.href = '../staff/view_activity.php';
    </script>";
    exit();
}

$reportID = intval($_POST['report_id']);
$status = trim($_POST['status']);

/* Allowed statuses */
$allowed = ["Pending", "In Progress", "Resolved"];

if(!in_array($status, $allowed))
{
    echo "<script>
        alert('Invalid status.');
        window.history.back();
    </script>";
    exit();
}

/* Check exists */
$sqlCheck = "SELECT report_id FROM room_issue_report WHERE report_id = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("i", $reportID);
$stmtCheck->execute();

$result = $stmtCheck->get_result();

if($result->num_rows == 0)
{
    echo "<script>
        alert('Report not found.');
        window.location.href = '../staff/view_activity.php';
    </script>";
    exit();
}

/* Update */
$sql = "UPDATE room_issue_report SET status = ? WHERE report_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $reportID);

if($stmt->execute())
{
    echo "<script>
        alert('Report updated successfully.');
        window.location.href = '../staff/view_activity.php';
    </script>";
}
else
{
    echo "<script>
        alert('Update failed.');
        window.history.back();
    </script>";
}

exit();

?>