<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php

$visitorId     = trim($_POST['visitorId'] ?? '');
$departureTime = trim($_POST['departureTime'] ?? '');

if ($visitorId == '') {
    $response = [
        'success' => false,
        'message' => "VISITOR ID IS REQUIRED"
    ];
    goto end;
}

if ($departureTime == '') {
    $response = [
        'success' => false,
        'message' => "DEPARTURE TIME IS REQUIRED"
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

mysqli_query($conn, "UPDATE `visitors_tab` SET `departure_time` = '$departureTime', `status_id` = 'OUT', `updated_at` = NOW() WHERE `visitor_id` = '$visitorId'") or die(mysqli_error($conn));

$fetchQuery = mysqli_query($conn, "SELECT visitors_tab.visitor_id, visitors_tab.visitor_name, visitors_tab.student_id, visitors_tab.purpose, visitors_tab.created_at, visitors_tab.updated_at, student_tab.first_name, student_tab.last_name, status_tab.status_name, DATE_FORMAT(visitors_tab.visit_date, '%d %M %Y') AS visitDate, TIME_FORMAT(visitors_tab.arrival_time, '%h:%i %p') AS arrivalTime, TIME_FORMAT(visitors_tab.departure_time, '%h:%i %p') AS departureTime FROM visitors_tab, student_tab, status_tab WHERE visitors_tab.student_id = student_tab.student_id AND visitors_tab.status_id = status_tab.status_id AND visitors_tab.visitor_id = '$visitorId'") or die(mysqli_error($conn));

$visitorData = mysqli_fetch_assoc($fetchQuery);

$response = [
    'success' => true,
    'message' => "VISITOR DEPARTURE RECORDED SUCCESSFULLY",
    'data'    => [
        'visitorId'     => $visitorData['visitor_id'],
        'visitorName'   => $visitorData['visitor_name'],
        'studentId'     => $visitorData['student_id'],
        'firstName'     => $visitorData['first_name'],
        'lastName'      => $visitorData['last_name'],
        'purpose'       => $visitorData['purpose'],
        'departureTime' => $visitorData['departureTime'],
        'statusName'    => $visitorData['status_name'],
        'createdAt'     => $visitorData['created_at'],
        'updatedAt'     => $visitorData['updated_at']
    ]
];
end:
echo json_encode($response);
?>