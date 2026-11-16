<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php

$studentId     = trim($_POST['studentId'] ?? '');
$checkoutDate  = trim($_POST['checkoutDate'] ?? date('Y-m-d'));
$reason        = trim($_POST['reason'] ?? 'End of Semester');
$roomCondition = trim($_POST['roomCondition'] ?? 'Good Condition');

if ($studentId == '') {
    $response = [
        'success' => false,
        'message' => "STUDENT IS REQUIRED, Kindly select or enter a student ID"
    ];
    goto end;
}

if ($checkoutDate == '') {
    $checkoutDate = date('Y-m-d');
}

if ($reason == '') {
    $reason = 'End of Semester';
}

if ($roomCondition == '') {
    $roomCondition = 'Good Condition';
}

$checkStudent = mysqli_query($conn, "SELECT * FROM student_tab WHERE student_id = '$studentId'") or die(mysqli_error($conn));

if (mysqli_num_rows($checkStudent) == 0) {
    $response = [
        'success' => false,
        'message' => "STUDENT NOT FOUND"
    ];
    goto end;
}

// 1. Check for active allocation
$allocationId = '';
$hostelId = '';
$roomId = '';
$bedId = '';

$activeAllocQuery = mysqli_query($conn, "SELECT * FROM allocation_tab WHERE student_id = '$studentId' AND status_id = 'A' LIMIT 1");
if ($activeAllocQuery && mysqli_num_rows($activeAllocQuery) > 0) {
    $allocData    = mysqli_fetch_assoc($activeAllocQuery);
    $allocationId = $allocData['allocation_id'] ?? '';
    $hostelId     = $allocData['hostel_id'] ?? '';
    $roomId       = $allocData['room_id'] ?? '';
    $bedId        = $allocData['bed_id'] ?? '';
} else {
    // Check any allocation for this student
    $anyAllocQuery = mysqli_query($conn, "SELECT * FROM allocation_tab WHERE student_id = '$studentId' ORDER BY created_at DESC LIMIT 1");
    if ($anyAllocQuery && mysqli_num_rows($anyAllocQuery) > 0) {
        $allocData    = mysqli_fetch_assoc($anyAllocQuery);
        $allocationId = $allocData['allocation_id'] ?? '';
        $hostelId     = $allocData['hostel_id'] ?? '';
        $roomId       = $allocData['room_id'] ?? '';
        $bedId        = $allocData['bed_id'] ?? '';
    } else {
        // Check beds_tab where student is assigned
        $bedQuery = mysqli_query($conn, "SELECT * FROM beds_tab WHERE student_id = '$studentId' LIMIT 1");
        if ($bedQuery && mysqli_num_rows($bedQuery) > 0) {
            $bData = mysqli_fetch_assoc($bedQuery);
            $hostelId     = $bData['hostel_id'] ?? '';
            $roomId       = $bData['room_id'] ?? '';
            $bedId        = $bData['bed_id'] ?? '';
            $allocationId = 'ALLOC-' . $studentId;
        } else {
            $allocationId = 'ALLOC-' . date("YmdHis");
            $hostelId     = trim($_POST['hostelId'] ?? 'HOSTEL1');
            $roomId       = trim($_POST['roomId'] ?? 'ROOM1');
            $bedId        = trim($_POST['bedId'] ?? 'BED1');
        }
    }
}

// Update allocation status to 'U' (Unassigned/Checked out)
if ($allocationId != '') {
    mysqli_query($conn, "UPDATE `allocation_tab` SET `status_id` = 'U' WHERE `allocation_id` = '$allocationId'");
}

// Free bed space in beds_tab
if ($bedId != '') {
    mysqli_query($conn, "UPDATE `beds_tab` SET `status_id` = 'D', `student_id` = NULL WHERE `bed_id` = '$bedId'");
} elseif ($studentId != '') {
    mysqli_query($conn, "UPDATE `beds_tab` SET `status_id` = 'D', `student_id` = NULL WHERE `student_id` = '$studentId'");
}

$checkoutId = 'CHK' . date("YmdHis");

// Check if updated_at column exists in checkouts_tab
$checkCol = mysqli_query($conn, "SHOW COLUMNS FROM `checkouts_tab` LIKE 'updated_at'");
if ($checkCol && mysqli_num_rows($checkCol) > 0) {
    mysqli_query($conn, "INSERT INTO `checkouts_tab` (`checkout_id`, `allocation_id`, `student_id`, `hostel_id`, `room_id`, `bed_id`, `checkout_date`, `reason`, `room_condition`, `created_at`, `updated_at`) VALUES ('$checkoutId', '$allocationId', '$studentId', '$hostelId', '$roomId', '$bedId', '$checkoutDate', '$reason', '$roomCondition', NOW(), NOW())") or die(mysqli_error($conn));
} else {
    mysqli_query($conn, "INSERT INTO `checkouts_tab` (`checkout_id`, `allocation_id`, `student_id`, `hostel_id`, `room_id`, `bed_id`, `checkout_date`, `reason`, `room_condition`, `created_at`) VALUES ('$checkoutId', '$allocationId', '$studentId', '$hostelId', '$roomId', '$bedId', '$checkoutDate', '$reason', '$roomCondition', NOW())") or die(mysqli_error($conn));
}

// Fetch created checkout with safe LEFT JOINs
$fetchQuery = mysqli_query($conn, "SELECT checkouts_tab.*, student_tab.first_name, student_tab.last_name, student_tab.email_address, hostels_tab.hostel_name, rooms_tab.room_number FROM checkouts_tab LEFT JOIN student_tab ON checkouts_tab.student_id = student_tab.student_id LEFT JOIN hostels_tab ON checkouts_tab.hostel_id = hostels_tab.hostel_id LEFT JOIN rooms_tab ON checkouts_tab.room_id = rooms_tab.room_id WHERE checkouts_tab.checkout_id = '$checkoutId'");

$fetchData = $fetchQuery ? mysqli_fetch_assoc($fetchQuery) : null;

$response = [
    'success' => true,
    'message' => "STUDENT CHECKED OUT SUCCESSFULLY, BED IS NOW AVAILABLE",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>
