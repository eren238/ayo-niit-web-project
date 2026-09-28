<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$studentId     = trim($_POST['studentId']);
$checkoutDate  = trim($_POST['checkoutDate']);
$reason        = trim($_POST['reason']);
$roomCondition = trim($_POST['roomCondition']);

if ($studentId == '') {
    $response = [
        'success' => false,
        'message' => "STUDENT IS REQUIRED, Kindly select a student"
    ];
    goto end;
}

if ($reason == '') {
    $response = [
        'success' => false,
        'message' => "REASON IS REQUIRED, Kindly select a reason for checkout"
    ];
    goto end;
}

if ($roomCondition == '') {
    $response = [
        'success' => false,
        'message' => "ROOM CONDITION IS REQUIRED, Kindly select the room condition"
    ];
    goto end;
}


$activeAllocQuery = mysqli_query($conn, "SELECT * FROM allocation_tab 
    WHERE student_id = '$studentId' AND status_id = 'A' LIMIT 1") or die(mysqli_error($conn));

if (mysqli_num_rows($activeAllocQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "NO ACTIVE ALLOCATION FOUND FOR THIS STUDENT"
    ];
    goto end;
}

$allocData    = mysqli_fetch_assoc($activeAllocQuery);
$allocationId = $allocData['allocation_id'];
$hostelId     = $allocData['hostel_id'];
$roomId       = $allocData['room_id'];
$bedId        = $allocData['bed_id'];


mysqli_query($conn, "UPDATE `allocation_tab` SET 
    `status_id`  = 'U', 
    `updated_at` = NOW() 
    WHERE `allocation_id` = '$allocationId'") or die(mysqli_error($conn));


mysqli_query($conn, "UPDATE `beds_tab` SET 
    `status_id`  = 'D', 
    `student_id` = NULL, 
    `updated_at` = NOW() 
    WHERE `bed_id` = '$bedId'") or die(mysqli_error($conn));


$checkoutId = 'CHK' . date("YmdHis");
mysqli_query($conn, "INSERT INTO `checkouts_tab` 
    (`checkout_id`, `allocation_id`, `student_id`, `hostel_id`, `room_id`, `bed_id`, `checkout_date`, `reason`, `room_condition`, `created_at`) VALUES 
    ('$checkoutId', '$allocationId', '$studentId', '$hostelId', '$roomId', '$bedId', '$checkoutDate', '$reason', '$roomCondition', NOW())") or die(mysqli_error($conn));

$response = [
    'success' => true,
    'message' => "STUDENT CHECKED OUT SUCCESSFULLY, BED IS NOW AVAILABLE",
    'data'    => [
        'checkoutId'    => $checkoutId,
        'studentId'     => $studentId,
        'bedId'         => $bedId,
        'checkoutDate'  => $checkoutDate,
        'reason'        => $reason,
        'roomCondition' => $roomCondition
    ]
];

end:
echo json_encode($response);
?>