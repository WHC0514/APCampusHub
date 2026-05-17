<?php

session_start();
require_once("../../config/db.php");

/* Staff only */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== "staff")
{
    header("Location: ../auth/login.php");
    exit();
}

/* Search bar for resource */
$search = $_GET['search'] ?? "";

/* Get searched resource data */
$sqlResource = "SELECT * FROM resource WHERE 1=1";

if(!empty($search))
{
    $sqlResource .= " AND (resource_name LIKE ? OR resource_type LIKE ? OR description LIKE ?)";
}

$stmtRes = $conn->prepare($sqlResource);

if(!empty($search))
{
    $like = "%".$search."%";
    $stmtRes->bind_param("sss", $like, $like, $like);
}

$stmtRes->execute();
$resourceResult = $stmtRes->get_result();


/* Get borrowed resource data */
$sqlBorrow = "SELECT rb.*, r.resource_name, r.resource_type FROM resource_borrow rb
LEFT JOIN resource r ON rb.resource_id = r.resource_id
WHERE rb.status = 'Borrowed' ORDER BY rb.borrow_time DESC";

$borrowResult = $conn->query($sqlBorrow);


/* Get usage log data */
$month = $_GET['month'] ?? date("Y-m");
$activeTab = $_GET['tab'] ?? ($_GET['month'] ?? null ? "log" : "resources");

$sqlLog = "SELECT log.*, r.resource_name, rb.user_id FROM resource_usage_log log
LEFT JOIN resource_borrow rb ON log.borrow_id = rb.borrow_id
LEFT JOIN resource r ON rb.resource_id = r.resource_id
WHERE DATE_FORMAT(log.action_time, '%Y-%m') = ? ORDER BY log.action_time DESC";

$stmtLog = $conn->prepare($sqlLog);
$stmtLog->bind_param("s", $month);
$stmtLog->execute();
$logResult = $stmtLog->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APCampusHub</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/staff/manage_resource.css">
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

            <!-- Manage Rooms Button -->
            <a href="manage_rooms.php" class="topbar-link">
                <div class="topbar-item">

                    <img src="../../assets/icons/room-booking.png" alt="Manage Rooms" class="topbar-icon">
                    <span>Manage Rooms</span>
            
                </div>
            </a>

            <!-- Manage Resource Button -->
            <a href="manage_resource.php" class="topbar-link active">
                <div class="topbar-item">

                    <img src="../../assets/icons/manage-resource.png" alt="Manage Resource" class="topbar-icon">
                    <span>Manage Resource</span>

                </div>
            </a>

            <!-- View Activity Button -->
            <a href="view_activity.php" class="topbar-link">
                <div class="topbar-item">

                    <img src="../../assets/icons/view-report.png" alt="View Activity" class="topbar-icon">
                    <span>View Activity</span>
            
                </div>
            </a>
        </div>
    </div>


    <div class="container">

        <h1>Manage Resource</h1>

        <!-- Tab Button -->
    <div class="tab-buttons">

        <button class="tab-btn <?= $activeTab == 'resources' ? 'active' : '' ?>" onclick="openTab(event, 'resources')">
            Resources
        </button>

        <button class="tab-btn <?= $activeTab == 'borrowed' ? 'active' : '' ?>" onclick="openTab(event, 'borrowed')">
            Borrowed
        </button>

        <button class="tab-btn <?= $activeTab == 'log' ? 'active' : '' ?>" onclick="openTab(event, 'log')">
            Usage Log
        </button>

    </div>

    <!-- Resource Tab -->
    <div id="resources" class="tab-content <?= $activeTab == 'resources' ? 'active' : '' ?>">
        <div class="section-header">
            <h2>Resources</h2>
            <a href="../manage_resource/add_resource.php" class="btn-add">+ Add Resource</a>
        </div>

        <form method="GET">
            <input type="text" name="search" placeholder="Search resource..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php if($resourceResult->num_rows > 0) { ?>

                    <?php while($r = $resourceResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $r['resource_name']; ?></td>
                        <td><?php echo $r['resource_type']; ?></td>
                        <td><?php echo $r['quantity']; ?></td>
                        <td><?php echo $r['status']; ?></td>
                        <td>
                            <button class="edit-btn" onclick="window.location.href='../manage_resource/edit_resource.php?id=<?= $r['resource_id'] ?>'">
                                Edit
                            </button>
                        </td>
                    </tr>
                    <?php } ?>

                <?php } else { ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:20px; color:#9ca3af;">
                            No data available
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>


    <!-- Borrow Tab -->
    <div id="borrowed" class="tab-content <?= $activeTab == 'borrowed' ? 'active' : '' ?>">
        <div class="section-header">
            <h2>Borrowed Resources</h2>
            <a href="../manage_resource/borrow_resource.php" class="btn-add">+ Borrow Resource</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Resource</th>
                    <th>User ID</th>
                    <th>Qty</th>
                    <th>Borrow Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php if($borrowResult->num_rows > 0) { ?>
                    <?php while($b = $borrowResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $b['resource_name']; ?></td>
                        <td><?php echo $b['user_id']; ?></td>
                        <td><?php echo $b['quantity']; ?></td>
                        <td><?php echo $b['borrow_time']; ?></td>
                        <td><?php echo $b['status']; ?></td>
                        <td>
                            <form method="POST" action="../manage_resource/return_resource.php">
                                <input type="hidden" name="borrow_id" value="<?php echo $b['borrow_id']; ?>">
                                <button type="submit" class="return-btn">Return</button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>

                <?php } else { ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:20px; color:#9ca3af;">
                            No data available
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>


    <!-- Log Tab -->
    <div id="log" class="tab-content <?= $activeTab == 'log' ? 'active' : '' ?>">
        <div class="section-header">
            <h2>Usage Log</h2>
        </div>

        <form method="GET" data-month="<?php echo $month; ?>">
            <input type="hidden" name="tab" value="log">
            <label>Month:</label>
            <input type="month" name="month" value="<?php echo $month; ?>">
            <button type="submit">Filter</button>
            <button type="button" onclick="window.print()">Print</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Resource</th>
                    <th>User ID</th>
                    <th>Action</th>
                    <th>Time</th>
                </tr>
            </thead>

            <tbody>
                <?php if($logResult->num_rows > 0) { ?>
                    <?php while($l = $logResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $l['resource_name']; ?></td>
                        <td><?php echo $l['user_id']; ?></td>
                        <td><?php echo $l['action']; ?></td>
                        <td><?php echo $l['action_time']; ?></td>
                    </tr>
                    <?php } ?>
                 <?php } else { ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding:20px; color:#9ca3af;">
                            No data available
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

</div>

</body>

<script>

function openTab(event, tabId)
{
    document.querySelectorAll(".tab-content").forEach(tab => {
        tab.classList.remove("active");
    });

    document.querySelectorAll(".tab-btn").forEach(btn => {
        btn.classList.remove("active");
    });

    document.getElementById(tabId).classList.add("active");
    event.currentTarget.classList.add("active");
}

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