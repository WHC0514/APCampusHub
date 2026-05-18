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

            <h2>Report Issue</h2>

        </div>
    </div>

    <!-- Content -->
    <div class="container">

        <div class="room-card">

            <h2>
                Report Issue for <?php echo $booking['room_name']; ?>
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
                <img src="../../assets/icons/report.png" class="title-icon">
                Submit Issue Report
            </h3>

            <form method="POST" action="process_report_issue.php">

                <input type="hidden" name="booking_id" value="<?php echo $bookingID; ?>">
                <input type="hidden" name="room_id" value="<?php echo $booking['room_id']; ?>">

                <div class="form-group">
                    <label>Issue Type</label>
                    <select name="issue_type" required>
                        <option value="" disabled selected>-- Please select one --</option>
                        <option value="Projector Issue">Projector Issue</option>
                        <option value="Lighting Problem">Lighting Problem</option>
                        <option value="Air Conditioner Issue">Air Conditioner Issue</option>
                        <option value="Power/Electricity Issue">Power/Electricity Issue</option>
                        <option value="Furniture Damage">Furniture Damage</option>
                        <option value="Cleanliness Issue">Cleanliness Issue</option>
                        <option value="Internet/WiFi Issue">Internet/WiFi Issue</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Severity</label>

                    <select name="severity" required>
                        <option value="" disabled selected>-- Please select one --</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Describe the issue..." required></textarea>
                </div>

                <button type="submit" class="btn">
                    Submit Report
                </button>

            </form>

        </div>

    </div>

</body>
</html>