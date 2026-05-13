<?php

session_start();

if(!isset($_SESSION['user_id']))
{
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

/* Room filter */
$filterType = "";
$search = "";

if(isset($_GET['room_type']))
{
    $filterType = trim($_GET['room_type']);
}

if(isset($_GET['search']))
{
    $search = trim($_GET['search']);
}

/* SQL Base */
$sqlRoom = "SELECT * FROM room WHERE status = 'Active'";

$params = [];
$types = "";

/* Room type filter */
if(!empty($filterType))
{
    $sqlRoom .= " AND room_type = ?";
    $params[] = $filterType;
    $types .= "s";
}

/* Search filter */
if(!empty($search))
{
    $sqlRoom .= " AND (room_name LIKE ? OR block LIKE ? OR room_number LIKE ? OR room_type LIKE ?)";

    $searchLike = "%" . $search . "%";

    for($i = 0; $i < 4; $i++)
    {
        $params[] = $searchLike;
        $types .= "s";
    }
}

$stmtRoom = $conn->prepare($sqlRoom);

/* Bind param dynamically */
if(!empty($params))
{
    $stmtRoom->bind_param($types, ...$params);
}

$stmtRoom->execute();
$roomResult = $stmtRoom->get_result();

$suggestionRooms = [];

$suggestSql = "SELECT room_id, room_name, room_type, block, room_number 
               FROM room 
               WHERE status = 'Active'
               LIMIT 20";

$suggestResult = $conn->query($suggestSql);

while($row = $suggestResult->fetch_assoc())
{
    $suggestionRooms[] = $row;
}
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

        <form method="GET" class="filter-form">

            <!-- Room Type -->
            <select name="room_type" class="filter-select" onchange="this.form.submit()">

                <option value="">All Rooms</option>

                <option value="Discussion Room"
                    <?php if($filterType == "Discussion Room"){ echo "selected"; } ?>>
                    Discussion Room
                </option>

                <option value="Presentation Room"
                    <?php if($filterType == "Presentation Room"){ echo "selected"; } ?>>
                    Presentation Room
                </option>

                <option value="Auditorium"
                    <?php if($filterType == "Auditorium"){ echo "selected"; } ?>>
                    Auditorium
                </option>

                <option value="Classroom"
                    <?php if($filterType == "Classroom"){ echo "selected"; } ?>>
                    Classroom
                </option>

                <option value="Lab"
                    <?php if($filterType == "Lab"){ echo "selected"; } ?>>
                    Lab
                </option>

            </select>

            <!-- Search -->
            <div class="room-search-wrapper" style="position:relative;">

                <input type="text" name="search" id="roomSearchInput" class="room-search" placeholder="Search room..." value="<?php echo htmlspecialchars($search); ?>" autocomplete="off">

                <div id="roomSearchResult" class="room-search-result"></div>

            </div>
            <button type="submit" class="search-btn">
                Search
            </button>

            <!-- My Bookings Button -->
            <a href="my_bookings.php" class="my-bookings-btn">
                My Bookings
            </a>

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

                            <a href="room_detail.php?room_id=<?php echo $room['room_id']; ?>" class="view-btn">

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

<!-- Search Bar for Topbar -->
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
    link: "#"
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

<!-- Search bar for room -->
<script>

const rooms = <?php echo json_encode($suggestionRooms); ?>;

const input = document.getElementById("roomSearchInput");
const box = document.getElementById("roomSearchResult");

input.addEventListener("input", function(){

    let value = this.value.toLowerCase().trim();
    box.innerHTML = "";

    if(value === "")
    {
        box.style.display = "none";
        return;
    }

    let filtered = rooms.filter(r =>
        r.room_name.toLowerCase().includes(value) ||
        r.room_type.toLowerCase().includes(value) ||
        r.block.toLowerCase().includes(value) ||
        r.room_number.toString().includes(value)
    ).slice(0,4); // ONLY 4 suggestions

    if(filtered.length === 0)
    {
        box.innerHTML = `<div class="room-search-item">No room found</div>`;
        box.style.display = "block";
        return;
    }

    filtered.forEach(r => {

        let label = `${r.room_name} (Block ${r.block} - Room ${r.room_number})`;

        box.innerHTML += `
            <div class="room-search-item"
                 onclick="selectRoomSearch('${r.room_name}')">
                ${label}
            </div>
        `;
    });

    box.style.display = "block";
});

/* Click suggestion */
function selectRoomSearch(value)
{
    input.value = value;
    box.style.display = "none";
    input.form.submit();
}

/* Hide on outside click */
document.addEventListener("click", function(e){
    if(!document.querySelector(".room-search-wrapper").contains(e.target))
    {
        box.style.display = "none";
    }
});

</script>

</html>