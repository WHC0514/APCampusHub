<?php

session_start();

if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin")
{
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

$search = trim($_GET['search'] ?? '');

$users = [];

if($search !== '')
{
    $sql = "

    SELECT 
        u.user_id,
        u.username,
        u.role,

        s.name,
        s.identical_number,
        s.email,
        s.profile_photo

    FROM user u
    LEFT JOIN student s ON u.user_id = s.user_id

    WHERE
        u.user_id LIKE ?
        OR s.name LIKE ?
        OR s.identical_number LIKE ?

    UNION

    SELECT 
        u.user_id,
        u.username,
        u.role,

        l.name,
        l.identical_number,
        l.email,
        l.profile_photo

    FROM user u
    LEFT JOIN lecturer l ON u.user_id = l.user_id

    WHERE
        u.user_id LIKE ?
        OR l.name LIKE ?
        OR l.identical_number LIKE ?

    UNION

    SELECT 
        u.user_id,
        u.username,
        u.role,

        a.name,
        a.identical_number,
        a.email,
        a.profile_photo

    FROM user u
    LEFT JOIN admin a ON u.user_id = a.user_id

    WHERE
        u.user_id LIKE ?
        OR a.name LIKE ?
        OR a.identical_number LIKE ?

    UNION

    SELECT 
        u.user_id,
        u.username,
        u.role,

        st.name,
        st.identical_number,
        st.email,
        st.profile_photo

    FROM user u
    LEFT JOIN staff st ON u.user_id = st.user_id

    WHERE
        u.user_id LIKE ?
        OR st.name LIKE ?
        OR st.identical_number LIKE ?

    ";

    $stmt = $conn->prepare($sql);

    $like = "%$search%";

    $stmt->bind_param("ssssssssssss", $like,$like,$like, $like,$like,$like, $like,$like,$like, $like,$like,$like);

    $stmt->execute();

    $result = $stmt->get_result();

    while($row = $result->fetch_assoc())
    {
        $users[] = $row;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/admin/user_management.css">
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

        <!-- Dashboard -->
        <a href="dashboard.php" class="topbar-link">

            <div class="topbar-item">

                <img src="../../assets/icons/dashboard.png"
                     class="topbar-icon">

                <span>Dashboard</span>

            </div>

        </a>

        <!-- User Management -->
        <a href="user_management.php" class="topbar-link active">

            <div class="topbar-item">

                <img src="../../assets/icons/user-management.png" class="topbar-icon">

                <span>User Management</span>

            </div>

        </a>

        <!-- Event Management -->
        <a href="event_management.php" class="topbar-link">

            <div class="topbar-item">

                <img src="../../assets/icons/events.png" class="topbar-icon">

                <span>Event Management</span>

            </div>

        </a>

    </div>

</div>

<!-- Content -->
<div class="container">

    <!-- Header -->
    <div class="top-section">

        <div class="title">
            User Management
        </div>

        <a href="add_user.php" class="add-btn">
            + Add New User
        </a>

    </div>

    <!-- Search -->
    <form class="search-box" method="GET">

        <input type="text" name="search" placeholder="Search using User ID, Name, IC Number..." value="<?php echo htmlspecialchars($search); ?>">

        <button type="submit">
            Search
        </button>

    </form>

    <!-- Empty -->
    <?php if($search === ''): ?>

        <div class="empty-result">
            Please search using User ID, Name, IC Number...
        </div>

    <?php elseif(empty($users)): ?>

        <div class="empty-result">
            No users found.
        </div>

    <?php else: ?>

        <!-- Result -->
        <div class="user-result-list">

            <?php foreach($users as $user): ?>

                <?php

                $photo = "../../uploads/profile_photo/default.png";

                if(!empty($user['profile_photo']))
                {
                    $photo =
                    "../../uploads/profile_photo/" .
                    $user['profile_photo'];
                }

                ?>

                <a href="edit_user.php?user_id=<?php echo $user['user_id']; ?>" class="user-card">

                    <div class="user-card-left">

                        <img src="<?php echo $photo; ?>" class="user-avatar">

                        <div class="user-info">

                            <div class="user-name">
                                <?php echo $user['name']; ?>
                            </div>

                            <div class="user-meta">
                                ID:
                                <?php echo $user['user_id']; ?>
                            </div>

                            <div class="user-meta">
                                IC:
                                <?php echo $user['identical_number']; ?>
                            </div>

                            <div class="user-meta">
                                <?php echo $user['email']; ?>
                            </div>

                        </div>

                    </div>

                    <div class="role-badge role-<?php echo strtolower($user['role']); ?>">

                        <?php echo ucfirst($user['role']); ?>

                    </div>

                </a>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

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