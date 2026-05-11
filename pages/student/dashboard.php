<?php

session_start();

if(!isset($_SESSION['user_id']))
{
    header("Location: ../auth/login.php");
    exit();
}

$username = $_SESSION['username'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APCampusHub</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
</head>
<body>
    
    <!-- Topbar -->
    <div class="topbar">

        <div class="topbar-left">

            <img src="../../assets/images/logo.png" class="topbar-logo">

            <input type="text" class="search-bar" placeholder="Search APCampusHub">

        </div>

        <div class="topbar-right">

            <!-- Room Booking Button -->
            <div class="topbar-item">

                <img src="../../assets/icons/room-booking.png" alt="Room Booking" class="topbar-icon">
                <span>Room Booking</span>
            
            </div>

            <!-- Check In / Out Button -->
            <div class="topbar-item">

                <img src="../../assets/icons/checin.png" alt="Check In" class="topbar-icon">
                <span>Check In/Out</span>

            </div>

            <!-- Events Button -->
            <div class="topbar-item">

                <img src="../../assets/icons/events.png" alt="Events" class="topbar-icon">
                <span>Events</span>
            
            </div>
        </div>
    </div>

    <!-- Profile -->
    <div class="profile-section">

        <div class="profile-left">

            <div class="profile-pic"></div>

            <!-- User Info -->
            <div class="profile-info">

                <h2><?php echo $username; ?></h2>

                <p>Welcome back to APCampusHub</p>

            </div>
        </div>

        <!-- Action Icons -->
        <div class="profile-right">

            <!-- Settings -->
            <img src="../../assets/icons/settigns.png" alt="Settings" class="profile-action">

            <!-- Notification -->
            <img src="../../assets/icons/notifications.png" alt="Notification" class="profile-action">

            <!-- Logout -->
            <a href="#">

                <img src="../../assets/icons/logout.png" alt="Logout" class="profile-action">

            </a>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="dashboard-content">

        <div class="card-grid">

            <!-- Room Booking -->
            <div class="dashboard-card">

                <img src="../../assets/images/room-booking.jpg" alt="Room Booking" class="card-image">

                <div class="card-body">

                    <h3>Room Booking</h3>

                    <p>
                        Reserve classrooms, discussion rooms,
                        and presentation rooms anytime.
                    </p>

                    <button class="primary-btn">

                        Open

                    </button>

                </div>
            </div>

            <!-- Check In -->
            <div class="dashboard-card">

                <img src="../../assets/images/checkin.jpg" alt="Check In" class="card-image">

                <div class="card-body">

                    <h3>Check In / Out</h3>

                    <p>
                        Manage attendance, entry records,
                        and activity tracking.
                    </p>

                    <button class="primary-btn">

                        Open

                    </button>
                </div>
            </div>

            <!-- Events -->
            <div class="dashboard-card">

                <img src="../../assets/images/events.jpg" alt="Events" class="card-image">

                <div class="card-body">

                    <h3>Campus Events</h3>

                    <p>
                        Stay updated with university events,
                        club activities, and announcements.
                    </p>

                    <button class="primary-btn">

                        Explore

                    </button>
                </div>
            </div>

            <!-- Facilities -->
            <div class="dashboard-card">

                <img src="../../assets/images/facilities.jpg" alt="Facilities" class="card-image">

                <div class="card-body">

                    <h3>Campus Facilities</h3>

                    <p>
                        Browse libraries, labs, sports facilities,
                        and available resources.
                    </p>

                    <button class="primary-btn">

                        View More

                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>