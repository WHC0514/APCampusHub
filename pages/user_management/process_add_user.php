<?php

session_start();
require_once("../../config/db.php");

date_default_timezone_set("Asia/Kuala_Lumpur");

/* Only admin allowed */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== "admin") {
    echo "<script>
        alert('Access denied. Admin only.');
        window.location.href = '../auth/login.php';
    </script>";
    exit();
}

function errorBack($msg) {
    echo "<script>
        alert('$msg');
        window.history.back();
    </script>";
    exit();
}

/* Get POST data */
$name = trim($_POST['name'] ?? '');
$ic = trim($_POST['identical_number'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone_number'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$userRole = trim($_POST['role'] ?? '');

/* Validate required fields */
if(
    empty($name) || empty($ic) || empty($email) ||
    empty($phone) || empty($gender) ||
    empty($username) || empty($password) ||
    empty($userRole)
){
    errorBack("Please fill in all required fields.");
}

/* Validate email (APU format only) */
$pattern = "/^[a-zA-Z0-9._%+-]+@mail\.apu\.edu\.my$/";

if(!preg_match($pattern, $email)) {
    errorBack("Invalid email format. Must be @mail.apu.edu.my");
}

/* Check duplicate username */
$stmt = $conn->prepare("SELECT user_id FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();

if($stmt->get_result()->num_rows > 0){
    errorBack("Username already exists.");
}

/* Hash password */
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$profilePhoto = "default.png";

if(isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0){

    $allowedTypes = ['image/jpeg','image/png','image/jpg','image/webp'];

    $tmp = $_FILES['profile_photo']['tmp_name'];
    $type = mime_content_type($tmp);

    if(!in_array($type, $allowedTypes)){
        errorBack("Only image files are allowed.");
    }

    $ext = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
    $newName = uniqid("user_", true) . "." . $ext;

    $uploadPath = "../../uploads/profile_photo/" . $newName;

    if(!move_uploaded_file($tmp, $uploadPath)){
        errorBack("Failed to upload profile photo.");
    }

    $profilePhoto = $newName;
}

$stmt = $conn->prepare("
    INSERT INTO user (username, password, role)
    VALUES (?, ?, ?)
");

$stmt->bind_param("sss", $username, $hashedPassword, $userRole);

if(!$stmt->execute()){
    errorBack("Failed to create user.");
}

$userID = $stmt->insert_id;

switch($userRole){

    case "student":
        $stmt = $conn->prepare("INSERT INTO student (name, identical_number, email, phone_number, gender, profile_photo, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        break;

    case "lecturer":
        $stmt = $conn->prepare("INSERT INTO lecturer (name, identical_number, email, phone_number, gender, profile_photo, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        break;

    case "admin":
        $stmt = $conn->prepare("INSERT INTO admin (name, identical_number, email, phone_number, gender, profile_photo, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        break;

    case "staff":
        $stmt = $conn->prepare("INSERT INTO staff (name, identical_number, email, phone_number, gender, profile_photo, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        break;

    default:
        errorBack("Invalid role selected.");
}

/* FIX: FORCE STRING SAFE VALUE */
$profilePhoto = (string)$profilePhoto;

/* Bind values */
$stmt->bind_param("ssssssi", $name, $ic, $email, $phone, $gender, $profilePhoto, $userID);

/* Execute role insert */
if(!$stmt->execute()){
    errorBack("Failed to save user profile.");
}

/* Success */
echo "<script>
    alert('User created successfully!');
    window.location.href = '../admin/user_management.php';
</script>";

exit();

?>