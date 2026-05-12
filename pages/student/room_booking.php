<?php

session_start();

if(!isset($_SESSION['user_id']))
{
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

/* Room Filter */
$filterType = "";

if(isset($_GET['room_type']))
{
    $filterType = $_GET['room_type'];
}

/* Get Room */
if(!empty($filterType))
{
    $sqlRoom = "SELECT * FROM room WHERE room_type = ? AND status = 'Active'";
    $stmtRoom = $conn->prepare($sqlRoom);
    $stmtRoom->bind_param("s", $filterType);
} else {
    $sqlRoom = "SELECT * FROM room WHERE status = 'Active'";
    $stmtRoom = $conn->prepare($sqlRoom);
}

$stmtRoom->execute();
$roomResult = $stmtRoom->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APCampusHub</title>
    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/student/room_booking.css">
</head>
<body>
    
    <!-- Topbar -->
    <div class="topbar">

        <div class="topbar-left">

            <img src="../../assets/images/app-logo.png" class="topbar-logo">

            <div class="search-container">

                <input type="text" class="search-bar" id="searchInput" placeholder="Search APCampusHub">

                <div class="search-result" id="searchResult"></div>

            </div>

        </div>

        <div class="topbar-right">

            <!-- Dashboard Button -->
            <a href="dashboard.php" class="topbar-link">
                <div class="topbar-item">

                    <img src="../../assets/icons/dashboard.png" alt="Dashboard" class="topbar-icon">
                    <span>Dashboard</span>
            
                </div>
            </a>

            <!-- Room Booking Button -->
            <a href="room_booking.php" class="topbar-link active">
                <div class="topbar-item">

                    <img src="../../assets/icons/room-booking.png" alt="Room Booking" class="topbar-icon">
                    <span>Room Booking</span>
            
                </div>
            </a>

            <!-- Check In / Out Button -->
            <a href="#" class="topbar-link">
                <div class="topbar-item">

                    <img src="../../assets/icons/check-in.png" alt="Check In" class="topbar-icon">
                    <span>Check In/Out</span>

                </div>
            </a>

            <!-- Events Button -->
            <a href="#" class="topbar-link">
                <div class="topbar-item">

                    <img src="../../assets/icons/events.png" alt="Events" class="topbar-icon">
                    <span>Events</span>
            
                </div>
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="room-filter-section">

        <form method="GET">

            <select name="room_type" class="filter-select">

                <option value="">All Rooms</option>
                <option value="Discussion Room"
                    <?php 
                        if($filterType == "Discussion Room")
                        {
                            echo "selected";
                        }
                    ?>> Discussion Room</option>

                <option value="Presentation Room"
                    <?php 
                        if($filterType == "Presentation Room")
                        {
                            echo "selected";
                        }
                    ?>> Presentation Room</option>

                <option value="Auditorium"
                    <?php 
                        if($filterType == "Auditorium")
                        {
                            echo "selected";
                        }
                    ?>> Auditorium</option>

                <option value="Classroom"
                    <?php 
                        if($filterType == "Classroom")
                        {
                            echo "selected";
                        }
                    ?>> Classroom</option>

                <option value="Lab"
                    <?php 
                        if($filterType == "Lab")
                        {
                            echo "selected";
                        }
                    ?>> Lab</option>

            </select>

            <button type="submit" class="filter-btn">
                Filter
            </button>

        </form>
    </div>

    <!-- Content -->
    <div class="room-content">

        <div class="room-grid">

            <?php

            if($roomResult->num_rows > 0)
            {
                while($room = $roomResult->fetch_assoc())
                {
                    $roomImage = "../../uploads/room/default-room.jpg";

                    if(!empty($room['cover_image']))
                    {
                        $roomImage = "../../uploads/room/" . $room['cover_image'];
                    }

                    ?>

                <div class="room-card">

                    <!-- Room Image -->
                    <img src="<?php echo $roomImage; ?>" class="room-image">

                    <!-- Room Info -->
                    <div class="room-info">

                        <h2>
                            <?php echo $room['room_name']; ?>
                        </h2>

                        <div class="room-badge">

                            <?php echo $room['room_type']; ?>

                        </div>

                        <p class="room-location">

                            Block
                            <?php echo $room['block']; ?>

                            -
                            Level
                            <?php echo $room['level']; ?>

                            -
                            Room
                            <?php echo $room['room_number']; ?>

                        </p>

                        <p class="room-capacity">

                            Capacity:
                            <?php echo $room['capacity']; ?>
                            pax

                        </p>

                        <p class="room-description">

                            <?php echo $room['description']; ?>

                        </p>

                        <!-- Buttons -->
                        <div class="room-btn-group">

                            <a href="room_details.php?room_id=<?php echo $room['room_id']; ?>" class="view-btn">

                                View Details

                            </a>

                            <a href="book_room.php?room_id=<?php echo $room['room_id']; ?>" class="book-btn">

                                Book Now

                            </a>

                        </div>

                    </div>
                </div>

                <?php
            }
        } else {

            echo '
            <div class="no-room">
                No rooms found
            </div>
            ';
        }

        ?>

    </div>
</div>
</body>
</html>