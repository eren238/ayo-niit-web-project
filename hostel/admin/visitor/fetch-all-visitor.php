<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php

$query = mysqli_query($conn, "SELECT visitors_tab.visitor_id, visitors_tab.visitor_name, visitors_tab.student_id, visitors_tab.purpose, visitors_tab.departure_time, visitors_tab.created_at, visitors_tab.updated_at, student_tab.first_name, student_tab.last_name, status_tab.status_name, DATE_FORMAT(visitors_tab.visit_date, '%d %M %Y') AS visitDate, TIME_FORMAT(visitors_tab.arrival_time, '%h:%i %p') AS arrivalTime, TIME_FORMAT(visitors_tab.departure_time, '%h:%i %p') AS departureTime FROM visitors_tab, student_tab, status_tab WHERE visitors_tab.student_id = student_tab.student_id AND visitors_tab.status_id = status_tab.status_id ORDER BY visitors_tab.created_at DESC") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => "NO VISITOR RECORDS FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($query, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "VISITORS FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?> 