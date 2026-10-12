
<?php require_once __DIR__ . '/../../config/connection.php'; ?>
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
$searchStudentQuery = mysqli_query($conn, "SELECT   student_tab.*,  status_tab.status_name  FROM student_tab, status_tab WHERE student_tab.status_id = status_tab.status_id AND (student_tab.first_name LIKE '%$searchContent%' OR student_tab.last_name LIKE '%$searchContent%'  OR student_tab.email_address LIKE '%$searchContent%'   OR student_tab.student_id LIKE '%$searchContent%'  OR student_tab.phone_number LIKE '%$searchContent%'  OR student_tab.department LIKE '%$searchContent%' )") or die(mysqli_error($conn));
if (mysqli_num_rows($searchStudentQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "NO STUDENT FOUND"
    ];
    goto end;
}
$fetchData = mysqli_fetch_all($searchStudentQuery, MYSQLI_ASSOC);
$response = [
    'success' => true,
    'message' => "STUDENT SEARCH SUCCESFULLY",
    'data'    => $fetchData
];
end:
echo json_encode($response);
?>