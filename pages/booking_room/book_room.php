<?php
session_start();
date_default_timezone_set("Asia/Kuala_Lumpur");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ["student", "lecturer"]))
{
    header("Location: ../auth/login.php");
    exit();
}

function fail($msg)
{
    $role = $_SESSION['role'] ?? 'student';

    if($role === "lecturer") {
        $redirect = "../lecturer/room_booking.php";
    } else {
        $redirect = "../student/room_booking.php";
    }

    echo "<script>
        alert('$msg');
        window.location.href = '$redirect';
    </script>";

    exit();
}

require_once("../../config/db.php");

if(!isset($_GET['room_id'])) {
    fail("Room ID missing");
}

$roomID = intval($_GET['room_id']);

if(isset($_GET['ajax'])) {
    $selectedDate = $_GET['date'] ?? date("Y-m-d");

    // Get all approved bookings for this room on the selected date
    $stmt = $conn->prepare("SELECT start_time, end_time FROM room_booking WHERE room_id = ? AND booking_date = ? AND booking_status = 'Approved' ORDER BY start_time ASC");
    $stmt->bind_param("is", $roomID, $selectedDate);
    $stmt->execute();
    $res = $stmt->get_result();

    $bookings = [];
    while($r = $res->fetch_assoc()) $bookings[] = $r;

    // Operating hours: 8am to 10pm
    $dayStart = strtotime($selectedDate . " 08:00:00");
    $dayEnd   = strtotime($selectedDate . " 22:00:00");

    // For today, start from current time. For future dates, start from 8am.
    if($selectedDate == date("Y-m-d")) {
        $current = max(time(), $dayStart);
        if($current > $dayEnd) $current = $dayEnd;
    } else {
        $current = $dayStart;
    }

    usort($bookings, function($a, $b) use ($selectedDate) {
        return strtotime($selectedDate." ".$a['start_time'])
            <=> strtotime($selectedDate." ".$b['start_time']);
    });

    // Walk through bookings and collect the free gaps between them
    $availableBlocks = [];
    $pointer = $current;

    foreach($bookings as $b) {
        $start = strtotime($selectedDate . " " . $b['start_time']);
        $end   = strtotime($selectedDate . " " . $b['end_time']);

        if($end <= $pointer) continue;

        if($pointer < $start) {
            // Free gap found before this booking
            $availableBlocks[] = [$pointer, $start];
            $pointer = $end;
        } elseif($pointer >= $start && $pointer < $end) {
            // Current pointer is inside a booking, skip past it
            $pointer = $end;
        }
    }

    // Add remaining time after the last booking until end of day
    if($pointer < $dayEnd) $availableBlocks[] = [$pointer, $dayEnd];

    // Fallback: if no blocks were built but day isn't over, add full remaining day
    if(empty($availableBlocks) && $current < $dayEnd) $availableBlocks[] = [$current, $dayEnd];

    $blocksForJS = [];
    foreach($availableBlocks as $b) {
        $blocksForJS[] = [
            (int)date('H', $b[0]) * 3600 + (int)date('i', $b[0]) * 60,
            (int)date('H', $b[1]) * 3600 + (int)date('i', $b[1]) * 60
        ];
    }

    $nextAvailable = !empty($blocksForJS) ? $blocksForJS[0] : null;

    header("Content-Type: application/json");
    echo json_encode([
        "blocks"        => $blocksForJS,
        "nextAvailable" => $nextAvailable
    ]);
    exit();
}

$selectedDate = $_GET['date'] ?? date("Y-m-d");

/* Room */
$stmt = $conn->prepare("SELECT * FROM room WHERE room_id = ?");
$stmt->bind_param("i", $roomID);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();

if(!$room) fail("Room not found");

/* Cover Image */
$coverImage = "../../uploads/room/default-room.jpg";
if(!empty($room['cover_image'])) {
    $coverImage = "../../uploads/room/" . $room['cover_image'];
}

/* Bookings */
$stmt = $conn->prepare("SELECT start_time, end_time FROM room_booking WHERE room_id = ? AND booking_date = ? AND booking_status = 'Approved' ORDER BY start_time ASC");
$stmt->bind_param("is", $roomID, $selectedDate);
$stmt->execute();
$res = $stmt->get_result();

$bookings = [];
while($r = $res->fetch_assoc()) $bookings[] = $r;

// Operating hours: 8am to 10pm
$dayStart = strtotime($selectedDate . " 08:00:00");
$dayEnd   = strtotime($selectedDate . " 22:00:00");

// For today, pointer starts at current time. For future dates, pointer starts at 8am.
if($selectedDate == date("Y-m-d")) {
    $current = max(time(), $dayStart);
    if($current > $dayEnd) $current = $dayEnd;
} else {
    $current = $dayStart;
}

usort($bookings, function($a, $b) use ($selectedDate) {
    return strtotime($selectedDate." ".$a['start_time'])
        <=> strtotime($selectedDate." ".$b['start_time']);
});

// Walk through bookings and collect the free gaps between them
$availableBlocks = [];
$pointer = $current;

foreach($bookings as $b) {
    $start = strtotime($selectedDate . " " . $b['start_time']);
    $end   = strtotime($selectedDate . " " . $b['end_time']);

    if($end <= $pointer) continue;

    if($pointer < $start) {
        // Free gap found before this booking
        $availableBlocks[] = [$pointer, $start];
        $pointer = $end;
    } elseif($pointer >= $start && $pointer < $end) {
        // Current pointer is inside a booking, skip past it
        $pointer = $end;
    }
}

// Add remaining time after the last booking until end of day
if($pointer < $dayEnd) $availableBlocks[] = [$pointer, $dayEnd];

// Fallback: if no blocks were built but day isn't over, add full remaining day
if(empty($availableBlocks) && $current < $dayEnd) $availableBlocks[] = [$current, $dayEnd];

// First available block is shown as the default suggestion
$nextAvailable = !empty($availableBlocks) ? $availableBlocks[0] : null;

?>

<!DOCTYPE html>
<html>
<head>
    <title>APCampusHub</title>
    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../../assets/css/booking_room/book_room.css">
</head>
<body>

<!-- TOPBAR -->
<div class="topbar profile-topbar">
    <div class="profile-topbar-left">
        <a href="room_detail.php?room_id=<?php echo $roomID; ?>" class="back-btn">
            <img src="../../assets/icons/back.png" class="back-icon">
        </a>
        <h2>Book Room</h2>
    </div>
</div>

<div class="container">

    <!-- Room Info -->
    <div class="room-card">
        <img src="<?php echo $coverImage; ?>" alt="room">
        <h2><?php echo $room['room_name']; ?></h2>
        <p><?php echo $room['room_type']; ?></p>
        <p>Capacity: <?php echo $room['capacity']; ?></p>
    </div>

    <!-- Date -->
    <div class="section">
        <h3>Select Date</h3>
        <input type="date" id="dateInput" value="<?php echo $selectedDate; ?>" min="<?php echo date('Y-m-d'); ?>">
    </div>

    <!-- Start Time -->
    <div class="section">
        <h3>Select Start Time</h3>
        <input type="time" id="startTime" value="<?php echo $nextAvailable ? date('H:i', $nextAvailable[0]) : ''; ?>">
        <div id="validationMsg"></div>
    </div>

    <!-- End Time -->
    <div class="section">
        <h3>Select End Time</h3>
        <input type="time" id="endTime" value="<?php echo $nextAvailable ? date('H:i', $nextAvailable[1]) : ''; ?>">
        <div id="endValidationMsg"></div>
    </div>

    <!-- Available Slot -->
    <div class="section">
        <h3>Available Slot</h3>
        <div id="slotPreview"></div>
    </div>

    <!-- FORM — hidden inputs carry the final validated values to process_booking.php -->
    <form method="POST" action="process_booking.php">
        <input type="hidden" name="room_id" value="<?php echo $roomID; ?>">
        <input type="hidden" name="date" id="finalDate" value="<?php echo $selectedDate; ?>">
        <input type="hidden" name="start_time" id="finalStart">
        <input type="hidden" name="end_time" id="finalEnd">
        <button type="submit" class="btn">Book Now</button>
    </form>

</div>

<script>

let blocks = <?php
    $blocksForJS = [];
    foreach ($availableBlocks as $b) {
        $blocksForJS[] = [
            (int)date('H', $b[0]) * 3600 + (int)date('i', $b[0]) * 60,
            (int)date('H', $b[1]) * 3600 + (int)date('i', $b[1]) * 60
        ];
    }
    echo json_encode($blocksForJS);
?>;

const todayStr  = "<?php echo date('Y-m-d'); ?>";
const roomID    = <?php echo $roomID; ?>;
const input     = document.getElementById("startTime");
const endInput  = document.getElementById("endTime");
const preview   = document.getElementById("slotPreview");
const msg       = document.getElementById("validationMsg");
const endMsg    = document.getElementById("endValidationMsg");
const dateInput = document.getElementById("dateInput");
const finalDate = document.getElementById("finalDate");

let dateEnterPressed = false;

async function goToDate() {
    // Clamp to today if user somehow enters a past date
    if(dateInput.value < todayStr) {
        dateInput.value = todayStr;
    }
    if(!dateInput.value) return;

    // Update the hidden date field so the form submits the correct date
    finalDate.value = dateInput.value;

    // Fetch new available blocks for the selected date (same file, ajax=1 flag)
    const res  = await fetch(`book_room.php?room_id=${roomID}&date=${dateInput.value}&ajax=1`);
    const data = await res.json();

    // Replace the blocks array with fresh data for the new date
    blocks = data.blocks;

    // Clear all previous messages and values
    msg.innerHTML     = "";
    endMsg.innerHTML  = "";
    preview.innerHTML = "";
    document.getElementById("finalStart").value = "";
    document.getElementById("finalEnd").value   = "";

    // Auto-fill start and end with the first available slot of the new date
    if(data.nextAvailable) {
        input.value    = toTime(data.nextAvailable[0]);
        endInput.value = toTime(data.nextAvailable[1]);
        document.getElementById("finalStart").value = input.value;
        document.getElementById("finalEnd").value   = endInput.value;
        updatePreview();
    } else {
        input.value    = "";
        endInput.value = "";
        preview.innerHTML = "<div class='error'>No available slots for this day.</div>";
    }
}

// Use a flag to prevent blur firing after Enter (both would call goToDate)
dateInput.addEventListener("keydown", function(e) {
    if(e.key === "Enter") {
        e.preventDefault();
        dateEnterPressed = true;
        goToDate();
    }
});

dateInput.addEventListener("blur", function() {
    if(dateEnterPressed) {
        dateEnterPressed = false;
        return;
    }
    goToDate();
});

// Returns the block that contains the given second, or null if it's a booked/invalid time
function findBlock(sec) {
    for(let b of blocks) {
        if(sec >= b[0] && sec < b[1]) return b;
    }
    return null;
}

// Returns the next block that starts after the given second (for snapping forward)
function findNextBlock(sec) {
    for(let b of blocks) {
        if(sec < b[0]) return b;
    }
    return null;
}

// Validates on blur (when user leaves the field).
// If the time is inside a valid block, accept it.
// If not, snap forward to the next available block.
// Also resets end time if it no longer falls in the same block.

input.addEventListener("blur", function() {
    let val = this.value;
    msg.innerHTML = "";
    preview.innerHTML = "";

    if(!val) return;

    let sec   = toSec(val);
    let match = findBlock(sec);

    if(!match) {
        // Time is booked or passed — snap to the start of the next free block
        let next = findNextBlock(sec);
        if(next) {
            match = next;
            sec   = next[0];
            val   = toTime(next[0]);
            input.value = val;
            msg.innerHTML = "<div class='info'>Time adjusted to next available slot: " + val + "</div>";
        }
    }

    if(!match) {
        msg.innerHTML = "<div class='error'>No available slots remaining for this day.</div>";
        document.getElementById("finalStart").value = "";
        document.getElementById("finalEnd").value   = "";
        preview.innerHTML = "";
        return;
    }

    document.getElementById("finalStart").value = val;

    // If end time is empty, before start, or in a different block — reset it to block end
    let endSec = toSec(endInput.value || "00:00");
    if(!endInput.value || endSec <= sec || findBlock(endSec) !== match) {
        endInput.value = toTime(match[1]);
        document.getElementById("finalEnd").value = toTime(match[1]);
        endMsg.innerHTML = "";
    }

    updatePreview();
});

// Validates on blur (when user leaves the field).
// End time must be after start time and within the same available block.
// If not, it snaps to the block's end time.

endInput.addEventListener("blur", function() {
    let val = this.value;
    endMsg.innerHTML = "";

    if(!val) return;

    let sec        = toSec(val);
    let startSec   = toSec(input.value || "00:00");
    let startBlock = findBlock(startSec);

    if(!startBlock) {
        endMsg.innerHTML = "<div class='error'>Please set a valid start time first.</div>";
        return;
    }

    if(sec <= startSec) {
        // End is before or equal to start — snap to block end
        val = toTime(startBlock[1]);
        endInput.value = val;
        endMsg.innerHTML = "<div class='info'>End time must be after start time. Adjusted to: " + val + "</div>";
    } else if(sec > startBlock[1]) {
        // End goes past the block boundary (into a booked slot) — snap back to block end
        val = toTime(startBlock[1]);
        endInput.value = val;
        endMsg.innerHTML = "<div class='info'>End time adjusted to slot boundary: " + val + "</div>";
    }

    document.getElementById("finalEnd").value = endInput.value;
    updatePreview();
});

// Shows a summary of the selected start and end time.

function updatePreview() {
    const startVal = input.value;
    const endVal   = endInput.value;

    if(!startVal || !endVal) {
        preview.innerHTML = "";
        return;
    }

    preview.innerHTML =
        `<div class="slot-box">
            Start: ${startVal}<br>
            End: ${endVal}
        </div>`;
}

// Convert "HH:MM" string to total seconds since midnight
function toSec(t) {
    let [h, m] = t.split(":");
    return (+h) * 3600 + (+m) * 60;
}

// Convert total seconds since midnight to "HH:MM" string
function toTime(s) {
    let h = Math.floor(s / 3600).toString().padStart(2, '0');
    let m = Math.floor((s % 3600) / 60).toString().padStart(2, '0');
    return `${h}:${m}`;
}

// Simulate blur on start time so the slot preview shows immediately on page load
window.addEventListener("load", function() {
    if(input.value) {
        input.dispatchEvent(new Event("blur"));
    }
});

</script>
</body>
</html>