php


<?php require_once __DIR__ . '/../../config/connection.php'; ?>
<?php
$studentId = trim($_POST['studentId']);
if ($studentId == '') {
    $response = [
        'success' => false,
        'message' => 'STUDENT ID REQUIRED'
    ];
    goto end;
}
$studentQuery = mysqli_query($conn, "SELECT student_tab.*,  status_tab.status_name FROM student_tab, status_tab WHERE student_tab.status_id = status_tab.status_id AND student_tab.student_id = '$studentId'") or die(mysqli_error($conn));
if (mysqli_num_rows($studentQuery) == 0) {
    $response = [
        'success' => false,
        'message' => 'STUDENT NOT FOUND'
    ];
    goto end;
}
$studentData = mysqli_fetch_assoc($studentQuery);
$response = [
    'success' => true,
    'message' => "STUDENT FETCHED SUCCESSFULLY",
    'data'    => $studentData
];
end:
echo json_encode($response);
?>