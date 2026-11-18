<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLERATION OF VARIABLE

$studentId = trim($_POST['studentId']);
$subject = trim($_POST['subject']);
$description = trim($_POST['description']);
$priority = trim($_POST['priority']);
$complaintDate = trim($_POST['complaintDate']);
$statusId = trim($_POST['statusId']);

if ($studentId == '') {
    $response = [
        'success' => false,
        'message' => "STUDENT IS REQUIRED, Kindly select a student to continue"
    ];
    goto end;
}

if ($subject == '') {
    $response = [
        'success' => false,
        'message' => "SUBJECT IS REQUIRED, Kindly enter complaint subject to continue"
    ];
    goto end;
}

if ($description == '') {
    $response = [
        'success' => false,
        'message' => "DESCRIPTION IS REQUIRED, Kindly enter description to continue"
    ];
    goto end;
}

if ($priority == '') {
    $response = [
        'success' => false,
        'message' => "PRIORITY IS REQUIRED, Kindly select priority level to continue"
    ];
    goto end;
}

if ($complaintDate == '') {
    $response = [
        'success' => false,
        'message' => "COMPLAINT DATE IS REQUIRED, Kindly select a date to continue"
    ];
    goto end;
}

if ($statusId == '') {
    $response = [
        'success' => false,
        'message' => "STATUS ID IS REQUIRED, Kindly select status to continue"
    ];
    goto end;
}

$checkStudent = mysqli_query($conn, "SELECT * FROM student_tab WHERE student_id = '$studentId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkStudent) == 0) {
    $response = [
        'success' => false,
        'message' => "STUDENT NOT FOUND"
    ];
    goto end;
}

$complaintId = 'CMP' . date("Ymdhis");

mysqli_query($conn, "INSERT INTO `complaints_tab`
    (`complaint_id`, `student_id`, `subject`, `description`, `priority`, `complaint_date`, `status_id`, `created_at`, `updated_at`) VALUES
    ('$complaintId', '$studentId', '$subject', '$description', '$priority', '$complaintDate', '$statusId', NOW(), NOW())") or die(mysqli_error($conn));

$createComplaintQuery = mysqli_query($conn, "SELECT complaints_tab.*, student_tab.first_name, student_tab.last_name, status_tab.status_name FROM complaints_tab LEFT JOIN student_tab ON complaints_tab.student_id = student_tab.student_id LEFT JOIN status_tab ON complaints_tab.status_id = status_tab.status_id WHERE complaints_tab.complaint_id = '$complaintId'") or die(mysqli_error($conn));
$complaintData = mysqli_fetch_assoc($createComplaintQuery);

$response = [
    'success' => true,
    'message' => "COMPLAINT CREATED SUCCESSFUL",
    'data' => [
        'complaintId' => $complaintData['complaint_id'],
        'studentId' => $complaintData['student_id'],
        'firstName' => $complaintData['first_name'],
        'lastName' => $complaintData['last_name'],
        'subject' => $complaintData['subject'],
        'description' => $complaintData['description'],
        'priority' => $complaintData['priority'],
        'complaintDate' => $complaintData['complaint_date'],
        'statusName' => $complaintData['status_name'],
    ]
];

end:
echo json_encode($response);
?>