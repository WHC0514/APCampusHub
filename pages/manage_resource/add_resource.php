<?php
session_start();
require_once("../../config/db.php");

/* Staff only */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== "staff")
{
    header("Location: ../auth/login.php");
    exit();
}

/* Handle form submit */
if($_SERVER["REQUEST_METHOD"] === "POST")
{
    $name = trim($_POST['resource_name']);
    $type = $_POST['resource_type'];
    $qty  = intval($_POST['quantity']);
    $desc = trim($_POST['description']);
    $status = "Available";

    if(empty($name) || empty($type) || $qty <= 0)
    {
        echo "<script>alert('Invalid input'); window.history.back();</script>";
        exit();
    }

    $sql = "INSERT INTO resource (resource_name, resource_type, quantity, description, status, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiss", $name, $type, $qty, $desc, $status);
    $stmt->execute();

    echo "<script>
        alert('Resource added successfully!');
        window.location.href = '../staff/manage_resource.php';
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
    <link rel="stylesheet" href="../../assets/css/manage_resource/add_resource.css">
</head>
<body>

    <!-- Topbar -->
    <div class="topbar profile-topbar">

        <div class="profile-topbar-left">

            <a href="../staff/manage_resource.php" class="back-btn">
                <img src="../../assets/icons/back.png" class="back-icon">
            </a>

            <h2>Add Resource</h2>

        </div>
    </div>

    <div class="container">

        <form method="POST" class="resource-form">

            <label>Resource Name</label>
            <input type="text" name="resource_name" required>

            <label>Resource Type</label>
            <select name="resource_type" required>
                <option value="Cables">Cables</option>
                <option value="Extension Plug">Extension Plug</option>
                <option value="Stationary">Stationary</option>
            </select>

            <label>Quantity</label>
            <input type="number" name="quantity" min="1" required>

            <label>Description</label>
            <textarea name="description"></textarea>

            <button type="submit">Add Resource</button>

        </form>

    </div>

</body>
</html>