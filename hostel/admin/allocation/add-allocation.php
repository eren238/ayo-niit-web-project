<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php

$studentId = trim($_POST['studentId'] ?? '');
$hostelId  = trim($_POST['hostelId'] ?? '');
$roomId    = trim($_POST['roomId'] ?? '');
$bedId     = trim($_POST['bedId'] ?? '');

if ($studentId == '') {
    $response = [
        'success' => false,
        'message' => "STUDENT IS REQUIRED, Kindly select a student"
    ];
    goto end;
}

if ($hostelId == '') {
    $response = [
        'success' => false,
        'message' => "HOSTEL IS REQUIRED, Kindly select a hostel"
    ];
    goto end;
}

if ($roomId == '') {
    $response = [
        'success' => false,
        'message' => "ROOM IS REQUIRED, Kindly select a room"
    ];
    goto end;
}

if ($bedId == '') {
    $response = [
        'success' => false,
        'message' => "BED IS REQUIRED, Kindly select an available bed"
    ];
    goto end;
}

$checkStudent = mysqli_query($conn, "SELECT * FROM student_tab WHERE student_id = '$studentId'") or die(mysqli_error($conn));

if (mysqli_num_rows($checkStudent) == 0) {
    $response = [
        'success' => false,
        'message' => "STUDENT NOT FOUND! The selected student does not exist"
    ];
    goto end;
}

$checkHostel = mysqli_query($conn, "SELECT * FROM hostels_tab WHERE hostel_id = '$hostelId'") or die(mysqli_error($conn));

if (mysqli_num_rows($checkHostel) == 0) {
    $response = [
        'success' => false,
        'message' => "HOSTEL NOT FOUND! The selected hostel does not exist"
    ];
    goto end;
}

$checkRoom = mysqli_query($conn, "SELECT * FROM rooms_tab WHERE room_id = '$roomId'") or die(mysqli_error($conn));

if (mysqli_num_rows($checkRoom) == 0) {
    $response = [
        'success' => false,
        'message' => "ROOM NOT FOUND! The selected room does not exist"
    ];
    goto end;
}

$checkStudentAllocation = mysqli_query($conn, "SELECT * FROM allocation_tab WHERE student_id = '$studentId' AND status_id = 'A'") or die(mysqli_error($conn));

if (mysqli_num_rows($checkStudentAllocation) > 0) {
    $response = [
        'success' => false,
        'message' => "STUDENT ALREADY HAS AN ACTIVE ALLOCATION!"
    ];
    goto end;
}

$checkBedQuery = mysqli_query($conn, "SELECT * FROM beds_tab WHERE bed_id = '$bedId' AND status_id = 'D'") or die(mysqli_error($conn));

if (mysqli_num_rows($checkBedQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "SELECTED BED IS NO LONGER AVAILABLE OR IS OCCUPIED"
    ];
    goto end;
}

$allocationId = 'ALLOC' . date("YmdHis");

mysqli_query($conn, "INSERT INTO `allocation_tab` (`allocation_id`, `student_id`, `hostel_id`, `room_id`, `bed_id`, `status_id`, `created_at`, `updated_at`) VALUES ('$allocationId', '$studentId', '$hostelId', '$roomId', '$bedId', 'A', NOW(), NOW())") or die(mysqli_error($conn));

mysqli_query($conn, "UPDATE `beds_tab` SET `status_id` = 'O', `student_id` = '$studentId', `updated_at` = NOW() WHERE `bed_id` = '$bedId'") or die(mysqli_error($conn));

$fetchQuery = mysqli_query($conn, "SELECT allocation_tab.*, student_tab.first_name, student_tab.last_name, student_tab.email_address, hostels_tab.hostel_name, rooms_tab.room_number, status_tab.status_name FROM allocation_tab, student_tab, hostels_tab, rooms_tab, status_tab WHERE allocation_tab.student_id = student_tab.student_id AND allocation_tab.hostel_id = hostels_tab.hostel_id AND allocation_tab.room_id = rooms_tab.room_id AND allocation_tab.status_id = status_tab.status_id AND allocation_tab.allocation_id = '$allocationId'") or die(mysqli_error($conn));

$fetchData = mysqli_fetch_assoc($fetchQuery);

$response = [
    'success' => true,
    'message' => "STUDENT ALLOCATED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>