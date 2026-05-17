<?php

session_start();

date_default_timezone_set("Asia/Kuala_Lumpur");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "student")
{
    header("Location: ../auth/login.php");
    exit();
}

require_once("../../config/db.php");

function fail($msg)
{
    echo "<script>
        alert('$msg');
        window.location.href = '../student/events.php';
    </script>";
    exit();
}

if(!isset($_GET['venue_id']))
{
    fail("Venue ID missing");
}

$venueID = intval($_GET['venue_id']);

if(isset($_GET['ajax']))
{
    $now = time();

    // Get all approved bookings that haven't ended yet
    $stmt = $conn->prepare("SELECT start_time, end_time FROM venue_booking WHERE venue_id = ? AND booking_status = 'Approved' AND end_time > NOW() ORDER BY start_time ASC");
    $stmt->bind_param("i", $venueID);
    $stmt->execute();
    $res = $stmt->get_result();

    $bookings = [];
    while($r = $res->fetch_assoc()) $bookings[] = $r;

    // Walk through bookings from now, collect free gaps between them
    $pointer   = $now;
    $available = [];

    foreach($bookings as $b)
    {
        $bStart = strtotime($b['start_time']);
        $bEnd   = strtotime($b['end_time']);

        if($bEnd <= $pointer) continue;

        if($pointer < $bStart)
        {
            // Free gap found before this booking
            $available[] = [
                date("Y-m-d H:i", $pointer),
                date("Y-m-d H:i", $bStart)
            ];
            $pointer = $bEnd;
        }
        elseif($pointer >= $bStart && $pointer < $bEnd)
        {
            // Inside a booking — skip past it
            $pointer = $bEnd;
        }
    }

    // After last booking, everything from pointer onwards is free
    $available[] = [
        date("Y-m-d H:i", $pointer),
        "onwards"
    ];

    header("Content-Type: application/json");
    echo json_encode([
        "blocks"  => $available,
        "pointer" => date("Y-m-d H:i", $pointer)
    ]);
    exit();
}

$stmt = $conn->prepare("SELECT * FROM event_venue WHERE venue_id = ?");
$stmt->bind_param("i", $venueID);
$stmt->execute();
$venue = $stmt->get_result()->fetch_assoc();

if(!$venue) fail("Venue not found");

$coverImage = "../../uploads/event/default-venue.jpg";
if(!empty($venue['cover_image']))
{
    $coverImage = "../../uploads/event/" . $venue['cover_image'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APCampusHub</title>

    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../../assets/css/events/book_venue.css">
</head>
<body>

    <!-- Topbar -->
    <div class="topbar profile-topbar">
        <div class="profile-topbar-left">
            <a href="venue_detail.php?venue_id=<?php echo $venueID; ?>" class="back-btn">
                <img src="../../assets/icons/back.png" class="back-icon">
            </a>
            <h2>Book Venue</h2>
        </div>
    </div>

    <div class="container">

        <!-- Venue Info -->
        <div class="venue-card">
            <img src="<?php echo $coverImage; ?>">
            <h2><?php echo $venue['venue_name']; ?></h2>
            <p><?php echo $venue['description']; ?></p>
        </div>

        <!-- Start Time -->
        <div class="section">
            <h3>Start Date & Time</h3>
            <input type="datetime-local" id="startInput">
            <div id="startMsg"></div>
        </div>

        <!-- End Time -->
        <div class="section">
            <h3>End Date & Time</h3>
            <input type="datetime-local" id="endInput">
            <div id="endMsg"></div>
        </div>

        <!-- Description -->
        <div class="section">
            <h3>Event Description</h3>
            <textarea id="descInput" placeholder="Describe your event / purpose..." style="width:100%;padding:12px;border-radius:10px;background:#121212;color:white;border:1px solid #333;min-height:100px;box-sizing:border-box;"></textarea>
        </div>

        <!-- Resource Request -->
        <div class="section">
            <h3>Resource Request <span style="font-weight:400;font-size:0.85rem;color:#888;">(Optional)</span></h3>

            <div id="resourceList"></div>

            <button type="button" class="btn-add-resource" onclick="addResource()">
                + Add Resource
            </button>
        </div>

        <!-- Available Slots -->
        <div class="section">
            <h3>Available Time Slots</h3>
            <div id="slotBox"></div>
        </div>

        <!-- Form -->
        <form method="POST" action="process_venue_booking.php">
            <input type="hidden" name="venue_id"    value="<?php echo $venueID; ?>">
            <input type="hidden" name="start_time"  id="finalStart">
            <input type="hidden" name="end_time"    id="finalEnd">
            <input type="hidden" name="description" id="finalDesc">
            <input type="hidden" name="resources"   id="finalResources" value="[]">
            <button type="submit" class="btn">Submit Proposal</button>
        </form>

    </div>

<script>

const start    = document.getElementById("startInput");
const end      = document.getElementById("endInput");
const box      = document.getElementById("slotBox");
const desc     = document.getElementById("descInput");
const startMsg = document.getElementById("startMsg");
const endMsg   = document.getElementById("endMsg");

// Get local time
function getLocalNow() {
    const now   = new Date();
    const year  = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day   = String(now.getDate()).padStart(2, '0');
    const hour  = String(now.getHours()).padStart(2, '0');
    const min   = String(now.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hour}:${min}`;
}

let nowStr = getLocalNow();

// Refresh nowStr every minute so it stays accurate
setInterval(() => { nowStr = getLocalNow(); }, 60000);

// Set initial mins
start.min = nowStr;
end.min   = nowStr;

// Fetches approved bookings from server and shows free gaps
async function loadSlots()
{
    box.innerHTML = "<div class='slot-box'>Loading...</div>";

    const res  = await fetch(`book_venue.php?venue_id=<?php echo $venueID; ?>&ajax=1`);
    const data = await res.json();

    box.innerHTML = "";

    if(!data.blocks || data.blocks.length === 0) {
        box.innerHTML = "<div class='slot-box'>Any time is available for booking</div>";
        return;
    }

    data.blocks.forEach(b => {
        if(b[1] === "onwards") {
            box.innerHTML += `<div class="slot-box">Available from ${b[0]} onwards</div>`;
        } else {
            box.innerHTML += `<div class="slot-box">${b[0]} → ${b[1]}</div>`;
        }
    });

    // Update start min to next free pointer so user can't pick a booked time
    if(data.pointer) {
        const pointerFormatted = data.pointer.replace(" ", "T");
        start.min = pointerFormatted;

        // If current start value is before pointer, clear it
        if(start.value && start.value < pointerFormatted) {
            start.value = "";
            document.getElementById("finalStart").value = "";
        }
    }
}

window.addEventListener("load", loadSlots);

start.addEventListener("blur", function() {
    startMsg.innerHTML = "";

    if(!this.value) return;

    // Reject past times
    if(this.value < nowStr) {
        this.value = nowStr;
        startMsg.innerHTML = "<div class='info'>Start time adjusted to now.</div>";
    }

    // Update end minimum to be after start
    end.min = this.value;

    // Clear end if no longer valid
    if(end.value && end.value <= this.value) {
        end.value = "";
        document.getElementById("finalEnd").value = "";
        endMsg.innerHTML = "<div class='info'>End time cleared — please select a new end time.</div>";
    }

    updateHidden();
});

end.addEventListener("blur", function() {
    endMsg.innerHTML = "";

    if(!this.value) return;

    // Must have a start first
    if(!start.value) {
        this.value = "";
        endMsg.innerHTML = "<div class='error'>Please select a start date and time first.</div>";
        return;
    }

    // End must be strictly after start
    if(this.value <= start.value) {
        this.value = "";
        document.getElementById("finalEnd").value = "";
        endMsg.innerHTML = "<div class='error'>End time must be after start time.</div>";
        return;
    }

    updateHidden();
});

desc.addEventListener("input", updateHidden);

function updateHidden() {
    document.getElementById("finalStart").value = start.value;
    document.getElementById("finalEnd").value   = end.value;
    document.getElementById("finalDesc").value  = desc.value;
}

// Allows user to optionally add multiple resource requests
let resourceCount = 0;

function addResource() {
    resourceCount++;
    const id = resourceCount;

    const div = document.createElement("div");
    div.className = "resource-row";
    div.id = `resource-${id}`;

    div.innerHTML = `
        <select class="resource-type-select">
            <option value="">-- Select Resource --</option>
            <option value="Cables">Cables</option>
            <option value="Extension Plug">Extension Plug</option>
            <option value="Stationary">Stationary</option>
            <option value="Projector">Projector</option>
            <option value="Microphone">Microphone</option>
            <option value="Laptop">Laptop</option>
            <option value="Tables Chairs">Tables Chairs</option>
        </select>
        <input type="text"
               class="resource-desc-input"
               placeholder="Additional notes (optional)">
        <button type="button"
                class="btn-remove-resource"
                onclick="removeResource(${id})">
            ✕
        </button>
    `;

    document.getElementById("resourceList").appendChild(div);
    updateResources();

    // Update hidden input whenever select or description changes
    div.querySelectorAll("select, input").forEach(el => {
        el.addEventListener("change", updateResources);
        el.addEventListener("input",  updateResources);
    });
}

function removeResource(id) {
    const el = document.getElementById(`resource-${id}`);
    if(el) el.remove();
    updateResources();
}

// Collect all resource rows into JSON and store in hidden input
function updateResources() {
    const rows      = document.querySelectorAll(".resource-row");
    const resources = [];

    rows.forEach(row => {
        const type = row.querySelector(".resource-type-select").value;
        const desc = row.querySelector(".resource-desc-input").value.trim();
        if(type) {
            resources.push({ type, desc });
        }
    });

    document.getElementById("finalResources").value = JSON.stringify(resources);
}

</script>

</body>
</html>