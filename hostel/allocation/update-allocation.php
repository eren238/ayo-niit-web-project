<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
$allocationId = trim($_POST['allocationId']);
$studentId    = trim($_POST['studentId']);
$hostelId     = trim($_POST['hostelId']);
$roomId       = trim($_POST['roomId']);
$bedId        = trim($_POST['bedId']);
$statusId     = trim($_POST['statusId'] ?? 'A');

if ($allocationId == '') {
    $response = [
        'success' => false,
        'message' => "ALLOCATION ID IS REQUIRED"
    ];
    goto end;
}

if ($studentId == '') {
    $response = [
        'success' => false,
        'message' => "STUDENT IS REQUIRED"
    ];
    goto end;
}

if ($hostelId == '') {
    $response = [
        'success' => false,
        'message' => "HOSTEL IS REQUIRED"
    ];
    goto end;
}

if ($roomId == '') {
    $response = [
        'success' => false,
        'message' => "ROOM IS REQUIRED"
    ];
    goto end;
}

if ($bedId == '') {
    $response = [
        'success' => false,
        'message' => "BED IS REQUIRED"
    ];
    goto end;
}

$checkAlloc = mysqli_query($conn, "SELECT * FROM allocation_tab WHERE allocation_id = '$allocationId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkAlloc) == 0) {
    $response = [
        'success' => false,
        'message' => "ALLOCATION RECORD NOT FOUND"
    ];
    goto end;
}

$allocData = mysqli_fetch_assoc($checkAlloc);
$oldBedId  = $allocData['bed_id'];

if ($oldBedId != $bedId) {
    $checkBed = mysqli_query($conn, "SELECT * FROM beds_tab WHERE bed_id = '$bedId' AND status_id = 'D'") or die(mysqli_error($conn));
    if (mysqli_num_rows($checkBed) == 0) {
        $response = [
            'success' => false,
            'message' => "SELECTED NEW BED IS OCCUPIED OR UNAVAILABLE"
        ];
        goto end;
    }
    mysqli_query($conn, "UPDATE `beds_tab` SET `status_id` = 'D', `student_id` = NULL, `updated_at` = NOW() WHERE `bed_id` = '$oldBedId'") or die(mysqli_error($conn));
    mysqli_query($conn, "UPDATE `beds_tab` SET `status_id` = 'O', `student_id` = '$studentId', `updated_at` = NOW() WHERE `bed_id` = '$bedId'") or die(mysqli_error($conn));
}

if ($statusId == 'U') {
    mysqli_query($conn, "UPDATE `beds_tab` SET `status_id` = 'D', `student_id` = NULL, `updated_at` = NOW() WHERE `bed_id` = '$bedId'") or die(mysqli_error($conn));
}

mysqli_query($conn, "UPDATE `allocation_tab` SET `student_id` = '$studentId', `hostel_id` = '$hostelId', `room_id` = '$roomId', `bed_id` = '$bedId', `status_id` = '$statusId', `updated_at` = NOW() WHERE `allocation_id` = '$allocationId'") or die(mysqli_error($conn));

$fetchUpdated = mysqli_query($conn, "SELECT allocation_tab.*, student_tab.first_name, student_tab.last_name, hostels_tab.hostel_name, rooms_tab.room_number, status_tab.status_name FROM allocation_tab, student_tab, hostels_tab, rooms_tab, status_tab WHERE allocation_tab.student_id = student_tab.student_id AND allocation_tab.hostel_id = hostels_tab.hostel_id AND allocation_tab.room_id = rooms_tab.room_id AND allocation_tab.status_id = status_tab.status_id AND allocation_tab.allocation_id = '$allocationId'") or die(mysqli_error($conn));
$allocationData = mysqli_fetch_assoc($fetchUpdated);

$response = [
    'success' => true,
    'message' => "ALLOCATION UPDATED SUCCESSFULLY",
    'data'    => [
        'allocationId' => $allocationData['allocation_id'],
        'studentId'    => $allocationData['student_id'],
        'firstName'    => $allocationData['first_name'],
        'lastName'     => $allocationData['last_name'],
        'hostelId'     => $allocationData['hostel_id'],
        'hostelName'   => $allocationData['hostel_name'],
        'roomId'       => $allocationData['room_id'],
        'roomNumber'   => $allocationData['room_number'],
        'bedId'        => $allocationData['bed_id'],
        'statusId'     => $allocationData['status_id'],
        'statusName'   => $allocationData['status_name'],
        'createdAt'    => $allocationData['created_at'],
        'updatedAt'    => $allocationData['updated_at']
    ]
];

end:
echo json_encode($response);
?>