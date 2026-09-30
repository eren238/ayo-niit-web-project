<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$complaintId = trim($_POST['complaintId']);

if ($complaintId == '') {
    $response = [
        'success' => false,
        'message' => "COMPLAINT ID IS REQUIRED"
    ];
    goto end;
}

$query = mysqli_query($conn, "SELECT complaints_tab.*, student_tab.first_name, student_tab.last_name, student_tab.phone_number, student_tab.email_address, status_tab.status_name, DATE_FORMAT(complaints_tab.complaint_date, '%d %b %Y') AS formatted_date FROM complaints_tab, student_tab, status_tab WHERE complaints_tab.student_id = student_tab.student_id AND complaints_tab.status_id = status_tab.status_id AND complaints_tab.complaint_id = '$complaintId'") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => "COMPLAINT NOT FOUND"
    ];
    goto end;
}

$complaintData = mysqli_fetch_assoc($query);

$response = [
    'success' => true,
    'message' => "COMPLAINT FETCHED SUCCESSFULLY",
    'data'    => $complaintData
];

end:
echo json_encode($response);
?>