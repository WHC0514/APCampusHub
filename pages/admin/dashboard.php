<?php

session_start();

if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin")
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
$sql = "SELECT profile_photo FROM admin WHERE user_id = ?";

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

            <!-- User Management Button -->
            <a href="user_management.php" class="topbar-link">
                <div class="topbar-item">

                    <img src="../../assets/icons/user-management.png" alt="User Management" class="topbar-icon">
                    <span>User Management</span>
            
                </div>
            </a>

            <!-- Event Management Button -->
            <a href="event_management.php" class="topbar-link">
                <div class="topbar-item">

                    <img src="../../assets/icons/events.png" alt="Event Management" class="topbar-icon">
                    <span>Event Management</span>
            
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

    <!-- Dashboard Content -->
    <div class="dashboard-content">

        <div class="card-grid">

            <!-- User Management -->
            <div class="dashboard-card">

                <img src="../../assets/images/manage-user.jpg" alt="Manage User" class="card-image">

                <div class="card-body">

                    <h3>Manage User</h3>

                    <p>
                        Manage users in APCampusHub easily,
                        at anytime anywhere.
                    </p>

                    <button class="primary-btn" onclick="location.href='user_management.php'">

                        Manage

                    </button>

                </div>
            </div>

            <!-- View Event Venue -->
            <div class="dashboard-card">

                <img src="../../assets/images/event-venue.jpg" alt="Event Venue" class="card-image">

                <div class="card-body">

                    <h3>View Venue Availability</h3>

                    <p>
                        Check the availability of venue
                        in our university campus more easily.
                    </p>

                    <button class="primary-btn" onclick="location.href='event_management.php'">

                        View More

                    </button>
                </div>
            </div>

            <!-- Event Approval -->
            <div class="dashboard-card">

                <img src="../../assets/images/event-approval.jpg" alt="Event Approval" class="card-image">

                <div class="card-body">

                    <h3>Manage Event Approval</h3>

                    <p>
                        Manage all events approval in a single appliction
                        with a more easy way.
                    </p>

                    <button class="primary-btn" onclick="location.href='../event_management/manage_request.php'">

                        Explore

                    </button>
                </div>
            </div>

            <!-- Event Schedule -->
            <div class="dashboard-card">

                <img src="../../assets/images/event-schedule.png" alt="Event Schedule" class="card-image">

                <div class="card-body">

                    <h3>View Event Schedule</h3>

                    <p>
                        Browse the latest event schedule
                        and get the information you need more easily.
                    </p>

                    <button class="primary-btn" onclick="location.href='../event_management/manage_request.php'">

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
    name: "User Management",
    link: "user_management.php"
    },
    {
    name: "Event Management",
    link: "event_management.php"
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

</html>