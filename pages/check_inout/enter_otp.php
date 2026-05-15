<?php

session_start();

if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ["student", "lecturer"]))
{
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

date_default_timezone_set("Asia/Kuala_Lumpur");

$role = $_SESSION['role'] ?? 'student';

$dashboard = ($role === "lecturer")
    ? "../lecturer/checkin.php"
    : "../student/checkin.php";

/* Show error and redirect user to their dashboard */
function fail($msg)
{
    $role = $_SESSION['role'] ?? 'student';

    if($role === "lecturer") {
        $redirect = "../lecturer/dashboard.php";
    } else {
        $redirect = "../student/dashboard.php";
    }

    echo "<script>
        alert('$msg');
        window.location.href = '$redirect';
    </script>";

    exit();
}

/* Validate booking ID */
if(!isset($_GET['booking_id']))
{
    fail("Booking ID missing");
}

$bookingID = intval($_GET['booking_id']);
$userID = $_SESSION['user_id'];

/* Get booking data */
$sql = "SELECT 
    rb.*,
    r.room_name,
    r.room_type,
    r.block,
    r.level,
    r.room_number,
    r.cover_image
FROM room_booking rb
INNER JOIN room r ON rb.room_id = r.room_id
WHERE rb.booking_id = ? AND rb.user_id = ? LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $bookingID, $userID);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0)
{
    fail("Booking not found");
}

$booking = $result->fetch_assoc();

$currentTime = time();

$bookingStart = strtotime($booking['booking_date'] ." " .$booking['start_time']);

$bookingEnd = strtotime($booking['booking_date'] . " " . $booking['end_time']);

$allowCheckin = $bookingStart - 600;
$closeCheckin = $bookingStart + 1800;

if($currentTime < $allowCheckin || $currentTime > $closeCheckin)
{
    fail("Check in unavailable");
}

$image = "../../uploads/room/default-room.jpg";

if(!empty($booking['cover_image']))
{
    $image = "../../uploads/room/" . $booking['cover_image'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>APCampusHub</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../../assets/css/check_inout/enter_otp.css">

</head>
<body>

<!-- TOPBAR -->
<div class="topbar profile-topbar">

    <div class="profile-topbar-left">

        <a href="$dashboard" class="back-btn">

            <img src="../../assets/icons/back.png" class="back-icon">

        </a>

        <h2>Room Check In</h2>

    </div>

</div>

<div class="otp-container">

    <div class="otp-card">

        <img src="<?php echo $image; ?>" class="otp-image">

        <div class="otp-body">

            <h1 class="otp-title">

                Enter OTP
                
            </h1>

            <p class="otp-subtitle">

                Enter the 3-digit OTP displayed
                in the room projector or screen.

            </p>

            <div class="room-info">

                <p>
                    <strong>
                        <?php
                        echo $booking['room_name'];
                        ?>
                    </strong>
                </p>

                <p>

                    <?php
                    echo date(
                        "d M Y",
                        strtotime(
                            $booking['booking_date']
                        )
                    );
                    ?>

                </p>

                <p>

                    <?php
                    echo date(
                        "g:i A",
                        strtotime(
                            $booking['start_time']
                        )
                    );

                    echo " - ";

                    echo date(
                        "g:i A",
                        strtotime(
                            $booking['end_time']
                        )
                    );
                    ?>

                </p>

            </div>

            <div class="error-msg" id="errorMsg">

                Invalid OTP

            </div>

            <form method="POST" action="process_checkin.php" id="otpForm">

                <input type="hidden" name="booking_id" value="<?php echo $bookingID; ?>">

                <input type="hidden" name="otp" id="finalOtp">

                <div class="otp-input-group">

                    <input type="text" maxlength="1" class="otp-input" id="otp1">

                    <input type="text" maxlength="1" class="otp-input" id="otp2">

                    <input type="text" maxlength="1" class="otp-input" id="otp3">

                </div>

                <button type="submit" class="verify-btn">

                    Verify & Check In

                </button>

            </form>

            <a href="$dashboard" class="back-btn-page">

                Back To Check In

            </a>

        </div>

    </div>

</div>

<script>

const inputs = document.querySelectorAll(".otp-input");

const finalOtp = document.getElementById("finalOtp");

/* Auto move */
inputs.forEach((input, index) =>
{
    input.addEventListener("input", () =>
    {
        input.value = input.value.replace(/[^0-9]/g,'');

        if(input.value && index < inputs.length - 1)
        {
            inputs[index + 1].focus();
        }

        updateOTP();
    });

    input.addEventListener("keydown", (e) =>
    {
        if(e.key === "Backspace" && !input.value && index > 0)
        {
            inputs[index - 1].focus();
        }
    });
});

/* Update OTP */
function updateOTP()
{
    let otp = "";

    inputs.forEach(input =>
    {
        otp += input.value;
    });

    finalOtp.value = otp;
}

document.getElementById("otpForm").addEventListener("submit", function(e)
{
    if(finalOtp.value.length !== 3)
    {
        e.preventDefault();

        document
        .getElementById("errorMsg")
        .style.display = "block";
    }
});

</script>

</body>
</html>