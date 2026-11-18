<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$hostelId       = trim($_POST['hostelId'] ?? '');
$hostelName     = trim($_POST['hostelName'] ?? '');
$gender         = trim($_POST['gender'] ?? '');
$hostelCapacity = trim($_POST['hostelCapacity'] ?? $_POST['capacity'] ?? '');
$statusId       = trim($_POST['statusId'] ?? '1');

if ($hostelId == '') {
    $response = [
        'success' => false,
        'message' => "HOSTEL ID IS REQUIRED"
    ];
    goto end;
}

$checkHostelQuery = mysqli_query($conn, "SELECT * FROM hostels_tab WHERE hostel_id = '$hostelId'") or die(mysqli_error($conn));
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
        'message' => "HOSTEL NAME IS REQUIRED"
    ];
    goto end;
}

if ($gender == '') {
    $response = [
        'success' => false,
        'message' => "GENDER IS REQUIRED"
    ];
    goto end;
}

if ($hostelCapacity == '' || !is_numeric($hostelCapacity) || $hostelCapacity <= 0) {
    $response = [
        'success' => false,
        'message' => "VALID HOSTEL CAPACITY IS REQUIRED"
    ];
    goto end;
}

// Normalize status
if ($statusId == '' || strtoupper($statusId) == 'ACTIVE') {
    $statusId = '1';
} elseif (strtoupper($statusId) == 'INACTIVE' || strtoupper($statusId) == 'CLOSED') {
    $statusId = '2';
}

$nameCheck = mysqli_query($conn, "SELECT sn FROM hostels_tab WHERE hostel_name = '$hostelName' AND hostel_id != '$hostelId'") or die(mysqli_error($conn));
if (mysqli_num_rows($nameCheck) > 0) {
    $response = [
        'success' => false,
        'message' => "HOSTEL NAME '$hostelName' IS ALREADY IN USE BY ANOTHER HOSTEL"
    ];
    goto end;
}

mysqli_query($conn, "UPDATE `hostels_tab` SET 
    `hostel_name`     = '$hostelName',
    `gender`          = '$gender',
    `hostel_capacity` = '$hostelCapacity',
    `status_id`       = '$statusId',
    `updated_at`      = NOW()
    WHERE `hostel_id` = '$hostelId'") or die(mysqli_error($conn));

$fetchUpdated = mysqli_query($conn, "SELECT hostels_tab.*, status_tab.status_name FROM hostels_tab LEFT JOIN status_tab ON hostels_tab.status_id = status_tab.status_id WHERE hostels_tab.hostel_id = '$hostelId'") or die(mysqli_error($conn));

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
