<?php

session_start();

require_once("../../config/db.php");

/* Staff only */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== "staff")
{
    header("Location: ../auth/login.php");
    exit();
}

/* Get requests data (Exclude Done) */
$sqlRequest = "SELECT 
    rsr.*,
    r.room_name
FROM room_service_request rsr
LEFT JOIN room r ON rsr.room_id = r.room_id
WHERE rsr.status != 'Done'
ORDER BY rsr.created_at ASC
";

$requestResult = $conn->query($sqlRequest);

/* Get reports data (Exclude Resolved) */
$sqlReport = "SELECT 
    rir.*,
    r.room_name
FROM room_issue_report rir
LEFT JOIN room r ON rir.room_id = r.room_id
WHERE rir.status != 'Resolved'
ORDER BY rir.created_at ASC
";

$reportResult = $conn->query($sqlReport);

/* Dashboard count */
$requestCount = $requestResult->num_rows;
$reportCount = $reportResult->num_rows;

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APCampusHub</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/staff/view_activity.css">
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

            <a href="manage_rooms.php" class="topbar-link">

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

            <a href="view_activity.php" class="topbar-link active">

                <div class="topbar-item">

                    <img src="../../assets/icons/view-report.png" class="topbar-icon">
                    <span>View Activity</span>

                </div>

            </a>

        </div>

    </div>

    <div class="container">

        <!-- Header -->
        <div class="page-header">

            <h1>View Activity</h1>

            <p>Requests and Reports overview</p>

        </div>

        <!-- Dashboard Cards -->
        <div class="activity-cards">

            <div class="activity-card request-card">

                <h3>Pending Requests</h3>

                <div class="dashboard-number">
                    <?php echo $requestCount; ?>
                </div>

            </div>

            <div class="activity-card report-card">

                <h3>Pending Reports</h3>

                <div class="dashboard-number">
                    <?php echo $reportCount; ?>
                </div>

            </div>

        </div>

        <!-- Tabs -->
        <div class="tab-buttons">

            <button class="tab-btn active" onclick="openTab(event, 'requests')">
                Requests
            </button>

            <button class="tab-btn" onclick="openTab(event, 'reports')">
                Reports
            </button>

        </div>

        <!-- Request Tab -->
        <div id="requests" class="tab-content active">

            <table>

                <thead>

                    <tr>

                        <th>Room</th>
                        <th>Booking ID</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($r = $requestResult->fetch_assoc()) { ?>

                    <?php
                        $statusClass = "";

                        if($r['status'] == "Pending")
                        {
                            $statusClass = "status-pending";
                        }
                        elseif($r['status'] == "In Progress")
                        {
                            $statusClass = "status-in-progress";
                        } else
                        {
                            $statusClass = "status-default";
                        }
                    ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($r['room_name']); ?>
                        </td>

                        <td>
                            <?php echo $r['booking_id']; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($r['request_type']); ?>
                        </td>

                        <td>

                            <span class="status-badge <?php echo $statusClass; ?>">

                                <?php echo $r['status']; ?>

                            </span>

                        </td>

                        <td>
                            <?php echo $r['created_at']; ?>
                        </td>

                        <td>

                            <a href="../view_activity/view_request_detail.php?id=<?php echo $r['request_id']; ?>" class="view-btn">

                                View

                            </a>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

        <!-- Report Tab -->
        <div id="reports" class="tab-content">

            <table>

                <thead>

                    <tr>

                        <th>Room</th>
                        <th>Booking ID</th>
                        <th>Issue Type</th>
                        <th>Severity</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($r = $reportResult->fetch_assoc()) { ?>

                    <?php

                        /* Severity Badge */
                        $severityClass = "";

                        if($r['severity'] == "High")
                        {
                            $severityClass = "severity-high";
                        }
                        elseif($r['severity'] == "Medium")
                        {
                            $severityClass = "severity-medium";
                        }
                        else
                        {
                            $severityClass = "severity-low";
                        }

                        /* Status Badge */
                        $statusClass = "";

                        if($r['status'] == "Pending")
                        {
                            $statusClass = "status-pending";
                        }
                        elseif($r['status'] == "In Progress")
                        {
                            $statusClass = "status-in-progress";
                        }
                        else
                        {
                            $statusClass = "status-default";
                        }

                    ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($r['room_name']); ?>
                        </td>

                        <td>
                            <?php echo $r['booking_id']; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($r['issue_type']); ?>
                        </td>

                        <td>

                            <span class="severity-badge <?php echo $severityClass; ?>">

                                <?php echo $r['severity']; ?>

                            </span>

                        </td>

                        <td>

                            <span class="status-badge <?php echo $statusClass; ?>">

                                <?php echo $r['status']; ?>

                            </span>

                        </td>

                        <td>
                            <?php echo $r['created_at']; ?>
                        </td>

                        <td>

                            <a href="../view_activity/view_report_detail.php?id=<?php echo $r['report_id']; ?>" class="view-btn">

                                View

                            </a>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</body>

<script>

function openTab(event, tab)
{
    document.querySelectorAll('.tab-content').forEach(t => {
        t.classList.remove('active');
    });

    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('active');
    });

    document.getElementById(tab).classList.add('active');

    event.currentTarget.classList.add('active');
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

/* Hide search */
document.addEventListener("click", function(e){

    if(!document.querySelector(".search-container").contains(e.target))
    {
        searchResult.style.display = "none";
    }

});

</script>

</html>