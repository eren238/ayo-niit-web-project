<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$complaintId = trim($_POST['complaintId']);

if ($complaintId == '') {
    $response = [
        'success' => false,
        'message' => "COMPLAINT ID REQUIRED"
    ];
    goto end;
}


$checkRecord = mysqli_query($conn, "SELECT * FROM complaints_tab WHERE complaint_id = '$complaintId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkRecord) == 0) {
    $response = [
        'success' => false,
        'message' => "COMPLAINT RECORD NOT FOUND"
    ];
    goto end;
}


mysqli_query($conn, "DELETE FROM `complaints_tab` WHERE `complaint_id` = '$complaintId'") or die(mysqli_error($conn));

$response = [
    'success' => true,
    'message' => "COMPLAINT DELETED SUCCESSFULLY"
];

end:
echo json_encode($response);
?>