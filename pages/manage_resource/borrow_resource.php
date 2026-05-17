<?php

session_start();

require_once("../../config/db.php");

/* Staff only */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== "staff")
{
    header("Location: ../auth/login.php");
    exit();
}

/* Get available resource data */
$sql = "SELECT * FROM resource WHERE status = 'Available'";
$result = $conn->query($sql);

/* Get borrow data */
$sqlUsers = "SELECT 
    rsr.user_id,
    rsr.room_id,
    u.role,
    r.room_name
FROM room_service_request rsr
LEFT JOIN user u ON rsr.user_id = u.user_id
LEFT JOIN room r ON rsr.room_id = r.room_id
WHERE rsr.request_type = 'Borrow Resource'
AND rsr.status IN ('Pending', 'In Progress')
GROUP BY rsr.user_id, rsr.room_id";

$userResult = $conn->query($sqlUsers);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APCampusHub</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../../assets/css/manage_resource/borrow_resource.css">
</head>
<body>

    <!-- Topbar -->
    <div class="topbar profile-topbar">

        <div class="profile-topbar-left">

            <a href="../staff/manage_resource.php" class="back-btn">
                <img src="../../assets/icons/back.png" class="back-icon">
            </a>

            <h2>Borrow Resource</h2>

        </div>
    </div>

    <div class="container">

        <form method="POST" action="process_borrow.php" class="borrow-form">

            <!-- Resource -->
            <label>Select Resource</label>
            <select name="resource_id" required>
                <option value="">-- Select Resource --</option>

                <?php while($r = $result->fetch_assoc()) { ?>
                    <option value="<?php echo $r['resource_id']; ?>">
                        <?php echo $r['resource_name']; ?>
                        (<?php echo $r['quantity']; ?> available)
                    </option>
                <?php } ?>

            </select>

            <!-- Borrower  -->
            <label>Select Borrower</label>
            <select name="user_id" required>
                <option value="">-- Select Borrow Request --</option>

                <?php 
                while($u = $userResult->fetch_assoc()) { 

                    $name = "Unknown User";

                    if($u['role'] === "student")
                    {
                        $stmt = $conn->prepare("SELECT name FROM student WHERE student_id = ?");
                    }
                    else if($u['role'] === "lecturer")
                    {
                        $stmt = $conn->prepare("SELECT name FROM lecturer WHERE lecturer_id = ?");
                    }
                    else
                    {
                        $stmt = null;
                    }

                    if($stmt)
                    {
                    $stmt->bind_param("i", $u['user_id']);
                    $stmt->execute();
                    $res = $stmt->get_result();

                    if($row = $res->fetch_assoc())
                    {
                        $name = $row['name'];
                    }
                }

                $display = $name . " (" . $u['room_name'] . ")";
                ?>

                    <option value="<?php echo $u['user_id']; ?>">
                        <?php echo $display; ?>
                    </option>

                <?php } ?>

            </select>

            <!-- Quantity -->
            <label>Quantity</label>
            <input type="number" name="quantity" min="1" required>

            <button type="submit">Borrow Resource</button>

        </form>

    </div>

</body>
</html>