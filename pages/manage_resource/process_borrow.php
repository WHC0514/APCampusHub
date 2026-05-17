<?php

session_start();
require_once("../../config/db.php");

/* Staff only */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== "staff")
{
    header("Location: ../auth/login.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] !== "POST")
{
    header("Location: ../staff/manage_resource.php");
    exit();
}

$resourceID = intval($_POST['resource_id']);
$userID     = intval($_POST['user_id']);
$quantity   = intval($_POST['quantity']);

/* Check resource */
$sql = "SELECT * FROM resource WHERE resource_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $resourceID);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0)
{
    echo "<script>alert('Resource not found'); window.history.back();</script>";
    exit();
}

$resource = $result->fetch_assoc();

/* Check stock */
if($quantity > $resource['quantity'])
{
    echo "<script>alert('Not enough stock'); window.history.back();</script>";
    exit();
}

/* Reduce stock */
$newQty = $resource['quantity'] - $quantity;

$update = "UPDATE resource SET quantity = ? WHERE resource_id = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("ii", $newQty, $resourceID);
$stmt->execute();

/* Insert borrow record */
$insert = "INSERT INTO resource_borrow (resource_id, user_id, quantity, borrow_time, status)
           VALUES (?, ?, ?, NOW(), 'Borrowed')";

$stmt = $conn->prepare($insert);
$stmt->bind_param("iii", $resourceID, $userID, $quantity);
$stmt->execute();

/* Get borrow ID */
$borrowID = $stmt->insert_id;

/* Log usage */
$log = "INSERT INTO resource_usage_log (borrow_id, action, action_time)
        VALUES (?, 'Borrow', NOW())";

$stmt = $conn->prepare($log);
$stmt->bind_param("i", $borrowID);
$stmt->execute();

echo "<script>
    alert('Resource borrowed successfully');
    window.location.href='../staff/manage_resource.php';
</script>";

exit();

?>