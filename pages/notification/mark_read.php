<?php
session_start();
require_once("../../config/db.php");

if(!isset($_SESSION['user_id'], $_GET['id']))
{
    exit();
}

$id = intval($_GET['id']);

$sql = "UPDATE notification SET is_read = 1 WHERE notification_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: notification.php");
exit();
?>