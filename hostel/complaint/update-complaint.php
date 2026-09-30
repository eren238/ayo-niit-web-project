<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$complaintId   = trim($_POST['complaintId']);
$studentId     = trim($_POST['studentId']);
$subject       = ($_POST['subject']);
$description   = ($_POST['description']);
$priority      = ($_POST['priority']);
$complaintDate = trim($_POST['complaintDate']);
$statusId      = trim($_POST['statusId']);

if ($complaintId == '') {
    $response = [
        'success' => false,
        'message' => "COMPLAINT ID IS REQUIRED"
    ];
    goto end;
}

if ($studentId == '') {
    $response = [
        'success' => false,
        'message' => "STUDENT IS REQUIRED"
    ];
    goto end;
}

if ($subject == '') {
    $response = [
        'success' => false,
        'message' => "SUBJECT IS REQUIRED"
    ];
    goto end;
}

if ($priority == '') {
    $response = [
        'success' => false,
        'message' => "PRIORITY IS REQUIRED"
    ];
    goto end;
}

if ($complaintDate == '') {
    $response = [
        'success' => false,
        'message' => "COMPLAINT DATE IS REQUIRED"
    ];
    goto end;
}

if ($statusId == '') {
    $response = [
        'success' => false,
        'message' => "STATUS ID IS REQUIRED"
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


mysqli_query($conn, "UPDATE `complaints_tab` SET `student_id` = '$studentId', `subject` = '$subject', `description` = '$description', `priority` = '$priority', `complaint_date` = '$complaintDate', `status_id` = '$statusId', `updated_at` = NOW() WHERE `complaint_id` = '$complaintId'") or die(mysqli_error($conn));


$fetchUpdated = mysqli_query($conn, "SELECT complaints_tab.*, student_tab.first_name, student_tab.last_name, status_tab.status_name, DATE_FORMAT(complaints_tab.complaint_date, '%d %b %Y') AS formatted_date FROM complaints_tab, student_tab, status_tab WHERE complaints_tab.student_id = student_tab.student_id AND complaints_tab.status_id = status_tab.status_id AND complaints_tab.complaint_id = '$complaintId'") or die(mysqli_error($conn));
$complaintData = mysqli_fetch_assoc($fetchUpdated);

$response = [
    'success' => true,
    'message' => "COMPLAINT UPDATED SUCCESSFULLY",
    'data'    => $complaintData
];

end:
echo json_encode($response);
?>