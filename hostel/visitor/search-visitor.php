<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$searchContent = trim($_POST['searchContent']);

if ($searchContent == '') {
    $response = [
        'success' => false,
        'message' => "SEARCH CONTENT IS REQUIRED, Kindly fill in the search content to continue"
    ];
    goto end;
}

$searchQuery = mysqli_query($conn, "SELECT visitors_tab.*, student_tab.first_name, student_tab.last_name, status_tab.status_name, TIME_FORMAT(visitors_tab.arrival_time, '%h:%i %p') AS arrival, IF(visitors_tab.departure_time IS NULL, '—', TIME_FORMAT(visitors_tab.departure_time, '%h:%i %p')) AS departure FROM visitors_tab, student_tab, status_tab WHERE visitors_tab.student_id = student_tab.student_id AND visitors_tab.status_id = status_tab.status_id AND (visitors_tab.visitor_name LIKE '%$searchContent%' OR student_tab.first_name LIKE '%$searchContent%' OR student_tab.last_name LIKE '%$searchContent%' OR visitors_tab.purpose LIKE '%$searchContent%' OR status_tab.status_name LIKE '%$searchContent%') ORDER BY visitors_tab.created_at DESC") or die(mysqli_error($conn));

if (mysqli_num_rows($searchQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "NO VISITOR RECORDS FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($searchQuery, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "VISITORS SEARCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>