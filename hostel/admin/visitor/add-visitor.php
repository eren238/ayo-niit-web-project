<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php

$visitorName   = trim($_POST['visitorName'] ?? '');
$studentId     = trim($_POST['studentId'] ?? '');
$purpose       = trim($_POST['purpose'] ?? '');
$visitDate     = trim($_POST['visitDate'] ?? '');
$arrivalTime   = trim($_POST['arrivalTime'] ?? '');
$departureTime = trim($_POST['departureTime'] ?? '');
$statusId      = trim($_POST['statusId'] ?? 'IN');

if ($visitorName == '') {
    $response = [
        'success' => false,
        'message' => "VISITOR NAME IS REQUIRED, Kindly fill in visitor name to continue"
    ];
    goto end;
}

if ($studentId == '') {
    $response = [
        'success' => false,
        'message' => "STUDENT IS REQUIRED, Kindly select the student being visited"
    ];
    goto end;
}

if ($purpose == '') {
    $response = [
        'success' => false,
        'message' => "PURPOSE OF VISIT IS REQUIRED, Kindly fill in the purpose to continue"
    ];
    goto end;
}

if ($visitDate == '') {
    $response = [
        'success' => false,
        'message' => "VISIT DATE IS REQUIRED, Kindly fill in the visit date to continue"
    ];
    goto end;
}

if ($arrivalTime == '') {
    $response = [
        'success' => false,
        'message' => "ARRIVAL TIME IS REQUIRED, Kindly fill in the arrival time to continue"
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

$visitorId = 'VIS' . date("YmdHis");
$depTime   = ($departureTime != '') ? "'$departureTime'" : "NULL";

mysqli_query($conn, "INSERT INTO `visitors_tab` (`visitor_id`, `visitor_name`, `student_id`, `purpose`, `visit_date`, `arrival_time`, `departure_time`, `status_id`, `created_at`, `updated_at`) VALUES ('$visitorId', '$visitorName', '$studentId', '$purpose', '$visitDate', '$arrivalTime', $depTime, '$statusId', NOW(), NOW())") or die(mysqli_error($conn));

$createQuery = mysqli_query($conn, "SELECT visitors_tab.visitor_id, visitors_tab.visitor_name, visitors_tab.student_id, visitors_tab.purpose, visitors_tab.created_at, visitors_tab.updated_at, student_tab.first_name, student_tab.last_name, status_tab.status_name, DATE_FORMAT(visitors_tab.visit_date, '%d %M %Y') AS visitDate, TIME_FORMAT(visitors_tab.arrival_time, '%h:%i %p') AS arrivalTime, TIME_FORMAT(visitors_tab.departure_time, '%h:%i %p') AS departureTime FROM visitors_tab, student_tab, status_tab WHERE visitors_tab.student_id = student_tab.student_id AND visitors_tab.status_id = status_tab.status_id AND visitors_tab.visitor_id = '$visitorId'") or die(mysqli_error($conn));

$visitorData = mysqli_fetch_assoc($createQuery);

$response = [
    'success' => true,
    'message' => "VISITOR RECORDED SUCCESSFULLY",
    'data'    => [
        'visitorId'     => $visitorData['visitor_id'],
        'visitorName'   => $visitorData['visitor_name'],
        'firstName'     => $visitorData['first_name'],
        'lastName'      => $visitorData['last_name'],
        'studentId'     => $visitorData['student_id'],
        'purpose'       => $visitorData['purpose'],
        'visitDate'     => $visitorData['visitDate'],
        'arrivalTime'   => $visitorData['arrivalTime'],
        'departureTime' => $visitorData['departureTime'],
        'statusName'    => $visitorData['status_name'],
        'createdAt'     => $visitorData['created_at'],
        'updatedAt'     => $visitorData['updated_at']
    ]
];
end:
echo json_encode($response);
?>