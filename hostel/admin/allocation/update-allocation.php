<?php require_once __DIR__ . '/../../config/connection.php'; ?>
<?php
$allocationId = trim($_POST['allocationId'] ?? '');
$studentId    = trim($_POST['studentId'] ?? '');
$hostelId     = trim($_POST['hostelId'] ?? '');
$roomId       = trim($_POST['roomId'] ?? '');
$bedId        = trim($_POST['bedId'] ?? '');
$statusId     = trim($_POST['statusId'] ?? '');

if ($allocationId == '') {
    $response = ['success' => false, 'message' => "ALLOCATION ID IS REQUIRED"];
    goto end;
}

if ($studentId == '') {
    $response = ['success' => false, 'message' => "STUDENT IS REQUIRED"];
    goto end;
}

if ($hostelId == '') {
    $response = ['success' => false, 'message' => "HOSTEL IS REQUIRED"];
    goto end;
}

if ($roomId == '') {
    $response = ['success' => false, 'message' => "ROOM IS REQUIRED"];
    goto end;
}

if ($bedId == '') {
    $response = ['success' => false, 'message' => "BED IS REQUIRED"];
    goto end;
}

if ($statusId == '') {
    $response = ['success' => false, 'message' => "STATUS IS REQUIRED"];
    goto end;
}

$checkAllocation = mysqli_query($conn, "SELECT * FROM allocation_tab WHERE allocation_id = '$allocationId'") or die(mysqli_error($conn));

if (mysqli_num_rows($checkAllocation) == 0) {
    $response = ['success' => false, 'message' => "ALLOCATION NOT FOUND"];
    goto end;
}

$checkStudent = mysqli_query($conn, "SELECT * FROM student_tab WHERE student_id = '$studentId'") or die(mysqli_error($conn));

if (mysqli_num_rows($checkStudent) == 0) {
    $response = ['success' => false, 'message' => "STUDENT NOT FOUND"];
    goto end;
}

$checkHostel = mysqli_query($conn, "SELECT * FROM hostels_tab WHERE hostel_id = '$hostelId'") or die(mysqli_error($conn));

if (mysqli_num_rows($checkHostel) == 0) {
    $response = ['success' => false, 'message' => "HOSTEL NOT FOUND"];
    goto end;
}

$checkRoom = mysqli_query($conn, "SELECT * FROM rooms_tab WHERE room_id = '$roomId'") or die(mysqli_error($conn));

if (mysqli_num_rows($checkRoom) == 0) {
    $response = ['success' => false, 'message' => "ROOM NOT FOUND"];
    goto end;
}

mysqli_query($conn, "UPDATE `allocation_tab` SET `student_id` = '$studentId', `hostel_id` = '$hostelId', `room_id` = '$roomId', `bed_id` = '$bedId', `status_id` = '$statusId', `updated_at` = NOW() WHERE `allocation_id` = '$allocationId'") or die(mysqli_error($conn));

$fetchQuery = mysqli_query($conn, "SELECT allocation_tab.*, student_tab.first_name, student_tab.last_name, student_tab.email_address, hostels_tab.hostel_name, rooms_tab.room_number, status_tab.status_name FROM allocation_tab, student_tab, hostels_tab, rooms_tab, status_tab WHERE allocation_tab.student_id = student_tab.student_id AND allocation_tab.hostel_id = hostels_tab.hostel_id AND allocation_tab.room_id = rooms_tab.room_id AND allocation_tab.status_id = status_tab.status_id AND allocation_tab.allocation_id = '$allocationId'") or die(mysqli_error($conn));

$fetchData = mysqli_fetch_assoc($fetchQuery);

$response = [
    'success' => true,
    'message' => "ALLOCATION UPDATED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>