<?php

session_start();

if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "student")
{
    header("Location: ../auth/login.php");
    exit();
}

$username = $_SESSION['username'];

require_once("../../config/db.php");

/* Default profile image */
$profilePhoto = "../../uploads/profile_photo/default.png";

$userID = $_SESSION['user_id'];

/* Get student profile photo */
$sql = "SELECT profile_photo FROM student WHERE user_id = ?";

$stmt = $conn->prepare($sql);

if($stmt)
{
    $stmt->bind_param("i", $userID);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows == 1)
    {
        $row = $result->fetch_assoc();

        if(!empty($row['profile_photo']))
        {
            $profilePhoto = $row['profile_photo'];
        }
    }

    $stmt->close();
}

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

            <img src="../../assets/images/app-logo.png" class="topbar-logo">

            <div class="search-container">

                <input type="text" class="search-bar" id="searchInput" placeholder="Search APCampusHub">

                <div class="search-result" id="searchResult"></div>

            </div>

        </div>

        <div class="topbar-right">

            <!-- Dashboard Button -->
            <a href="dashboard.php" class="topbar-link active">
                <div class="topbar-item">

                    <img src="../../assets/icons/dashboard.png" alt="Dashboard" class="topbar-icon">
                    <span>Dashboard</span>
            
                </div>
            </a>

            <!-- Room Booking Button -->
            <a href="room_booking.php" class="topbar-link">
                <div class="topbar-item">

                    <img src="../../assets/icons/room-booking.png" alt="Room Booking" class="topbar-icon">
                    <span>Room Booking</span>
            
                </div>
            </a>

            <!-- Check In / Out Button -->
            <a href="../check_inout/room_check_redirect.php" class="topbar-link">
                <div class="topbar-item">

                    <img src="../../assets/icons/check-in.png" alt="Check In" class="topbar-icon">
                    <span>Check In/Out</span>

                </div>
            </a>

            <!-- Events Button -->
            <a href="events.php" class="topbar-link">
                <div class="topbar-item">

                    <img src="../../assets/icons/events.png" alt="Events" class="topbar-icon">
                    <span>Events</span>
            
                </div>
            </a>
        </div>
    </div>

    <!-- Profile -->
    <div class="profile-section">

        <div class="profile-left">

            <div class="profile-pic">

                <a href="../profile/profile.php" class="profile-link">

                    <img src="../../uploads/profile_photo/<?php echo $profilePhoto; ?>" alt="Profile Picture" class="profile-pic">

                </a>
                
            </div>

            <!-- User Info -->
            <div class="profile-info">

                <h2><?php echo $username; ?></h2>

                <p>Welcome back to APCampusHub</p>

            </div>
        </div>

        <!-- Action Icons -->
        <div class="profile-right">

            <!-- Settings -->
            <a href="#">

                <img src="../../assets/icons/settings.png" alt="Settings" class="profile-action">

            </a>

            <!-- Notification -->
            <a href="#">

                <img src="../../assets/icons/notifications.png" alt="Notification" class="profile-action">

            </a>

            <!-- Logout -->
            <a href="../auth/logout.php">

                <img src="../../assets/icons/logout.png" alt="Logout" class="profile-action">

            </a>
        </div>
    </div>

    <!-- Checkout Success -->
    <?php if(isset($_GET['checkout_success'])): ?>

        <div class="session-ended-overlay">

            <div class="session-ended-card">

                <button class="close-ended-btn" onclick="closeEndedCard()">

                    <img src="../../assets/icons/close.png" class="close-ended-icon">

                </button>

                <div class="ended-icon">
                    <img src="../../assets/icons/check.png" class="ended-icon-img">
                </div>

                <h2>
                    Checkout Successful
                </h2>

                <p>

                    You have successfully
                    checked out from the room.

                    <br><br>

                    Thank you for using
                    APCampusHub.

                </p>

            </div>

        </div>

    <?php endif; ?>


    <!-- Session Ended -->
    <?php if(isset($_GET['session_ended'])): ?>

        <div class="session-ended-overlay">

            <div class="session-ended-card">

                <button class="close-ended-btn" onclick="closeEndedCard()">

                    <img src="../../assets/icons/close.png" class="close-ended-icon">

                </button>

                <div class="ended-icon">

                    <img src="../../assets/icons/door.png" class="ended-icon-img">

                </div>

                <h2>
                    Booking Session Ended
                </h2>

                <p>

                    Your booking session has ended.

                    <br><br>

                    Please leave the classroom
                    as soon as possible.

                </p>

            </div>

        </div>

    <?php endif; ?>

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

                    <button class="primary-btn" onclick="location.href='room_booking.php'">

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
                        Smart Check-In and Out,
                        and request for assist easily.
                    </p>

                    <button class="primary-btn" onclick="location.href='../check_inout/room_check_redirect.php'">

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
                        Stay updated with events schedule,
                        venue availability, and announcements.
                    </p>

                    <button class="primary-btn" onclick="location.href='events.php'">

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

                    <button class="primary-btn" onclick="location.href='room_booking.php'">

                        View More

                    </button>
                </div>
            </div>
        </div>
    </div>
</body>

<script>

/* Searchable pages */
const pages = [
    {
    name: "Dashboard",
    link: "dashboard.php"
    },
    {
    name: "My Account",
    link: "../profile/profile.php"
    },
    {
    name: "Room Booking",
    link: "room_booking.php"
    },
    {
    name: "Check In/Out",
    link: "../check_inout/room_check_redirect.php"
    },
    {
    name: "Events",
    link: "events.php"
    }
];

const searchInput = document.getElementById("searchInput");
const searchResult = document.getElementById("searchResult");

/* Live search */
searchInput.addEventListener("keyup", function(){
    let input = searchInput.value.toLowerCase();
    searchResult.innerHTML = "";

    /* Empty input */
    if(input === "")
    {
        searchResult.style.display = "none";
        return;
    }

    /* Filter results */
    let filtered = pages.filter(page => page.name.toLowerCase().includes(input));

    /* No result */
    if(filtered.length === 0)
    {
        searchResult.innerHTML = 
        `
            <div class="search-item">
                No result found
            </div>
        `;

        searchResult.style.display = "block";
        return;
    }

    /* Show results */
    filtered.forEach(page => {
        searchResult.innerHTML +=
        `
            <div class="search-item clickable"
                onclick="window.location.href='${page.link}'">

                ${page.name}

            </div>
        `;
    });

    searchResult.style.display = "block";
});

/* Hide when click outside */
document.addEventListener("click", function(e){
    if(!document.querySelector(".search-container").contains(e.target))
    {
        searchResult.style.display = "none";
    }
});

</script>

<script>

function closeEndedCard()
{
    document.querySelector(
        ".session-ended-overlay"
    ).style.display = "none";

    window.history.replaceState(
        {},
        document.title,
        window.location.pathname
    );
}

</script>

</html>