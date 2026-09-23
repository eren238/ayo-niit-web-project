<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$studentId = trim($_POST['studentId']);
$hostelId  = trim($_POST['hostelId']);
$roomId    = trim($_POST['roomId']);
$bedId     = trim($_POST['bedId']);

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


mysqli_query($conn, "INSERT INTO `allocation_tab` 
    (`allocation_id`, `student_id`, `hostel_id`, `room_id`, `bed_id`, `status_id`, `created_at`, `updated_at`) VALUES 
    ('$allocationId', '$studentId', '$hostelId', '$roomId', '$bedId', 'A', NOW(), NOW())") or die(mysqli_error($conn));


mysqli_query($conn, "UPDATE `beds_tab` SET `status_id`  = 'O', `student_id` = '$studentId', `updated_at` = NOW()  WHERE `bed_id` = '$bedId'") or die(mysqli_error($conn));
$response = [
    'success' => true,
    'message' => "STUDENT ALLOCATED SUCCESSFULLY",
    'data'    => [
        'allocationId' => $allocationId,
        'studentId'    => $studentId,
        'bedId'        => $bedId,
        'status'       => 'Allocated'
    ]
];

end:
echo json_encode($response);
?>