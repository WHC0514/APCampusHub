<?php

session_start();

if(!isset($_SESSION['user_id']))
{
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

$userID = $_SESSION['user_id'];

date_default_timezone_set("Asia/Kuala_Lumpur");

$currentDate = date("Y-m-d");
$currentTime = time();

/* Get upcoming bookings */
$sql = "SELECT 
    rb.booking_id,
    rb.room_id,
    rb.booking_date,
    rb.start_time,
    rb.end_time,
    rb.booking_status,

    r.room_name,
    r.room_type,
    r.block,
    r.level,
    r.room_number,
    r.cover_image

FROM room_booking rb
INNER JOIN room r ON rb.room_id = r.room_id
WHERE rb.user_id = ? AND rb.booking_status = 'Approved' AND rb.booking_date >= ?
ORDER BY rb.booking_date ASC, rb.start_time ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $userID, $currentDate);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>APCampusHub</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/student/checkin.css">

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
            <a href="room_booking.php" class="topbar-link">
                <div class="topbar-item">

                    <img src="../../assets/icons/room-booking.png" alt="Room Booking" class="topbar-icon">
                    <span>Room Booking</span>
            
                </div>
            </a>

            <!-- Check In / Out Button -->
            <a href="checkin.php" class="topbar-link active">
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

</div>

<!-- Content -->
<div class="page-container">

    <h1 class="page-title">
        Check In
    </h1>

    <div class="booking-grid">

    <?php

    if($result->num_rows > 0)
    {
        while($row = $result->fetch_assoc())
        {
            $image ="../../uploads/room/default-room.jpg";

            if(!empty($row['cover_image']))
            {
                $image ="../../uploads/room/" . $row['cover_image'];
            }

            $bookingStart = strtotime(
                $row['booking_date'] .
                " " .
                $row['start_time']
            );

            $bookingEnd = strtotime(
                $row['booking_date'] .
                " " .
                $row['end_time']
            );

            /* Only allow check in before 10 min and after 30 min */
            $allowCheckin = $bookingStart - 600;

            $closeCheckin = $bookingStart + 1800;

            ?>

            <div class="booking-card">

                <img src="<?php echo $image; ?>" class="booking-image">

                <div class="booking-body">

                    <h2 class="booking-title">
                        <?php
                        echo $row['room_name'];
                        ?>
                    </h2>

                    <div class="booking-badge">
                        <?php
                        echo $row['room_type'];
                        ?>
                    </div>

                    <p class="booking-info">

                        Block
                        <?php
                        echo $row['block'];
                        ?>

                        -
                        Level
                        <?php
                        echo $row['level'];
                        ?>

                        -
                        Room
                        <?php
                        echo $row['room_number'];
                        ?>

                    </p>

                    <p class="booking-info">

                        <?php
                        echo date(
                            "d M Y",
                            strtotime(
                                $row['booking_date']
                            )
                        );
                        ?>

                    </p>

                    <p class="booking-info">

                        <?php
                        echo date(
                            "g:i A",
                            strtotime(
                                $row['start_time']
                            )
                        );

                        echo " - ";

                        echo date(
                            "g:i A",
                            strtotime(
                                $row['end_time']
                            )
                        );
                        ?>

                    </p>

                    <?php

                    if($currentTime < $allowCheckin)
                    {
                        ?>

                        <div class="status-box status-upcoming">

                            Check In Opens At

                            <?php
                            echo date(
                                "g:i A",
                                $allowCheckin
                            );
                            ?>

                        </div>

                        <button class="action-btn disabled" disabled>

                            Too Early To Check In

                        </button>

                        <?php
                    } else if($currentTime >= $allowCheckin && $currentTime <= $closeCheckin)
                    {
                        ?>

                        <div class="status-box status-active">

                            Check In Available

                        </div>

                        <a href="../check_inout/enter_otp.php?booking_id=<?php echo $row['booking_id'];?>">

                            <button class="action-btn">

                                Check In

                            </button>

                        </a>

                        <?php
                    } else {
                        ?>

                        <div class="status-box status-expired">

                            Check In Closed

                        </div>

                        <button class="action-btn disabled" disabled>

                            Unavailable

                        </button>

                        <?php
                    }

                    ?>

                </div>

            </div>

            <?php
        }
    } else {
        ?>

        <div class="empty-box">

            No upcoming bookings found.

        </div>

        <?php
    }

    ?>

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
    link: "checkin.php"
    },
    {
    name: "Events",
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

</html>