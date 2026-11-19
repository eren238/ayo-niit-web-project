<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLERATION OF VARIABLE

$hostelName = trim($_POST['hostelName']);
$gender = trim($_POST['gender']);
$hostelCapacity = trim($_POST['hostelCapacity']);
$statusId = trim($_POST['statusId']);

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

if ($hostelCapacity == '') {
    $response = [
        'success' => false,
        'message' => "HOSTEL CAPACITY IS REQUIRED, Kindly fill in the hostel capacity to continue"
    ];
    goto end;
}

if ($statusId == '') {
    $response = [
        'success' => false,
        'message' => "STATUS ID IS REQUIRED, Kindly fill in the status id to continue"
    ];
    goto end;
}

$hostelcheck = mysqli_query($conn, "SELECT * FROM hostels_tab WHERE hostel_name = '$hostelName'") or die(mysqli_error($conn));
if (mysqli_num_rows($hostelcheck) > 0) {
    $response = [
        "success" => false,
        "message" => "HOSTEL ALREADY EXIST kindly choose another hostel name"
    ];
    goto end;
}

$hostelId = 'HOSTEL' . date("Ymdhis");

mysqli_query($conn, "INSERT INTO `hostels_tab`
    (`hostel_id`, `hostel_name`, `gender`, `hostel_capacity`, `status_id`, `created_at`, `updated_at`) VALUES
    ('$hostelId', '$hostelName', '$gender', '$hostelCapacity', '$statusId', NOW(), NOW())") or die(mysqli_error($conn));

$createHostelQuery = mysqli_query($conn, "SELECT hostels_tab.*, status_tab.status_name FROM hostels_tab, status_tab WHERE hostels_tab.status_id = status_tab.status_id AND hostels_tab.hostel_id = '$hostelId'") or die(mysqli_error($conn));
$hostelData = mysqli_fetch_assoc($createHostelQuery);

$response = [
    'success' => true,
    'message' => "HOSTEL CREATED SUCCESSFUL",
    'data' => [
        'hostelId' => $hostelData['hostel_id'],
        'hostelName' => $hostelData['hostel_name'],
        'gender' => $hostelData['gender'],
        'hostelCapacity' => $hostelData['hostel_capacity'],
        'statusName' => $hostelData['status_name'],
    ]
];

end:
echo json_encode($response);
?>