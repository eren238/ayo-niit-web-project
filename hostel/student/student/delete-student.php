<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
$studentId = trim($_POST['studentId'] ?? '');
if ($studentId == '') {
    $response = [
        'success' => false,
        'message' => 'STUDENT ID REQUIRED'
    ];
    goto end;
}
$checkStudentQuery = mysqli_query($conn, "SELECT * FROM student_tab WHERE student_id = '$studentId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkStudentQuery) == 0) {
    $response = [
        'success' => false,
        'message' => 'STUDENT NOT FOUND'
    ];
    goto end;
}
mysqli_query($conn, "DELETE FROM student_tab WHERE student_id = '$studentId'") or die(mysqli_error($conn));
$response = [
    'success' => true,
    'message' => 'STUDENT DELETED SUCCESSFULLY'
];
end:
echo json_encode($response);
?>
