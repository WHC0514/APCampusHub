<?php

session_start();

if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin")
{
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

/* Search */
$search = "";

if(isset($_GET['search']))
{
    $search = trim($_GET['search']);
}

/* SQL Base */
$sqlVenue = "SELECT * FROM event_venue WHERE 1=1";

$params = [];
$types = "";

/* Search filter */
if(!empty($search))
{
    $sqlVenue .= " AND (venue_name LIKE ? OR description LIKE ?)";

    $searchLike = "%" . $search . "%";

    for($i = 0; $i < 2; $i++)
    {
        $params[] = $searchLike;
        $types .= "s";
    }
}

$stmtVenue = $conn->prepare($sqlVenue);

/* Bind param dynamically */
if(!empty($params))
{
    $stmtVenue->bind_param($types, ...$params);
}

$stmtVenue->execute();
$venueResult = $stmtVenue->get_result();

/* Search suggestion */
$suggestionVenues = [];

$suggestSql = "SELECT venue_id, venue_name, description FROM event_venue LIMIT 20";

$suggestResult = $conn->query($suggestSql);

while($row = $suggestResult->fetch_assoc())
{
    $suggestionVenues[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APCampusHub</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/admin/event_management.css">
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

            <!-- User Management Button -->
            <a href="user_management.php" class="topbar-link">
                <div class="topbar-item">

                    <img src="../../assets/icons/user-management.png" alt="User Management" class="topbar-icon">
                    <span>User Management</span>
            
                </div>
            </a>

            <!-- Event Management Button -->
            <a href="event_management.php" class="topbar-link active">
                <div class="topbar-item">

                    <img src="../../assets/icons/events.png" alt="Event Management" class="topbar-icon">
                    <span>Event Management</span>
            
                </div>
            </a>
        </div>
    </div>

    <!-- Filter/Search -->
    <div class="event-filter-section">

        <form method="GET" class="filter-form">

            <div class="venue-search-wrapper">

                <input type="text" name="search" id="venueSearchInput" class="venue-search" placeholder="Search venue..." value="<?php echo htmlspecialchars($search); ?>" autocomplete="off">

                <div id="venueSearchResult" class="venue-search-result"></div>

            </div>

            <button type="submit" class="search-btn">
                Search
            </button>

            <a href="../event_management/manage_request.php" class="manage-requests-btn">
            Manage Requests
            </a>

        </form>

    </div>

    <!-- Content -->
    <div class="event-content">

        <div class="event-grid">

            <?php

            if($venueResult->num_rows > 0)
            {
                while($venue = $venueResult->fetch_assoc())
                {
                    $venueImage = "../../uploads/event/default-venue.jpg";

                    if(!empty($venue['cover_image']))
                    {
                        $venueImage = "../../uploads/event/" . $venue['cover_image'];
                    }

                    ?>

                    <div class="event-card">

                        <img src="<?php echo $venueImage; ?>" class="event-image">

                        <div class="venue-info">

                            <h2>
                                <?php echo $venue['venue_name']; ?>
                            </h2>

                            <div class="venue-badge">
                                Event Venue
                            </div>

                            <p class="venue-description">
                                <?php echo $venue['description']; ?>
                            </p>

                            <div class="venue-btn-group">

                                <a href="../event_management/venue_detail.php?venue_id=<?php echo $venue['venue_id']; ?>" class="view-btn">
                                    View Details
                                </a>

                            </div>

                        </div>

                    </div>

                    <?php
                }
            }
            else
            {
                echo '
                <div class="no-venue">
                    No venues found
                </div>
                ';
            }

            ?>

        </div>

    </div>

</body>

<!-- Topbar Search -->
<script>

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

searchInput.addEventListener("keyup", function(){

    let input = searchInput.value.toLowerCase();
    searchResult.innerHTML = "";

    if(input === "")
    {
        searchResult.style.display = "none";
        return;
    }

    let filtered = pages.filter(page =>
        page.name.toLowerCase().includes(input)
    );

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

document.addEventListener("click", function(e){

    if(!document.querySelector(".search-container").contains(e.target))
    {
        searchResult.style.display = "none";
    }

});

<!-- Venue Search -->

const venues = <?php echo json_encode($suggestionVenues); ?>;

const input = document.getElementById("venueSearchInput");
const box = document.getElementById("venueSearchResult");

input.addEventListener("input", function(){

    let value = this.value.toLowerCase().trim();

    box.innerHTML = "";

    if(value === "")
    {
        box.style.display = "none";
        return;
    }

    let filtered = venues.filter(v =>
        v.venue_name.toLowerCase().includes(value) ||
        v.description.toLowerCase().includes(value)
    ).slice(0,4);

    if(filtered.length === 0)
    {
        box.innerHTML = `
            <div class="venue-search-item">
                No venue found
            </div>
        `;

        box.style.display = "block";
        return;
    }

    filtered.forEach(v => {

        let label = `${v.venue_name}`;

        box.innerHTML +=
        `
            <div class="venue-search-item"
                onclick="selectVenueSearch('${v.venue_name}')">

                ${label}

            </div>
        `;
    });

    box.style.display = "block";
});

function selectVenueSearch(value)
{
    input.value = value;
    box.style.display = "none";
    input.form.submit();
}

document.addEventListener("click", function(e){

    if(!document.querySelector(".venue-search-wrapper").contains(e.target))
    {
        box.style.display = "none";
    }

});

</script>

</html>