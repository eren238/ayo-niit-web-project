<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$hostelId       = trim($_POST['hostelId'] ?? '');
$hostelName     = trim($_POST['hostelName'] ?? '');
$gender         = trim($_POST['gender'] ?? '');
$hostelCapacity = trim($_POST['hostelCapacity'] ?? $_POST['capacity'] ?? '');
$statusId       = trim($_POST['statusId'] ?? '1');

// Normalize status ID
if (strtoupper($statusId) === 'ACTIVE' || $statusId === '1' || $statusId === '') {
    $statusId = '1';
} elseif (strtoupper($statusId) === 'MAINTENANCE' || strtoupper($statusId) === 'CLOSED' || $statusId === '2') {
    $statusId = '2';
} else {
    $statusId = '1';
}

// Normalize gender
if (strcasecmp($gender, 'Co-ed') === 0 || strcasecmp($gender, 'Mixed') === 0) {
    $gender = 'Mixed';
} elseif (strcasecmp($gender, 'Male') === 0) {
    $gender = 'Male';
} elseif (strcasecmp($gender, 'Female') === 0) {
    $gender = 'Female';
}

if ($hostelId == '') {
    $response = [
        'success' => false,
        'message' => "HOSTEL ID IS REQUIRED, Kindly fill in the hostel ID to continue"
    ];
    goto end;
}

$safeHostelId = mysqli_real_escape_string($conn, $hostelId);
$checkHostelQuery = mysqli_query($conn, "SELECT sn FROM hostels_tab WHERE hostel_id = '$safeHostelId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkHostelQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "HOSTEL NOT FOUND"
    ];
    goto end;
}

if ($hostelName == '') {
    $response = [
        'success' => false,
        'message' => "HOSTEL NAME IS REQUIRED, Kindly fill in the hostel name to continue"
    ];
    goto end;
}

if ($gender == '') {
    $response = [
        'success' => false,
        'message' => "GENDER IS REQUIRED, Kindly select gender to continue"
    ];
    goto end;
}

if ($hostelCapacity == '' || !is_numeric($hostelCapacity) || (int)$hostelCapacity <= 0) {
    $response = [
        'success' => false,
        'message' => "VALID HOSTEL CAPACITY IS REQUIRED"
    ];
    goto end;
}

$safeHostelName = mysqli_real_escape_string($conn, $hostelName);
$safeGender     = mysqli_real_escape_string($conn, $gender);
$safeCapacity   = (int)$hostelCapacity;
$safeStatusId   = mysqli_real_escape_string($conn, $statusId);

$nameCheck = mysqli_query($conn, "SELECT sn FROM hostels_tab WHERE hostel_name = '$safeHostelName' AND hostel_id != '$safeHostelId'") or die(mysqli_error($conn));
if (mysqli_num_rows($nameCheck) > 0) {
    $response = [
        'success' => false,
        'message' => "HOSTEL NAME '$hostelName' IS ALREADY IN USE BY ANOTHER HOSTEL"
    ];
    goto end;
}

mysqli_query($conn, "UPDATE `hostels_tab` SET 
    `hostel_name`     = '$safeHostelName',
    `gender`          = '$safeGender',
    `hostel_capacity` = '$safeCapacity',
    `status_id`       = '$safeStatusId',
    `updated_at`      = NOW()
    WHERE `hostel_id` = '$safeHostelId'") or die(mysqli_error($conn));

$fetchUpdated = mysqli_query($conn, "SELECT hostels_tab.*, COALESCE(status_tab.status_name, 'Active') AS status_name FROM hostels_tab LEFT JOIN status_tab ON hostels_tab.status_id = status_tab.status_id WHERE hostels_tab.hostel_id = '$safeHostelId'") or die(mysqli_error($conn));

$data = mysqli_fetch_assoc($fetchUpdated);
$data['hostelId'] = $data['hostel_id'];

$response = [
    'success' => true,
    'message' => "HOSTEL UPDATED SUCCESSFULLY",
    'data'    => $data
];

end:
echo json_encode($response);
?>