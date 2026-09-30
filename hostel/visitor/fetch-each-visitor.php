<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$visitorId = trim($_POST['visitorId']);

if ($visitorId == '') {
    $response = [
        'success' => false,
        'message' => "VISITOR ID IS REQUIRED"
    ];
    goto end;
}

$query = mysqli_query($conn, "SELECT visitors_tab.*, student_tab.first_name, student_tab.last_name, student_tab.phone_number, status_tab.status_name, TIME_FORMAT(visitors_tab.arrival_time, '%h:%i %p') AS arrival, IF(visitors_tab.departure_time IS NULL, '—', TIME_FORMAT(visitors_tab.departure_time, '%h:%i %p')) AS departure FROM visitors_tab, student_tab, status_tab WHERE visitors_tab.student_id = student_tab.student_id AND visitors_tab.status_id = status_tab.status_id AND visitors_tab.visitor_id = '$visitorId'") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => "VISITOR NOT FOUND"
    ];
    goto end;
}

$visitorData = mysqli_fetch_assoc($query);

$response = [
    'success' => true,
    'message' => "VISITOR FETCHED SUCCESSFULLY",
    'data'    => $visitorData
];

end:
echo json_encode($response);
?>