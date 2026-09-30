<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$visitorId     = trim($_POST['visitorId']);
$visitorName   = trim($_POST['visitorName']);
$studentId     = trim($_POST['studentId']);
$purpose       = trim($_POST['purpose']);
$visitDate     = trim($_POST['visitDate']);
$arrivalTime   = trim($_POST['arrivalTime']);
$departureTime = trim($_POST['departureTime']);
$statusId      = trim($_POST['statusId']);

if ($visitorId == '') {
    $response = [
        'success' => false,
        'message' => "VISITOR ID IS REQUIRED"
    ];
    goto end;
}

if ($visitorName == '') {
    $response = [
        'success' => false,
        'message' => "VISITOR NAME IS REQUIRED"
    ];
    goto end;
}

if ($studentId == '') {
    $response = [
        'success' => false,
        'message' => "STUDENT IS REQUIRED"
    ];
    goto end;
}

if ($purpose == '') {
    $response = [
        'success' => false,
        'message' => "PURPOSE IS REQUIRED"
    ];
    goto end;
}

if ($statusId == '') {
    $response = [
        'success' => false,
        'message' => "STATUS ID IS REQUIRED"
    ];
    goto end;
}


$checkRecord = mysqli_query($conn, "SELECT * FROM visitors_tab WHERE visitor_id = '$visitorId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkRecord) == 0) {
    $response = [
        'success' => false,
        'message' => "VISITOR RECORD NOT FOUND"
    ];
    goto end;
}


if ($departureTime != '') {
    mysqli_query($conn, "UPDATE `visitors_tab` SET `visitor_name` = '$visitorName', `student_id` = '$studentId', `purpose` = '$purpose', `visit_date` = '$visitDate', `arrival_time` = '$arrivalTime', `departure_time` = '$departureTime', `status_id` = '$statusId', `updated_at` = NOW() WHERE `visitor_id` = '$visitorId'") or die(mysqli_error($conn));
} else {
    mysqli_query($conn, "UPDATE `visitors_tab` SET `visitor_name` = '$visitorName', `student_id` = '$studentId', `purpose` = '$purpose', `visit_date` = '$visitDate', `arrival_time` = '$arrivalTime', `status_id` = '$statusId', `updated_at` = NOW() WHERE `visitor_id` = '$visitorId'") or die(mysqli_error($conn));
}


$fetchUpdated = mysqli_query($conn, "SELECT visitors_tab.*, student_tab.first_name, student_tab.last_name, status_tab.status_name, TIME_FORMAT(visitors_tab.arrival_time, '%h:%i %p') AS arrival, IF(visitors_tab.departure_time IS NULL, '—', TIME_FORMAT(visitors_tab.departure_time, '%h:%i %p')) AS departure FROM visitors_tab, student_tab, status_tab WHERE visitors_tab.student_id = student_tab.student_id AND visitors_tab.status_id = status_tab.status_id AND visitors_tab.visitor_id = '$visitorId'") or die(mysqli_error($conn));
$visitorData = mysqli_fetch_assoc($fetchUpdated);

$response = [
    'success' => true,
    'message' => "VISITOR RECORD UPDATED SUCCESSFULLY",
    'data'    => $visitorData
];

end:
echo json_encode($response);
?>