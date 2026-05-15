<?php

session_start();

require_once("../../config/db.php");

if(!isset($_POST['room_id']) || !isset($_POST['type']) || !isset($_POST['value']))
{
    exit("Invalid");
}

$roomID = intval($_POST['room_id']);

$type = $_POST['type'];

$value = $_POST['value'];

/* Projector */

if($type == "projector")
{
    $sql = "UPDATE room_iot_state SET projector = ? WHERE room_id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("si", $value, $roomID);

    $stmt->execute();

    exit("success");
}

/* Light */

if($type == "light")
{
    $brightness = intval($value);

    $sql = "UPDATE room_iot_state SET lights_brightness = ? WHERE room_id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ii", $brightness, $roomID);

    $stmt->execute();

    exit("success");
}

/* Aircond */

if($type == "ac")
{
    $temp = intval($value);

    $sql = "UPDATE room_iot_state SET ac_temperature = ? WHERE room_id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ii", $temp, $roomID);

    $stmt->execute();

    exit("success");
}
?>