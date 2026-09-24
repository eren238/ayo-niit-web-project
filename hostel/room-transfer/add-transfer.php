<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$studentId = trim($_POST['studentId']);
$newBedId  = trim($_POST['newBedId']);
$reason    = trim($_POST['reason']);

if ($studentId == '') {
    $response = [
        'success' => false,
        'message' => "STUDENT IS REQUIRED, Kindly select a student"
    ];
    goto end;
}

if ($newBedId == '') {
    $response = [
        'success' => false,
        'message' => "NEW BED IS REQUIRED, Kindly select a new bed"
    ];
    goto end;
}

if ($reason == '') {
    $response = [
        'success' => false,
        'message' => "REASON IS REQUIRED, Kindly fill in the reason for transfer"
    ];
    goto end;
}


$currentAllocQuery = mysqli_query($conn, "SELECT * FROM allocation_tab  WHERE student_id = '$studentId' AND status_id = 'A' LIMIT 1") or die(mysqli_error($conn));

if (mysqli_num_rows($currentAllocQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "STUDENT HAS NO ACTIVE ALLOCATION TO TRANSFER FROM"
    ];
    goto end;
}

$currentAlloc = mysqli_fetch_assoc($currentAllocQuery);
$oldAllocationId = $currentAlloc['allocation_id'];
$oldBedId        = $currentAlloc['bed_id'];
$oldRoomId       = $currentAlloc['room_id'];
$oldHostelId     = $currentAlloc['hostel_id'];

if ($oldBedId == $newBedId) {
    $response = [
        'success' => false,
        'message' => "STUDENT IS ALREADY ON THIS BED"
    ];
    goto end;
}

$newBedQuery = mysqli_query($conn, "SELECT * FROM beds_tab 
    WHERE bed_id = '$newBedId' AND status_id = 'D'") or die(mysqli_error($conn));
if (mysqli_num_rows($newBedQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "SELECTED NEW BED IS NO LONGER AVAILABLE"
    ];
    goto end;
}

$newBedData = mysqli_fetch_assoc($newBedQuery);
$newRoomId   = $newBedData['room_id'];
$newHostelId = $newBedData['hostel_id'];


mysqli_query($conn, "UPDATE `beds_tab` SET `status_id`  = 'D', `student_id` = NULL, `updated_at` = NOW() WHERE `bed_id` = '$oldBedId'") or die(mysqli_error($conn));
mysqli_query($conn, "UPDATE `beds_tab` SET  `status_id`  = 'O', `student_id` = '$studentId',  `updated_at` = NOW() WHERE `bed_id` = '$newBedId'") or die(mysqli_error($conn));
mysqli_query($conn, "UPDATE `allocation_tab` SET `hostel_id`  = '$newHostelId',`room_id` = '$newRoomId',`bed_id`  = '$newBedId', `updated_at` = NOW() WHERE `allocation_id` = '$oldAllocationId'") or die(mysqli_error($conn));


$transferId = 'TRAF' . date("YmdHis");
mysqli_query($conn, "INSERT INTO `transfers_tab` 
    (`transfer_id`, `student_id`, `old_bed_id`, `old_room_id`, `old_hostel_id`, `new_bed_id`, `new_room_id`, `new_hostel_id`, `reason`, `created_at`) VALUES 
    ('$transferId', '$studentId', '$oldBedId', '$oldRoomId', '$oldHostelId', '$newBedId', '$newRoomId', '$newHostelId', '$reason', NOW())") or die(mysqli_error($conn));

$response = [
    'success' => true,
    'message' => "STUDENT ROOM TRANSFERRED SUCCESSFULLY",
    'data'    => [
        'transferId' => $transferId,
        'studentId'  => $studentId,
        'oldBed'     => $oldBedId,
        'newBed'     => $newBedId,
        'reason'     => $reason
    ]
];
end:
echo json_encode($response);
?>