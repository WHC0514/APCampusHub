<?php

session_start();

if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "staff")
{
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

date_default_timezone_set("Asia/Kuala_Lumpur");

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

/* SQL */
$sqlRoom = "SELECT r.*, rs.status AS room_live_status FROM room r
LEFT JOIN room_status rs ON r.room_id = rs.room_id WHERE 1=1";

$params = [];
$types = "";

/* Filter room type */
if(!empty($filterType))
{
    $sqlRoom .= " AND r.room_type = ?";
    $params[] = $filterType;
    $types .= "s";
}

/* Search */
if(!empty($search))
{
    $sqlRoom .= " AND (
        r.room_name LIKE ? OR 
        r.block LIKE ? OR 
        r.room_number LIKE ? OR 
        r.room_type LIKE ?
    )";

    $searchLike = "%" . $search . "%";

    for($i = 0; $i < 4; $i++)
    {
        $params[] = $searchLike;
        $types .= "s";
    }
}

$stmtRoom = $conn->prepare($sqlRoom);

if(!empty($params))
{
    $stmtRoom->bind_param($types, ...$params);
}

$stmtRoom->execute();
$roomResult = $stmtRoom->get_result();

/* Suggestions */
$suggestSql = "SELECT room_id, room_name, room_type, block, room_number FROM room LIMIT 20";
$suggestResult = $conn->query($suggestSql);

$suggestionRooms = [];

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
    <link rel="stylesheet" href="../../assets/css/staff/manage_rooms.css">
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

            <a href="dashboard.php" class="topbar-link">
                <div class="topbar-item">
                    <img src="../../assets/icons/dashboard.png" class="topbar-icon">
                    <span>Dashboard</span>
                </div>
            </a>

            <a href="manage_rooms.php" class="topbar-link active">
                <div class="topbar-item">
                    <img src="../../assets/icons/room-booking.png" class="topbar-icon">
                    <span>Manage Rooms</span>
                </div>
            </a>

            <a href="manage_resource.php" class="topbar-link">
                <div class="topbar-item">
                    <img src="../../assets/icons/manage-resource.png" class="topbar-icon">
                    <span>Manage Resource</span>
                </div>
            </a>

            <a href="view_activity.php" class="topbar-link">
                <div class="topbar-item">
                    <img src="../../assets/icons/view-report.png" class="topbar-icon">
                    <span>View Activity</span>
                </div>
            </a>

        </div>
    </div>

    <!-- Filter -->
    <div class="room-filter-section">

    <form method="GET" class="filter-form">

        <select name="room_type" class="filter-select" onchange="this.form.submit()">

            <option value="">All Rooms</option>
            <option value="Discussion Room" <?php if($filterType=="Discussion Room") echo "selected"; ?>>Discussion Room</option>
            <option value="Presentation Room" <?php if($filterType=="Presentation Room") echo "selected"; ?>>Presentation Room</option>
            <option value="Auditorium" <?php if($filterType=="Auditorium") echo "selected"; ?>>Auditorium</option>
            <option value="Classroom" <?php if($filterType=="Classroom") echo "selected"; ?>>Classroom</option>
            <option value="Lab" <?php if($filterType=="Lab") echo "selected"; ?>>Lab</option>

        </select>

        <div class="room-search-wrapper">
            <input type="text" name="search" id="roomSearchInput" class="room-search" placeholder="Search room..." value="<?php echo htmlspecialchars($search); ?>">

            <div id="roomSearchResult" class="room-search-result"></div>
        </div>

        <button type="submit" class="search-btn">Search</button>

    </form>

    </div>

    <!-- Content -->
    <div class="room-content">

    <div class="room-grid">

    <?php if($roomResult->num_rows > 0): ?>

    <?php while($room = $roomResult->fetch_assoc()): ?>

    <?php
    $roomImage = "../../uploads/room/default-room.jpg";

    if(!empty($room['cover_image']))
    {
        $roomImage = "../../uploads/room/" . $room['cover_image'];
    }

    /* Status Logic */
    $roomStatus = "Available";

    if(strtolower($room['status']) === "active") {

        if($room['room_live_status'] === "Available") {
            $roomStatus = "Available";
        }
        elseif($room['room_live_status'] === "Occupied") {
            $roomStatus = "Occupied";
        }
    }
    else {
        $roomStatus = ucfirst($room['status']);
    }
    ?>

    <div class="room-card">

        <img src="<?php echo $roomImage; ?>" class="room-image">

        <div class="room-info">

            <h2><?php echo $room['room_name']; ?></h2>

            <div class="room-badge">
                <?php echo $room['room_type']; ?>
            </div>

            <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $roomStatus)); ?>">
                <?php echo $roomStatus; ?>
            </span>

            <p class="room-location">
                Block <?php echo $room['block']; ?> -
                Level <?php echo $room['level']; ?> -
                Room <?php echo $room['room_number']; ?>
            </p>

            <p class="room-capacity">
                Capacity: <?php echo $room['capacity']; ?> pax
            </p>

            <p class="room-description">
                <?php echo $room['description']; ?>
            </p>

            <div class="room-btn-group">
                <a href="../manage_rooms/manage_room_detail.php?room_id=<?php echo $room['room_id']; ?>" class="view-btn">
                    View Room Details
                </a>
            </div>

        </div>
    </div>

    <?php endwhile; ?>

    <?php else: ?>

        <div class="no-room">No rooms found</div>

    <?php endif; ?>

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
    link: "manage_rooms.php"
    },
    {
    name: "Manage Resource",
    link: "manage_resource.php"
    },
    {
    name: "View Activity",
    link: "view_activity.php"
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