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

$searchQuery = mysqli_query($conn, "SELECT complaints_tab.*, student_tab.first_name, student_tab.last_name, status_tab.status_name, DATE_FORMAT(complaints_tab.complaint_date, '%d %b %Y') AS formatted_date FROM complaints_tab, student_tab, status_tab WHERE complaints_tab.student_id = student_tab.student_id AND complaints_tab.status_id = status_tab.status_id AND (complaints_tab.complaint_id LIKE '%$searchContent%' OR complaints_tab.subject LIKE '%$searchContent%' OR student_tab.first_name LIKE '%$searchContent%' OR student_tab.last_name LIKE '%$searchContent%' OR complaints_tab.priority LIKE '%$searchContent%' OR status_tab.status_name LIKE '%$searchContent%') ORDER BY complaints_tab.created_at DESC") or die(mysqli_error($conn));

if (mysqli_num_rows($searchQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "NO COMPLAINTS FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($searchQuery, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "COMPLAINTS SEARCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>