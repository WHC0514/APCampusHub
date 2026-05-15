<?php

session_start();

require_once("../../config/db.php");

date_default_timezone_set("Asia/Kuala_Lumpur");

/* Validate room ID */
if(!isset($_GET['room_id']))
{
    die("Room ID missing");
}

$roomID = intval($_GET['room_id']);
$userID = $_SESSION['user_id'] ?? 0;

/* Get room data */
$sqlRoom = "SELECT * FROM room WHERE room_id = ? LIMIT 1";

$stmtRoom = $conn->prepare($sqlRoom);
$stmtRoom->bind_param("i", $roomID);
$stmtRoom->execute();

$roomResult = $stmtRoom->get_result();

if($roomResult->num_rows == 0)
{
    die("Room not found");
}

$room = $roomResult->fetch_assoc();

/* Get room's cover photo */
$coverImage = "../../uploads/room/default-room.jpg";

if(!empty($room['cover_image']))
{
    $coverImage = "../../uploads/room/" . $room['cover_image'];
}

/* Get current booking session */
$currentDate = date("Y-m-d");
$currentTime = date("H:i:s");

$sqlBooking = "SELECT rb.*, s.name AS student_name, l.name AS lecturer_name FROM room_booking rb
LEFT JOIN student s ON rb.user_id = s.user_id
LEFT JOIN lecturer l ON rb.user_id = l.user_id
WHERE rb.room_id = ? AND rb.booking_date = ? AND rb.start_time <= ? AND rb.end_time >= ? LIMIT 1";

$stmtBooking = $conn->prepare($sqlBooking);
$stmtBooking->bind_param("isss", $roomID, $currentDate, $currentTime, $currentTime);

$stmtBooking->execute();
$resBooking = $stmtBooking->get_result();

$currentBooking = null;
$userName = "Guest";
$sessionStart = null;
$sessionEnd = null;

if($resBooking->num_rows > 0)
{
    $currentBooking = $resBooking->fetch_assoc();

    $userName = $currentBooking['student_name']
              ?? $currentBooking['lecturer_name']
              ?? "Guest";

    $sessionStart = $currentBooking['start_time'];
    $sessionEnd = $currentBooking['end_time'];
}

/* Check if user already checked in for current session */
$alreadyCheckedIn = false;

if($userID && $currentBooking)
{
    $sqlCheck = "
    SELECT *
    FROM room_checkin
    WHERE booking_id = ?
    AND actual_checkout IS NULL
    LIMIT 1
    ";

    $stmtCheck = $conn->prepare($sqlCheck);

    $stmtCheck->bind_param(
        "i",
        $currentBooking['booking_id']
    );

    $stmtCheck->execute();

    $checkResult = $stmtCheck->get_result();

    if($checkResult->num_rows > 0)
    {
        $alreadyCheckedIn = true;
    }
}

/* Get latest OTP */
$sqlOTP = "SELECT * FROM room_otp WHERE room_id = ? ORDER BY generated_at DESC LIMIT 1";

$stmtOTP = $conn->prepare($sqlOTP);
$stmtOTP->bind_param("i", $roomID);
$stmtOTP->execute();

$otpResult = $stmtOTP->get_result();

$currentOTP = "";
$secondsLeft = 30;

if($otpResult->num_rows > 0)
{
    $otpData = $otpResult->fetch_assoc();

    $generatedTime = strtotime($otpData['generated_at']);
    $diff = time() - $generatedTime;

    if($diff < 30)
    {
        $currentOTP = $otpData['otp_code'];
        $secondsLeft = 30 - $diff;
    } else {
        $currentOTP = null;
    }
}

/* Generate new OTP every 30s */
if(!$alreadyCheckedIn && empty($currentOTP))
{
    $currentOTP = str_pad(rand(0, 999), 3, "0", STR_PAD_LEFT);

    $insert = "
    INSERT INTO room_otp (room_id, otp_code)
    VALUES (?, ?)
    ";

    $stmtInsert = $conn->prepare($insert);
    $stmtInsert->bind_param("is", $roomID, $currentOTP);
    $stmtInsert->execute();

    $secondsLeft = 30;
}

/* Stop generate if checked in */
if($alreadyCheckedIn)
{
    $secondsLeft = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Display</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/check_inout/room_display.css">

</head>
<body>

    <div class="otp-wrapper">
        <div class="otp-card">

            <img src="<?php echo $coverImage; ?>" class="room-image">

            <div class="otp-body">

                <h1 class="room-name">
                    <?php echo $room['room_name']; ?>
                </h1>

                <div class="room-info">
                    <?php echo $room['room_type']; ?> •
                    Block <?php echo $room['block']; ?> •
                    Level <?php echo $room['level']; ?>
                </div>

                <?php if($alreadyCheckedIn && $currentBooking): ?>

                    <!-- Smart Display -->
                    <div class="success-box">

                        <h2>
                            Welcome to <?php echo $room['room_name']; ?>,
                            <?php echo $userName; ?>
                        </h2>

                        <p>
                            Current Session Time:<br>
                            <strong>
                                <?php echo $sessionStart; ?>
                                to
                                <?php echo $sessionEnd; ?>
                            </strong>
                        </p>

                    </div>

                <?php else: ?>

                    <div class="otp-label">Room OTP</div>

                    <div class="otp-code">
                        <?php echo $currentOTP; ?>
                    </div>

                    <div class="timer-box">
                        Refreshing in <span id="timer"><?php echo $secondsLeft; ?></span>s
                    </div>

                <?php endif; ?>

            </div>

        </div>
    </div>

<?php if(!$alreadyCheckedIn): ?>

<script>

let timeLeft = <?php echo $secondsLeft; ?>;
const timer = document.getElementById("timer");

const interval = setInterval(() => {

    timeLeft--;
    timer.innerText = timeLeft;

    if(timeLeft <= 0)
    {
        clearInterval(interval);
        location.reload();
    }

}, 1000);

</script>
<?php endif; ?>

</body>
</html>