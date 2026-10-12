<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php

mysqli_report(MYSQLI_REPORT_OFF);

$studentId  = trim($_POST['studentId'] ?? '');
$hostelName = trim($_POST['hostelName'] ?? '');
$roomNumber = trim($_POST['roomNumber'] ?? '');

if ($studentId == '') {
    $response = [
        'success' => false,
        'message' => "STUDENT ID IS REQUIRED"
    ];
    goto end;
}

if ($hostelName == '') {
    $response = [
        'success' => false,
        'message' => "HOSTEL NAME IS REQUIRED, Kindly select a hostel"
    ];
    goto end;
}

if ($roomNumber == '') {
    $response = [
        'success' => false,
        'message' => "ROOM NUMBER IS REQUIRED, Kindly select a room"
    ];
    goto end;
}

$studentQuery = mysqli_query($conn, "SELECT * FROM student_tab WHERE student_id = '$studentId' AND status_id = '1'") or die(mysqli_error($conn));
if (mysqli_num_rows($studentQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "STUDENT NOT FOUND OR INACTIVE"
    ];
    goto end;
}
$studentData   = mysqli_fetch_assoc($studentQuery);
$studentGender = $studentData['gender'];

$checkActiveBooking = mysqli_query($conn, "SELECT * FROM room_bookings_tab WHERE student_id = '$studentId' AND status_id = 'BK'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkActiveBooking) > 0) {
    $response = [
        'success' => false,
        'message' => "YOU ALREADY HAVE A PENDING ROOM BOOKING AWAITING PAYMENT"
    ];
    goto end;
}

$checkAlloc = mysqli_query($conn, "SELECT * FROM allocation_tab WHERE student_id = '$studentId' AND status_id = 'A'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkAlloc) > 0) {
    $response = [
        'success' => false,
        'message' => "YOU ALREADY HAVE AN ALLOCATED ROOM"
    ];
    goto end;
}

$hostelQuery = mysqli_query($conn, "SELECT * FROM hostels_tab WHERE hostel_name = '$hostelName' AND status_id = '1'") or die(mysqli_error($conn));
if (mysqli_num_rows($hostelQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "HOSTEL '$hostelName' NOT FOUND OR INACTIVE"
    ];
    goto end;
}
$hostelData = mysqli_fetch_assoc($hostelQuery);
$hostelId   = $hostelData['hostel_id'];

if ($hostelData['gender'] != 'Mixed' && $hostelData['gender'] != $studentGender) {
    $response = [
        'success' => false,
        'message' => "THIS HOSTEL IS NOT DESIGNATED FOR YOUR GENDER"
    ];
    goto end;
}

$roomQuery = mysqli_query($conn, "SELECT * FROM rooms_tab WHERE room_number = '$roomNumber' AND hostel_id = '$hostelId' AND status_id = '1'") or die(mysqli_error($conn));
if (mysqli_num_rows($roomQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "ROOM '$roomNumber' NOT FOUND IN '$hostelName'"
    ];
    goto end;
}
$roomData     = mysqli_fetch_assoc($roomQuery);
$roomCapacity = $roomData['room_capacity'];

$countRoomBookings = mysqli_query($conn, "SELECT COUNT(*) AS total_booked FROM room_bookings_tab WHERE hostel_name = '$hostelName' AND room_number = '$roomNumber' AND status_id = 'BK'") or die(mysqli_error($conn));
$countData = mysqli_fetch_assoc($countRoomBookings);
if ($countData['total_booked'] >= $roomCapacity) {
    $response = [
        'success' => false,
        'message' => "SORRY, ROOM '$roomNumber' IS FULLY BOOKED"
    ];
    goto end;
}

$bookingId = 'BK' . date("Ymdhis");

mysqli_query($conn, "INSERT INTO `room_bookings_tab` (`booking_id`, `student_id`, `hostel_name`, `room_number`, `booking_date`, `status_id`, `created_at`, `updated_at`) VALUES ('$bookingId', '$studentId', '$hostelName', '$roomNumber', CURDATE(), 'BK', NOW(), NOW())") or die(mysqli_error($conn));

$fetchQuery = mysqli_query($conn, "SELECT room_bookings_tab.*, student_tab.first_name, student_tab.last_name, status_tab.status_name, DATE_FORMAT(room_bookings_tab.booking_date, '%d %b %Y') AS formatted_date FROM room_bookings_tab, student_tab, status_tab WHERE room_bookings_tab.student_id = student_tab.student_id AND room_bookings_tab.status_id = status_tab.status_id AND room_bookings_tab.booking_id = '$bookingId'") or die(mysqli_error($conn));
$bookDetails = mysqli_fetch_assoc($fetchQuery);

$response = [
    'success' => true,
    'message' => "ROOM BOOKED SUCCESSFULLY! KINDLY PROCEED TO PAYMENT TO SECURE YOUR SPACE",
    'data'    => [
        'sn'            => $bookDetails['sn'],
        'bookingId'     => $bookDetails['booking_id'],
        'studentId'     => $bookDetails['student_id'],
        'firstName'     => $bookDetails['first_name'],
        'lastName'      => $bookDetails['last_name'],
        'hostelName'    => $bookDetails['hostel_name'],
        'roomNumber'    => $bookDetails['room_number'],
        'bookingDate'   => $bookDetails['booking_date'],
        'formattedDate' => $bookDetails['formatted_date'],
        'statusName'    => $bookDetails['status_name'],
        'createdAt'     => $bookDetails['created_at']
    ]
];

end:
echo json_encode($response);
?>