<?php

session_start();

require_once("../../config/db.php");

$role = $_SESSION['role'] ?? 'student';

$dashboard = ($role === "lecturer")
    ? "../lecturer/dashboard.php"
    : "../student/dashboard.php";

if(!isset($_GET['booking_id'])) {
    echo "<script>
        alert('Missing booking ID');
        window.location.href = '$dashboard';
    </script>";
    exit();
}

$userID = $_SESSION['user_id'];
$bookingID = intval($_GET['booking_id']);

/* Get room and booking data */
$sql = "SELECT rb.*, r.room_id, r.room_name, r.room_type, r.block, r.level FROM room_booking rb
INNER JOIN room r ON rb.room_id = r.room_id
WHERE rb.booking_id = ? AND rb.user_id = ? LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $bookingID, $userID);
$stmt->execute();

$booking = $stmt->get_result()->fetch_assoc();

if(!$booking) {
    echo "<script>
        alert('Invalid booking');
        window.location.href = '$dashboard';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APCampusHub</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../../assets/css/check_inout/room_service.css">

</head>
<body>

    <!-- Topbar -->
    <div class="topbar profile-topbar">

        <div class="profile-topbar-left">

            <a href="room_session.php?booking_id=<?php echo $bookingID; ?>" class="back-btn">
                <img src="../../assets/icons/back.png" class="back-icon">
            </a>

            <h2>Service Request</h2>

        </div>
    </div>

    <!-- Content -->
    <div class="container">

        <!-- Header Card -->
        <div class="room-card">

            <h2>
                Request for <?php echo $booking['room_name']; ?>
            </h2>

            <p>
                <?php echo $booking['room_type']; ?> •
                Block <?php echo $booking['block']; ?> •
                Level <?php echo $booking['level']; ?>
            </p>

        </div>

        <!-- Form -->
        <div class="section">

            <h3>
                <img src="../../assets/icons/help.png" class="title-icon">
                Submit Request
            </h3>

            <form method="POST" action="process_room_service.php">

                <input type="hidden" name="booking_id" value="<?php echo $bookingID; ?>">
                <input type="hidden" name="room_id" value="<?php echo $booking['room_id']; ?>">

                <div class="form-group">
                    <label>Request Type</label>
                    <select name="request_type" id="requestType" required>
                        <option value="" disabled selected>-- Please select one --</option>
                        <option value="Borrow Resource">Borrow Resource</option>
                        <option value="Technical Issue">Technical Issue</option>
                        <option value="Staff Assistance">Staff Assistance</option>
                        <option value="General Request">General Request</option>
                    </select>
                </div>

                <div id="resourceBox" class="form-group" style="display:none;">
                    <label>Resource Type</label>
                    <select name="resource_type">
                        <option value="" disabled selected>-- Please select one --</option>
                        <option value="Cables">Cables</option>
                        <option value="Extension Plug">Extension Plug</option>
                        <option value="Stationary">Stationary</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" placeholder="Please describe your request..." required></textarea>
                </div>

                <button type="submit" class="btn">
                    Submit Request
                </button>

            </form>

        </div>

    </div>

<script>
const type = document.getElementById("requestType");
const box = document.getElementById("resourceBox");

type.addEventListener("change", function () {
    box.style.display = (this.value === "Borrow Resource") ? "flex" : "none";
});
</script>

</body>
</html>