<?php

session_start();

require_once("../../config/db.php");

/* Staff only */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== "staff")
{
    header("Location: ../auth/login.php");
    exit();
}

/* Validate ID */
if(!isset($_GET['id']))
{
    header("Location: ../staff/manage_resource.php");
    exit();
}

$resourceID = intval($_GET['id']);

/* Get resource */
$sql = "SELECT * FROM resource WHERE resource_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $resourceID);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0)
{
    echo "<script>
        alert('Resource not found.');
        window.location.href='../staff/manage_resource.php';
    </script>";
    exit();
}

$resource = $result->fetch_assoc();

/* Update */
if($_SERVER["REQUEST_METHOD"] === "POST")
{
    $name = trim($_POST['resource_name']);
    $type = $_POST['resource_type'];
    $qty  = intval($_POST['quantity']);
    $desc = trim($_POST['description']);
    $status = $_POST['status'];

    $allowedStatus = ["Available", "Disabled"];

    if(!in_array($status, $allowedStatus))
    {
        echo "<script>alert('Invalid status'); window.history.back();</script>";
        exit();
    }

    $sqlUpdate = "UPDATE resource 
                  SET resource_name=?, resource_type=?, quantity=?, description=?, status=?
                  WHERE resource_id=?";

    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ssissi", $name, $type, $qty, $desc, $status, $resourceID);
    $stmtUpdate->execute();

    echo "<script>
        alert('Resource updated successfully!');
        window.location.href='../staff/manage_resource.php';
    </script>";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APCampusHub</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../../assets/css/manage_resource/edit_resource.css">
</head>
<body>

    <!-- Topbar -->
    <div class="topbar profile-topbar">

        <div class="profile-topbar-left">

            <a href="../staff/manage_resource.php" class="back-btn">
                <img src="../../assets/icons/back.png" class="back-icon">
            </a>

            <h2>Edit Resource</h2>

        </div>
    </div>

    <div class="container">

        <form method="POST" class="edit-form">

            <label>Resource Name</label>
            <input type="text" name="resource_name" value="<?php echo htmlspecialchars($resource['resource_name']); ?>" required>

            <label>Resource Type</label>
            <select name="resource_type">
                <option value="Cables" <?php if($resource['resource_type']=="Cables") echo "selected"; ?>>Cables</option>
                <option value="Extension Plug" <?php if($resource['resource_type']=="Extension Plug") echo "selected"; ?>>Extension Plug</option>
                <option value="Stationary" <?php if($resource['resource_type']=="Stationary") echo "selected"; ?>>Stationary</option>
            </select>

            <label>Quantity</label>
            <input type="number" name="quantity" value="<?php echo $resource['quantity']; ?>" min="1">

            <label>Description</label>
            <textarea name="description"><?php echo htmlspecialchars($resource['description']); ?></textarea>

            <label>Status</label>
            <select name="status">
                <option value="Available" <?php if($resource['status']=="Available") echo "selected"; ?>>Available</option>
                <option value="Disabled" <?php if($resource['status']=="Disabled") echo "selected"; ?>>Disabled</option>
            </select>

            <button type="submit">Update Resource</button>

        </form>

    </div>

</body>
</html>