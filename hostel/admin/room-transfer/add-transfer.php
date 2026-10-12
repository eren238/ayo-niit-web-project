<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php

$studentId = trim($_POST['studentId'] ?? '');
$newBedId  = trim($_POST['newBedId'] ?? '');
$reason    = trim($_POST['reason'] ?? '');

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

$currentAllocQuery = mysqli_query($conn, "SELECT * FROM allocation_tab WHERE student_id = '$studentId' AND status_id = 'A' LIMIT 1") or die(mysqli_error($conn));

if (mysqli_num_rows($currentAllocQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "STUDENT HAS NO ACTIVE ALLOCATION TO TRANSFER FROM"
    ];
    goto end;
}

$currentAlloc    = mysqli_fetch_assoc($currentAllocQuery);
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

$newBedQuery = mysqli_query($conn, "SELECT * FROM beds_tab WHERE bed_id = '$newBedId' AND status_id = 'D'") or die(mysqli_error($conn));

if (mysqli_num_rows($newBedQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "SELECTED NEW BED IS NO LONGER AVAILABLE"
    ];
    goto end;
}

$newBedData  = mysqli_fetch_assoc($newBedQuery);
$newRoomId   = $newBedData['room_id'];
$newHostelId = $newBedData['hostel_id'];

mysqli_query($conn, "UPDATE `beds_tab` SET `status_id` = 'D', `student_id` = NULL, `updated_at` = NOW() WHERE `bed_id` = '$oldBedId'") or die(mysqli_error($conn));

mysqli_query($conn, "UPDATE `beds_tab` SET `status_id` = 'O', `student_id` = '$studentId', `updated_at` = NOW() WHERE `bed_id` = '$newBedId'") or die(mysqli_error($conn));

mysqli_query($conn, "UPDATE `allocation_tab` SET `hostel_id` = '$newHostelId', `room_id` = '$newRoomId', `bed_id` = '$newBedId', `updated_at` = NOW() WHERE `allocation_id` = '$oldAllocationId'") or die(mysqli_error($conn));

$transferId = 'TRAF' . date("YmdHis");

mysqli_query($conn, "INSERT INTO `transfers_tab` (`transfer_id`, `student_id`, `old_bed_id`, `old_room_id`, `old_hostel_id`, `new_bed_id`, `new_room_id`, `new_hostel_id`, `reason`, `created_at`) VALUES ('$transferId', '$studentId', '$oldBedId', '$oldRoomId', '$oldHostelId', '$newBedId', '$newRoomId', '$newHostelId', '$reason', NOW())") or die(mysqli_error($conn));

$fetchQuery = mysqli_query($conn, "SELECT transfers_tab.*, student_tab.first_name, student_tab.last_name, old_h.hostel_name AS old_hostel_name, old_r.room_number AS old_room_number, new_h.hostel_name AS new_hostel_name, new_r.room_number AS new_room_number FROM transfers_tab, student_tab, hostels_tab AS old_h, rooms_tab AS old_r, hostels_tab AS new_h, rooms_tab AS new_r WHERE transfers_tab.student_id = student_tab.student_id AND transfers_tab.old_hostel_id = old_h.hostel_id AND transfers_tab.old_room_id = old_r.room_id AND transfers_tab.new_hostel_id = new_h.hostel_id AND transfers_tab.new_room_id = new_r.room_id AND transfers_tab.transfer_id = '$transferId'") or die(mysqli_error($conn));

$fetchData = mysqli_fetch_assoc($fetchQuery);

$response = [
    'success' => true,
    'message' => "STUDENT TRANSFERRED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>