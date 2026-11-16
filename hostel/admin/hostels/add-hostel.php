<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php

$hostelName     = trim($_POST['hostelName'] ?? '');
$gender         = trim($_POST['gender'] ?? '');
$hostelCapacity = trim($_POST['hostelCapacity'] ?? $_POST['capacity'] ?? '');
$statusId       = trim($_POST['statusId'] ?? '1');

// Validate Hostel Name
if ($hostelName == '') {
    $response = [
        'success' => false,
        'message' => "HOSTEL NAME IS REQUIRED, Kindly fill in the hostel name to continue"
    ];
    goto end;
}

// Validate Gender
if ($gender == '') {
    $response = [
        'success' => false,
        'message' => "GENDER IS REQUIRED, Kindly select gender to continue"
    ];
    goto end;
}

// Validate Capacity
if ($hostelCapacity == '' || !is_numeric($hostelCapacity) || $hostelCapacity <= 0) {
    $response = [
        'success' => false,
        'message' => "VALID CAPACITY IS REQUIRED, Kindly enter a valid capacity number"
    ];
    goto end;
}

// Normalize Status ID
if ($statusId == '' || strtoupper($statusId) == 'ACTIVE') {
    $statusId = '1';
} elseif (strtoupper($statusId) == 'INACTIVE' || strtoupper($statusId) == 'CLOSED') {
    $statusId = '2';
}

// Check if Hostel Name already exists
$checkHostelName = mysqli_query($conn, "SELECT sn FROM hostels_tab WHERE hostel_name = '$hostelName'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkHostelName) > 0) {
    $response = [
        'success' => false,
        'message' => "HOSTEL ALREADY EXISTS! A hostel with name '$hostelName' already exists"
    ];
    goto end;
}

// Auto-generate unique Hostel ID
$hostelId = 'HOSTEL' . date("YmdHis");
$checkHostelId = mysqli_query($conn, "SELECT sn FROM hostels_tab WHERE hostel_id = '$hostelId'") or die(mysqli_error($conn));
$suffix = 1;
while (mysqli_num_rows($checkHostelId) > 0) {
    $hostelId = 'HOSTEL' . date("YmdHis") . $suffix;
    $checkHostelId = mysqli_query($conn, "SELECT sn FROM hostels_tab WHERE hostel_id = '$hostelId'") or die(mysqli_error($conn));
    $suffix++;
}

// Check if 'code' column exists in table schema (for backwards compatibility if column exists on server)
$checkCol = mysqli_query($conn, "SHOW COLUMNS FROM `hostels_tab` LIKE 'code'");
if ($checkCol && mysqli_num_rows($checkCol) > 0) {
    mysqli_query($conn, "INSERT INTO `hostels_tab` (`hostel_id`, `hostel_name`, `code`, `gender`, `hostel_capacity`, `status_id`, `created_at`, `updated_at`) VALUES ('$hostelId', '$hostelName', '$hostelId', '$gender', '$hostelCapacity', '$statusId', NOW(), NOW())") or die(mysqli_error($conn));
} else {
    mysqli_query($conn, "INSERT INTO `hostels_tab` (`hostel_id`, `hostel_name`, `gender`, `hostel_capacity`, `status_id`, `created_at`, `updated_at`) VALUES ('$hostelId', '$hostelName', '$gender', '$hostelCapacity', '$statusId', NOW(), NOW())") or die(mysqli_error($conn));
}

$fetchHostelQuery = mysqli_query($conn, "SELECT hostels_tab.*, status_tab.status_name FROM hostels_tab LEFT JOIN status_tab ON hostels_tab.status_id = status_tab.status_id WHERE hostels_tab.hostel_id = '$hostelId'") or die(mysqli_error($conn));
$hostelData = mysqli_fetch_assoc($fetchHostelQuery);

// Add convenience alias for any clients
$hostelData['hostelId'] = $hostelData['hostel_id'];

$response = [
    'success' => true,
    'message' => "HOSTEL CREATED SUCCESSFULLY",
    'data'    => $hostelData
];

end:
echo json_encode($response);
?>
