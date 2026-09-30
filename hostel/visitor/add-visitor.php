<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$visitorName = trim($_POST['visitorName']);
$studentId   = trim($_POST['studentId']);
$purpose     = trim($_POST['purpose']);
$visitDate   = trim($_POST['visitDate']);
$arrivalTime = trim($_POST['arrivalTime']);
$statusId    = trim($_POST['statusId'] ?? 'IN');

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


$checkStudent = mysqli_query($conn, "SELECT * FROM student_tab WHERE student_id = '$studentId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkStudent) == 0) {
    $response = [
        'success' => false,
        'message' => "STUDENT NOT FOUND"
    ];
    goto end;
}


$visitorId = 'VIS' . date("Ymdhis");


mysqli_query($conn, "INSERT INTO `visitors_tab` (`visitor_id`, `visitor_name`, `student_id`, `purpose`, `visit_date`, `arrival_time`, `status_id`, `created_at`, `updated_at`) VALUES ('$visitorId', '$visitorName', '$studentId', '$purpose', '$visitDate', '$arrivalTime', '$statusId', NOW(), NOW())") or die(mysqli_error($conn));

$createQuery = mysqli_query($conn, "SELECT visitors_tab.*, student_tab.first_name, student_tab.last_name, status_tab.status_name, TIME_FORMAT(visitors_tab.arrival_time, '%h:%i %p') AS arrival, IF(visitors_tab.departure_time IS NULL, '—', TIME_FORMAT(visitors_tab.departure_time, '%h:%i %p')) AS departure FROM visitors_tab, student_tab, status_tab WHERE visitors_tab.student_id = student_tab.student_id AND visitors_tab.status_id = status_tab.status_id AND visitors_tab.visitor_id = '$visitorId'") or die(mysqli_error($conn));
$visitorData = mysqli_fetch_assoc($createQuery);

$response = [
    'success' => true,
    'message' => "VISITOR RECORDED SUCCESSFULLY",
    'data'    => [
        'visitorId'     => $visitorData['visitor_id'],
        'visitorName'   => $visitorData['visitor_name'],
        'studentId'     => $visitorData['student_id'],
        'firstName'     => $visitorData['first_name'],
        'lastName'      => $visitorData['last_name'],
        'purpose'       => $visitorData['purpose'],
        'visitDate'     => $visitorData['visit_date'],
        'arrival'       => $visitorData['arrival'],
        'departure'     => $visitorData['departure'],
        'statusId'      => $visitorData['status_id'],
        'statusName'    => $visitorData['status_name'],
        'createdAt'     => $visitorData['created_at'],
        'updatedAt'     => $visitorData['updated_at']
    ]
];

end:
echo json_encode($response);
?>