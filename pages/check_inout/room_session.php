<?php

session_start();

require_once("../../config/db.php");

date_default_timezone_set("Asia/Kuala_Lumpur");

if(!isset($_GET['booking_id']))
{
    die("Missing booking");
}

$userID = $_SESSION['user_id'];
$bookingID = intval($_GET['booking_id']);

/* Get session data */

$sql = "SELECT
    rb.*,
    r.room_name,
    r.cover_image,
    r.room_id,
    r.room_type,
    r.block,
    r.level
FROM room_booking rb
INNER JOIN room r ON rb.room_id = r.room_id
WHERE rb.booking_id = ? AND rb.user_id = ? LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $bookingID, $userID);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0)
{
    die("Invalid session");
}

$booking = $result->fetch_assoc();

$roomID = $booking['room_id'];

/* Get room image */

$roomImage = "../../uploads/room/default-room.jpg";

if(!empty($booking['cover_image']))
{
    $roomImage ="../../uploads/room/" . $booking['cover_image'];
}

/* Get session time */

$start =
date(
    "g:i A",
    strtotime($booking['start_time'])
);

$end =
date(
    "g:i A",
    strtotime($booking['end_time'])
);

/* Get session end timestamp */

$sessionEndTimestamp = strtotime(
    $booking['booking_date'] .
    " " .
    $booking['end_time']
);

$currentTimestamp = time();

$remainingSeconds =
$sessionEndTimestamp - $currentTimestamp;

/* Get IOT state */

$sqlIOT = "SELECT * FROM room_iot_state WHERE room_id = ? LIMIT 1";

$stmtIOT = $conn->prepare($sqlIOT);
$stmtIOT->bind_param("i", $roomID);
$stmtIOT->execute();

$iotResult = $stmtIOT->get_result();

if($iotResult->num_rows > 0)
{
    $iot = $iotResult->fetch_assoc();

    $projectorStatus =
    $iot['projector'];

    $lightBrightness =
    $iot['lights_brightness'];

    $acTemperature =
    $iot['ac_temperature'];
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>APCampusHub</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../../assets/css/check_inout/session.css">

</head>

<body>

<!-- Topbar -->
    <div class="topbar profile-topbar">

        <div class="profile-topbar-left">

            <!-- Back Button -->
            <a href="../student/dashboard.php" class="back-btn">

                <img src="../../assets/icons/back.png" class="back-icon">

            </a>

            <h2>Smart Room Control</h2>

        </div>
    </div>

    <!-- Content -->

<div class="otp-wrapper">

    <div class="dashboard-container">

        <!-- Room Header -->
        <div class="room-header">

            <img src="<?php echo $roomImage; ?>"
                 class="room-image">

            <h1 class="welcome-title">

                Welcome to
                <?php echo $booking['room_name']; ?>

            </h1>

            <div class="room-subinfo">

                <?php echo $booking['room_type']; ?>

                •

                Block
                <?php echo $booking['block']; ?>

                •

                Level
                <?php echo $booking['level']; ?>

            </div>

        </div>

        <!-- Session Card -->
        <div class="session-card">

            <div class="session-info">

                <h3>
                    Current Active Session
                </h3>

                <p>

                    <?php echo $start; ?>

                    -

                    <?php echo $end; ?>

                </p>

            </div>

            <button class="checkout-btn" onclick="manualCheckout()">

                Check Out

            </button>

        </div>

        <!-- Control Grid -->
        <div class="control-grid">

            <!-- Projector -->
            <div class="control-card">

                <div class="control-top">

                    <div>

                        <h3>

                            <img src="../../assets/icons/projector.png" class="control-icon">
                            Projector Control

                        </h3>

                        <p>
                            Control room projector power
                        </p>

                    </div>

                    <div class="status-badge 
                    <?php echo ($projectorStatus == 'ON') 
                    ? 'green-status' 
                    : 'red-status'; ?>" id="projectorBadge">

                        <?php echo $projectorStatus; ?>

                    </div>

                </div>

                <div class="btn-group">

                    <button class="btn green" onclick="updateProjector('ON')">

                        Turn ON

                    </button>

                    <button class="btn red" onclick="updateProjector('OFF')">

                        Turn OFF

                    </button>

                </div>

            </div>

            <!-- Light -->
            <div class="control-card">

                <h3>

                    <img src="../../assets/icons/bulb.png" class="control-icon">
                    Light Brightness

                </h3>

                <p>
                    Adjust room lighting intensity
                </p>

                <input type="hidden" id="roomID" value="<?php echo $roomID; ?>">

                <div class="range-value">

                    Current:
                    <span id="brightnessText">

                        <?php echo $lightBrightness; ?>

                    </span>%

                </div>

                <input type="range" id="brightnessSlider" min="0" max="100" value="<?php echo $lightBrightness; ?>">

                <button class="btn blue full-btn" onclick="updateBrightness()">

                    Update Brightness

                </button>

            </div>

            <!-- Aircond -->
            <div class="control-card">

                <h3>

                    <img src="../../assets/icons/freeze.png" class="control-icon">
                    Air Conditioner

                </h3>

                <p>
                    Adjust room temperature
                </p>

                <div class="temp-display" id="tempText">

                    <?php echo $acTemperature; ?>°C

                </div>

                <input type="number" id="tempInput" min="16" max="30" value="<?php echo $acTemperature; ?>">

                <button class="btn blue full-btn" onclick="updateAC()">

                    Set Temperature

                </button>

            </div>

            <!-- HELP / REPORT -->
            <div class="control-card">

                <h3>

                    <img src="../../assets/icons/help.png" class="control-icon">
                    Assistance & Support

                </h3>

                <p>
                    Request staff assistance or
                    report room issues
                </p>

                <div class="support-btn-group">

                    <a href="request_help.php?booking_id=<?php echo $bookingID; ?>" class="btn blue">

                        Request Assist

                    </a>

                    <a href="report_issue.php?booking_id=<?php echo $bookingID; ?>" class="btn orange">

                        Submit Report

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- Session Warning -->

<div class="session-warning" id="sessionWarning">

    <div class="warning-card">

        <!-- Close Button -->
        <button class="close-warning-btn" onclick="closeWarning()">

            <img src="../../assets/icons/close.png" class="close-icon">

        </button>

        <div class="warning-icon">

            <img src="../../assets/icons/clock.png" class="warning-icon-img">

        </div>

        <h2>
            Session Ending Soon
        </h2>

        <p>
            Your booking session will end
            in less than 5 minutes.
        </p>

    </div>

</div>

<!-- Session Ended -->

<div class="session-ended" id="sessionEnded">

    <div class="ended-card">

        <div class="ended-icon">

            <img src="../../assets/icons/door.png" class="ended-image-icon">

        </div>

        <h2>
            Session Ended
        </h2>

        <p>

            Your booking session has ended.

            <br><br>

            Please leave the classroom
            as soon as possible.

        </p>

    </div>

</div>

</body>

<script>

/* Session timer */

const remainingSeconds =
<?php echo $remainingSeconds; ?>;

/* Show warning before 5 min session end */

if(
    remainingSeconds <= 300 &&
    remainingSeconds > 0
)
{
    document.getElementById("sessionWarning").style.display = "flex";
}

/* Close warning popup */

function closeWarning()
{
    document.getElementById("sessionWarning").style.display = "none";
}

/* Auto checkout */

setTimeout(() =>
{
    autoCheckout();

}, remainingSeconds * 1000);

/* Auto checkout function */

function autoCheckout() {
    fetch("auto_checkout.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "booking_id=<?php echo $bookingID; ?>"
    })
    .then(res => res.text())
    .then(data => {

        // Replace body content with ended screen
        document.body.innerHTML = `
            <div class="session-ended" style="display:flex;">
                <div class="ended-card">
                    <div class="ended-icon">
                        <img src="../../assets/icons/door.png" class="ended-image-icon">
                    </div>
                    <h2>Session Ended</h2>
                    <p>Your booking session has ended.<br><br>Please leave the classroom as soon as possible.</p>
                </div>
            </div>
        `;

        setTimeout(() => {
            window.location.href = "../student/dashboard.php?session_ended=1";
        }, 4000);
    });
}

/*vManual checkout */

function manualCheckout()
{
    if(!confirm("Are you sure you want to check out?"))
    {
        return;
    }

    fetch("auto_checkout.php",
    {
        method: "POST",

        headers:
        {
            "Content-Type":
            "application/x-www-form-urlencoded"
        },

        body:
        "booking_id=<?php echo $bookingID; ?>"
    })
    .then(res => res.text())
    .then(data =>
    {
        window.location.href ="../student/dashboard.php?checkout_success=1";
    });
}

</script>

<script>

/* Text for live brightness */

const slider = document.getElementById("brightnessSlider");

const brightnessText = document.getElementById("brightnessText");

slider.addEventListener("input", () =>
{
    brightnessText.innerHTML =
    slider.value;
});

/* Update projector status */

function updateProjector(state)
{
    const roomID = document.getElementById("roomID").value;

    fetch("update_iot.php",
    {
        method:"POST",

        headers:
        {
            "Content-Type":
            "application/x-www-form-urlencoded"
        },

        body:
        `room_id=${roomID}&type=projector&value=${state}`
    })
    .then(res => res.text())
    .then(data =>
    {
        const badge =
        document.getElementById(
            "projectorBadge"
        );

        // Update text
        badge.innerHTML = state;

        // Remove old colors
        badge.classList.remove(
            "green-status",
            "red-status"
        );

        // Add correct color
        if(state === "ON")
        {
            badge.classList.add(
                "green-status"
            );
        }
        else
        {
            badge.classList.add(
                "red-status"
            );
        }
    });
}

/* Update light status */

function updateBrightness()
{
    const roomID = document.getElementById("roomID").value;

    const brightness = slider.value;

    fetch("update_iot.php",
    {
        method:"POST",

        headers:
        {
            "Content-Type":
            "application/x-www-form-urlencoded"
        },

        body:
        `room_id=${roomID}&type=light&value=${brightness}`
    })
    .then(res => res.text())
    .then(data =>
    {
        alert("Brightness updated");
    });
}

/* Update aircond status */

function updateAC()
{
    const roomID = document.getElementById("roomID").value;

    const temp = document.getElementById("tempInput").value;

    fetch("update_iot.php",
    {
        method:"POST",

        headers:
        {
            "Content-Type":
            "application/x-www-form-urlencoded"
        },

        body:
        `room_id=${roomID}&type=ac&value=${temp}`
    })
    .then(res => res.text())
    .then(data =>
    {
        document.getElementById("tempText").innerHTML = temp + "°C";

        alert("Temperature updated");
    });
}
</script>

</html>