<?php

session_start();

if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "staff")
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
$sql = "SELECT profile_photo FROM staff WHERE user_id = ?";

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

            <!-- Manage Rooms Button -->
            <a href="manage_rooms.php" class="topbar-link">
                <div class="topbar-item">

                    <img src="../../assets/icons/room-booking.png" alt="Manage Rooms" class="topbar-icon">
                    <span>Manage Rooms</span>
            
                </div>
            </a>

            <!-- Manage Resource Button -->
            <a href="#" class="topbar-link">
                <div class="topbar-item">

                    <img src="../../assets/icons/manage-resource.png" alt="Manage Resource" class="topbar-icon">
                    <span>Manage Resource</span>

                </div>
            </a>

            <!-- View Activity Button -->
            <a href="#" class="topbar-link">
                <div class="topbar-item">

                    <img src="../../assets/icons/view-report.png" alt="View Activity" class="topbar-icon">
                    <span>View Activity</span>
            
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

            <!-- Manage Rooms -->
            <div class="dashboard-card">

                <img src="../../assets/images/room-manage.jpg" alt="Manage Rooms" class="card-image">

                <div class="card-body">

                    <h3>Manage Rooms</h3>

                    <p>
                        Manage rooms for maintenance,
                        upgrade and cleaning more easily.
                    </p>

                    <button class="primary-btn" onclick="location.href='#'">

                        Open

                    </button>

                </div>
            </div>

            <!-- Manage Resource -->
            <div class="dashboard-card">

                <img src="../../assets/images/manage-resource.jpg" alt="Manage Resource" class="card-image">

                <div class="card-body">

                    <h3>Manage Resource</h3>

                    <p>
                        A better way to manage a 
                        variety of resource in university.
                    </p>

                    <button class="primary-btn" onclick="location.href='#'">

                        Explore

                    </button>
                </div>
            </div>

            <!-- Resource Usage Report -->
            <div class="dashboard-card">

                <img src="../../assets/images/resource-usage.jpg" alt="Resource Usage Report" class="card-image">

                <div class="card-body">

                    <h3>Resource Usage Report</h3>

                    <p>
                        Check the resource usage report
                        at anytime anywhere.
                    </p>

                    <button class="primary-btn" onclick="location.href='#'">

                        View

                    </button>
                </div>
            </div>

            <!-- View Activity -->
            <div class="dashboard-card">

                <img src="../../assets/images/user-activity.jpg" alt="View Activity" class="card-image">

                <div class="card-body">

                    <h3>View Activity</h3>

                    <p>
                        A faster and convenient way to
                        receive request and provide help to user.
                    </p>

                    <button class="primary-btn" onclick="location.href='#'">

                        Explore

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
    name: "Manage Rooms",
    link: "#"
    },
    {
    name: "Manage Resource",
    link: "#"
    },
    {
    name: "View Activity",
    link: "#"
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