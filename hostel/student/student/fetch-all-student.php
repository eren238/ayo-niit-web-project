<?php require_once __DIR__ . '/../../config/connection.php'; ?>
<?php
$fetchAllStudentQuery = mysqli_query($conn, "SELECT   student_tab.*, status_tab.status_name FROM student_tab, status_tab  WHERE student_tab.status_id = status_tab.status_id ORDER BY student_tab.created_at DESC") or die(mysqli_error($conn));
if (mysqli_num_rows($fetchAllStudentQuery) == 0) {
    $response = [
        'success' => false,
        'message' => 'NO STUDENT FOUND'
    ];
    goto end;
}
$fetchData = mysqli_fetch_all($fetchAllStudentQuery, MYSQLI_ASSOC);
$response = [
    'success' => true,
    'message' => "STUDENTS FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];
end:
echo json_encode($response);
?>