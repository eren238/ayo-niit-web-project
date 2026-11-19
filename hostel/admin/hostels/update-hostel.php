<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLERATION OF VARIABLE

$hostelId = trim($_POST['hostelId']);
$hostelName = trim($_POST['hostelName']);
$gender = trim($_POST['gender']);
$hostelCapacity = trim($_POST['hostelCapacity']);
$statusId = trim($_POST['statusId']);

if ($hostelId == '') {
    $response = [
        'success' => false,
        'message' => "HOSTEL ID IS REQUIRED, Kindly fill in the hostel ID to continue"
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
        'message' => "STATUS ID IS REQUIRED, Kindly select status to continue"
    ];
    goto end;
}

$hostelCheck = mysqli_query($conn, "SELECT * FROM hostels_tab WHERE hostel_name = '$hostelName' AND hostel_id != '$hostelId'") or die(mysqli_error($conn));
if (mysqli_num_rows($hostelCheck) > 0) {
    $response = [
        'success' => false,
        'message' => "HOSTEL NAME '$hostelName' IS ALREADY IN USE BY ANOTHER HOSTEL"
    ];
    goto end;
}

mysqli_query($conn, "UPDATE `hostels_tab` SET 
    `hostel_name` = '$hostelName',
    `gender` = '$gender',
    `hostel_capacity` = '$hostelCapacity',
    `status_id` = '$statusId',
    `updated_at` = NOW()
    WHERE `hostel_id` = '$hostelId'") or die(mysqli_error($conn));

$fetchUpdated = mysqli_query($conn, "SELECT hostels_tab.*, status_tab.status_name FROM hostels_tab, status_tab WHERE hostels_tab.status_id = status_tab.status_id AND hostels_tab.hostel_id = '$hostelId'") or die(mysqli_error($conn));
$hostelData = mysqli_fetch_assoc($fetchUpdated);

$response = [
    'success' => true,
    'message' => "HOSTEL UPDATED SUCCESSFULLY",
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