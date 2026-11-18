<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$feeId    = trim($_POST['feeId']);
$feeName  = trim($_POST['feeName']);
$hostelId = trim($_POST['hostelId']);
$session  = trim($_POST['session']);
$amount   = trim($_POST['amount']);
$dueDate  = trim($_POST['dueDate']);
$statusId = trim($_POST['statusId']);

if ($feeId == '') {
    $response = [
        'success' => false,
        'message' => "FEE ID IS REQUIRED, Kindly fill in the fee ID to continue"
    ];
    goto end;
}

if ($feeName == '') {
    $response = [
        'success' => false,
        'message' => "FEE NAME IS REQUIRED, Kindly fill in the fee name to continue"
    ];
    goto end;
}

if ($hostelId == '') {
    $response = [
        'success' => false,
        'message' => "HOSTEL IS REQUIRED, Kindly select a hostel to continue"
    ];
    goto end;
}

if ($session == '') {
    $response = [
        'success' => false,
        'message' => "SESSION IS REQUIRED, Kindly fill in the session to continue"
    ];
    goto end;
}

if ($amount == '' || !is_numeric($amount) || $amount <= 0) {
    $response = [
        'success' => false,
        'message' => "VALID AMOUNT IS REQUIRED"
    ];
    goto end;
}

if ($dueDate == '') {
    $response = [
        'success' => false,
        'message' => "DUE DATE IS REQUIRED, Kindly select due date to continue"
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

$checkFee = mysqli_query($conn, "SELECT * FROM hostel_fees_tab WHERE fee_id = '$feeId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkFee) == 0) {
    $response = [
        'success' => false,
        'message' => "FEE RECORD NOT FOUND"
    ];
    goto end;
}

mysqli_query($conn, "UPDATE `hostel_fees_tab` SET 
    `fee_name`   = '$feeName',
    `hostel_id`  = '$hostelId',
    `session`    = '$session',
    `amount`     = '$amount',
    `due_date`   = '$dueDate',
    `status_id`  = '$statusId',
    `updated_at` = NOW()
    WHERE `fee_id` = '$feeId'") or die(mysqli_error($conn));

$fetchUpdated = mysqli_query($conn, "SELECT hostel_fees_tab.*, hostels_tab.hostel_name, status_tab.status_name FROM hostel_fees_tab LEFT JOIN hostels_tab ON hostel_fees_tab.hostel_id = hostels_tab.hostel_id LEFT JOIN status_tab ON hostel_fees_tab.status_id = status_tab.status_id WHERE hostel_fees_tab.fee_id = '$feeId'") or die(mysqli_error($conn));

$data = mysqli_fetch_assoc($fetchUpdated);

$response = [
    'success' => true,
    'message' => "HOSTEL FEE UPDATED SUCCESSFULLY",
    'data'    => $data
];

end:
echo json_encode($response);
?>
