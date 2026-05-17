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

$borrowID = intval($_POST['borrow_id']);

/* Get borrow data */
$sql = "SELECT rb.*, r.resource_id, r.quantity AS current_stock FROM resource_borrow rb
LEFT JOIN resource r ON rb.resource_id = r.resource_id
WHERE rb.borrow_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $borrowID);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0)
{
    echo "<script>
        alert('Borrow record not found.');
        window.location.href='../staff/manage_resource.php';
    </script>";
    exit();
}

$data = $result->fetch_assoc();

/* Check already returned or not */
if($data['status'] === "Returned")
{
    echo "<script>
        alert('This resource is already returned.');
        window.location.href='../staff/manage_resource.php';
    </script>";
    exit();
}

$resourceID = $data['resource_id'];
$borrowQty  = $data['quantity'];

/* Update borrow status */
$update = "UPDATE resource_borrow SET status='Returned', return_time=NOW() WHERE borrow_id=?";
$stmt = $conn->prepare($update);
$stmt->bind_param("i", $borrowID);
$stmt->execute();

/* Restore resource stock */
$restore = "UPDATE resource 
            SET quantity = quantity + ? 
            WHERE resource_id = ?";

$stmt = $conn->prepare($restore);
$stmt->bind_param("ii", $borrowQty, $resourceID);
$stmt->execute();

/* Insert usage log */
$log = "INSERT INTO resource_usage_log (borrow_id, action, action_time)
        VALUES (?, 'Return', NOW())";

$stmt = $conn->prepare($log);
$stmt->bind_param("i", $borrowID);
$stmt->execute();

/* Success */
echo "<script>
    alert('Resource returned successfully!');
    window.location.href='../staff/manage_resource.php';
</script>";

exit();

?>