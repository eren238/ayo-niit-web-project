<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE

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

// Normalize gender to match enum('Male','Female','Mixed')
if (strcasecmp($gender, 'Co-ed') === 0 || strcasecmp($gender, 'Mixed') === 0) {
    $gender = 'Mixed';
} elseif (strcasecmp($gender, 'Male') === 0) {
    $gender = 'Male';
} elseif (strcasecmp($gender, 'Female') === 0) {
    $gender = 'Female';
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
        'message' => "VALID HOSTEL CAPACITY IS REQUIRED, Kindly fill in a positive number"
    ];
    goto end;
}

$safeHostelName = mysqli_real_escape_string($conn, $hostelName);
$safeGender     = mysqli_real_escape_string($conn, $gender);
$safeCapacity   = (int)$hostelCapacity;
$safeStatusId   = mysqli_real_escape_string($conn, $statusId);

$hostelcheck = mysqli_query($conn, "SELECT sn FROM hostels_tab WHERE hostel_name = '$safeHostelName'") or die(mysqli_error($conn));
if (mysqli_num_rows($hostelcheck) > 0) {
    $response = [
        "success" => false,
        "message" => "HOSTEL ALREADY EXIST kindly choose another hostel name"
    ];
    goto end;
}

$hostelId = 'HOSTEL' . date("YmdHis") . rand(10, 99);

mysqli_query($conn, "INSERT INTO `hostels_tab`
    (`hostel_id`, `hostel_name`, `gender`, `hostel_capacity`, `status_id`, `created_at`, `updated_at`) VALUES
    ('$hostelId', '$safeHostelName', '$safeGender', '$safeCapacity', '$safeStatusId', NOW(), NOW())") or die(mysqli_error($conn));

$createHostelQuery = mysqli_query($conn, "SELECT hostels_tab.*, COALESCE(status_tab.status_name, 'Active') AS status_name FROM hostels_tab LEFT JOIN status_tab ON hostels_tab.status_id = status_tab.status_id WHERE hostels_tab.hostel_id = '$hostelId'") or die(mysqli_error($conn));
$hostelData = mysqli_fetch_assoc($createHostelQuery);

$response = [
    'success' => true,
    'message' => "HOSTEL CREATED SUCCESSFUL",
    'data' => [
        'hostelId'       => $hostelData['hostel_id'],
        'hostelName'     => $hostelData['hostel_name'],
        'gender'         => $hostelData['gender'],
        'hostelCapacity' => $hostelData['hostel_capacity'],
        'statusName'     => $hostelData['status_name'],
    ]
];

end:
echo json_encode($response);
?>