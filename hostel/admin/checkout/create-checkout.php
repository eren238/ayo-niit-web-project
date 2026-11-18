<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLERATION OF VARIABLE

$studentId = trim($_POST['studentId']);
$checkoutDate = trim($_POST['checkoutDate']);
$reason = trim($_POST['reason']);
$roomCondition = trim($_POST['roomCondition']);

if ($studentId == '') {
    $response = [
        'success' => false,
        'message' => "STUDENT IS REQUIRED, Kindly select or enter a student ID"
    ];
    goto end;
}

if ($checkoutDate == '') {
    $response = [
        'success' => false,
        'message' => "CHECKOUT DATE IS REQUIRED, Kindly select checkout date to continue"
    ];
    goto end;
}

if ($reason == '') {
    $response = [
        'success' => false,
        'message' => "REASON IS REQUIRED, Kindly fill in reason to continue"
    ];
    goto end;
}

if ($roomCondition == '') {
    $response = [
        'success' => false,
        'message' => "ROOM CONDITION IS REQUIRED, Kindly fill in room condition to continue"
    ];
    goto end;
}

$checkStudent = mysqli_query($conn, "SELECT * FROM student_tab WHERE student_id = '$studentId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkStudent) == 0) {
    $response = [
        'success' => false,
        'message' => "STUDENT NOT FOUND"
    ];
    goto end;
}

$allocationId = '';
$hostelId = '';
$roomId = '';
$bedId = '';

$activeAllocQuery = mysqli_query($conn, "SELECT * FROM allocation_tab WHERE student_id = '$studentId' AND status_id = 'A' LIMIT 1") or die(mysqli_error($conn));
if (mysqli_num_rows($activeAllocQuery) > 0) {
    $allocData = mysqli_fetch_assoc($activeAllocQuery);
    $allocationId = $allocData['allocation_id'];
    $hostelId = $allocData['hostel_id'];
    $roomId = $allocData['room_id'];
    $bedId = $allocData['bed_id'];
    mysqli_query($conn, "UPDATE `allocation_tab` SET `status_id` = 'U' WHERE `allocation_id` = '$allocationId'") or die(mysqli_error($conn));
    mysqli_query($conn, "UPDATE `beds_tab` SET `status_id` = 'D', `student_id` = NULL WHERE `bed_id` = '$bedId'") or die(mysqli_error($conn));
} else {
    $allocationId = 'ALLOC-' . $studentId;
    mysqli_query($conn, "UPDATE `beds_tab` SET `status_id` = 'D', `student_id` = NULL WHERE `student_id` = '$studentId'") or die(mysqli_error($conn));
}

$checkoutId = 'CHK' . date("Ymdhis");

mysqli_query($conn, "INSERT INTO `checkouts_tab`
    (`checkout_id`, `allocation_id`, `student_id`, `hostel_id`, `room_id`, `bed_id`, `checkout_date`, `reason`, `room_condition`, `created_at`) VALUES
    ('$checkoutId', '$allocationId', '$studentId', '$hostelId', '$roomId', '$bedId', '$checkoutDate', '$reason', '$roomCondition', NOW())") or die(mysqli_error($conn));

$createCheckoutQuery = mysqli_query($conn, "SELECT checkouts_tab.*, student_tab.first_name, student_tab.last_name, hostels_tab.hostel_name, rooms_tab.room_number FROM checkouts_tab LEFT JOIN student_tab ON checkouts_tab.student_id = student_tab.student_id LEFT JOIN hostels_tab ON checkouts_tab.hostel_id = hostels_tab.hostel_id LEFT JOIN rooms_tab ON checkouts_tab.room_id = rooms_tab.room_id WHERE checkouts_tab.checkout_id = '$checkoutId'") or die(mysqli_error($conn));
$checkoutData = mysqli_fetch_assoc($createCheckoutQuery);

$response = [
    'success' => true,
    'message' => "CHECKOUT CREATED SUCCESSFUL",
    'data' => [
        'checkoutId' => $checkoutData['checkout_id'],
        'studentId' => $checkoutData['student_id'],
        'firstName' => $checkoutData['first_name'],
        'lastName' => $checkoutData['last_name'],
        'hostelName' => $checkoutData['hostel_name'],
        'roomNumber' => $checkoutData['room_number'],
        'bedId' => $checkoutData['bed_id'],
        'checkoutDate' => $checkoutData['checkout_date'],
        'reason' => $checkoutData['reason'],
        'roomCondition' => $checkoutData['room_condition'],
    ]
];

end:
echo json_encode($response);
?>