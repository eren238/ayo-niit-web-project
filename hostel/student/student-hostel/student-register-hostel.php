<?php require_once __DIR__ . '/../../../config/connection.php'; ?>

<?php

$studentId  = trim($_POST['studentId'] ?? '');
$hostelName = trim($_POST['hostelName'] ?? '');


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

$checkActiveBooking = mysqli_query($conn, "SELECT * FROM bookings_tab WHERE student_id = '$studentId' AND status_id = 'BK'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkActiveBooking) > 0) {
    $response = [
        'success' => false,
        'message' => "YOU ALREADY HAVE A PENDING BOOKING AWAITING PAYMENT"
    ];
    goto end;
}

$checkAlloc = mysqli_query($conn, "SELECT * FROM allocation_tab WHERE student_id = '$studentId' AND status_id = 'A'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkAlloc) > 0) {
    $response = [
        'success' => false,
        'message' => "YOU ARE ALREADY ALLOCATED TO A HOSTEL FOR THIS SESSION"
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
$capacity   = $hostelData['capacity'];

if ($hostelData['gender'] != 'Mixed' && $hostelData['gender'] != $studentGender) {
    $response = [
        'success' => false,
        'message' => "THIS HOSTEL IS NOT DESIGNATED FOR YOUR GENDER"
    ];
    goto end;
}

$bookingId = 'BK' . date("Ymdhis");

mysqli_query($conn, "INSERT INTO `bookings_tab` (`booking_id`, `student_id`, `hostel_name`, `session`, `booking_date`, `status_id`, `created_at`, `updated_at`) VALUES ('$bookingId', '$studentId', '$hostelName', CURDATE(), 'BK', NOW(), NOW())") or die(mysqli_error($conn));

$fetchQuery = mysqli_query($conn, "SELECT bookings_tab.*, student_tab.first_name, student_tab.last_name, status_tab.status_name, DATE_FORMAT(bookings_tab.booking_date, '%d %b %Y') AS formatted_date FROM bookings_tab, student_tab, status_tab WHERE bookings_tab.student_id = student_tab.student_id AND bookings_tab.status_id = status_tab.status_id AND bookings_tab.booking_id = '$bookingId'") or die(mysqli_error($conn));
$bookDetails = mysqli_fetch_assoc($fetchQuery);

$response = [
    'success' => true,
    'message' => "HOSTEL BOOKED SUCCESSFULLY! KINDLY PROCEED TO PAYMENT TO SECURE YOUR SPACE",
    'data'    => [
        'sn'            => $bookDetails['sn'],
        'bookingId'     => $bookDetails['booking_id'],
        'studentId'     => $bookDetails['student_id'],
        'firstName'     => $bookDetails['first_name'],
        'lastName'      => $bookDetails['last_name'],
        'hostelName'    => $bookDetails['hostel_name'],
        'bookingDate'   => $bookDetails['booking_date'],
        'formattedDate' => $bookDetails['formatted_date'],
        'statusName'    => $bookDetails['status_name'],
        'createdAt'     => $bookDetails['created_at']
    ]
];

end:
echo json_encode($response);
?>