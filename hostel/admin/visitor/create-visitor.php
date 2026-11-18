<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLERATION OF VARIABLE

$visitorName = trim($_POST['visitorName']);
$studentId = trim($_POST['studentId']);
$purpose = trim($_POST['purpose']);
$visitDate = trim($_POST['visitDate']);
$arrivalTime = trim($_POST['arrivalTime']);
$departureTime = trim($_POST['departureTime']);
$statusId = trim($_POST['statusId']);

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

if ($statusId == '') {
    $response = [
        'success' => false,
        'message' => "STATUS ID IS REQUIRED, Kindly fill in the status id to continue"
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
$depTime = ($departureTime != '') ? "'$departureTime'" : "NULL";

mysqli_query($conn, "INSERT INTO `visitors_tab`
    (`visitor_id`, `visitor_name`, `student_id`, `purpose`, `visit_date`, `arrival_time`, `departure_time`, `status_id`, `created_at`, `updated_at`) VALUES
    ('$visitorId', '$visitorName', '$studentId', '$purpose', '$visitDate', '$arrivalTime', $depTime, '$statusId', NOW(), NOW())") or die(mysqli_error($conn));

$createVisitorQuery = mysqli_query($conn, "SELECT visitors_tab.*, student_tab.first_name, student_tab.last_name, status_tab.status_name FROM visitors_tab, student_tab, status_tab WHERE visitors_tab.student_id = student_tab.student_id AND visitors_tab.status_id = status_tab.status_id AND visitors_tab.visitor_id = '$visitorId'") or die(mysqli_error($conn));
$visitorData = mysqli_fetch_assoc($createVisitorQuery);

$response = [
    'success' => true,
    'message' => "VISITOR CREATED SUCCESSFUL",
    'data' => [
        'visitorId' => $visitorData['visitor_id'],
        'visitorName' => $visitorData['visitor_name'],
        'studentId' => $visitorData['student_id'],
        'firstName' => $visitorData['first_name'],
        'lastName' => $visitorData['last_name'],
        'purpose' => $visitorData['purpose'],
        'visitDate' => $visitorData['visit_date'],
        'arrivalTime' => $visitorData['arrival_time'],
        'departureTime' => $visitorData['departure_time'],
        'statusName' => $visitorData['status_name'],
    ]
];

end:
echo json_encode($response);
?>