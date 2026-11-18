<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLERATION OF VARIABLE

$feeName = trim($_POST['feeName']);
$hostelId = trim($_POST['hostelId']);
$session = trim($_POST['session']);
$amount = trim($_POST['amount']);
$dueDate = trim($_POST['dueDate']);
$statusId = trim($_POST['statusId']);

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
        'message' => "HOSTEL ID IS REQUIRED, Kindly select a hostel to continue"
    ];
    goto end;
}

if ($session == '') {
    $response = [
        'success' => false,
        'message' => "SESSION IS REQUIRED, Kindly fill in session to continue"
    ];
    goto end;
}

if ($amount == '') {
    $response = [
        'success' => false,
        'message' => "AMOUNT IS REQUIRED, Kindly fill in amount to continue"
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
        'message' => "STATUS ID IS REQUIRED, Kindly fill in status id to continue"
    ];
    goto end;
}

$checkFeeQuery = mysqli_query($conn, "SELECT * FROM fees_tab WHERE fee_name = '$feeName' AND hostel_id = '$hostelId' AND session = '$session'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkFeeQuery) > 0) {
    $response = [
        'success' => false,
        'message' => "FEE ALREADY EXIST FOR THIS HOSTEL AND SESSION"
    ];
    goto end;
}

$feeId = 'FEE' . date("Ymdhis");

mysqli_query($conn, "INSERT INTO `fees_tab`
    (`fee_id`, `fee_name`, `hostel_id`, `session`, `amount`, `due_date`, `status_id`, `created_at`, `updated_at`) VALUES
    ('$feeId', '$feeName', '$hostelId', '$session', '$amount', '$dueDate', '$statusId', NOW(), NOW())") or die(mysqli_error($conn));

$createFeeQuery = mysqli_query($conn, "SELECT fees_tab.*, status_tab.status_name FROM fees_tab LEFT JOIN status_tab ON fees_tab.status_id = status_tab.status_id WHERE fees_tab.fee_id = '$feeId'") or die(mysqli_error($conn));
$feeData = mysqli_fetch_assoc($createFeeQuery);

$response = [
    'success' => true,
    'message' => "FEE CREATED SUCCESSFUL",
    'data' => [
        'feeId' => $feeData['fee_id'],
        'feeName' => $feeData['fee_name'],
        'hostelId' => $feeData['hostel_id'],
        'session' => $feeData['session'],
        'amount' => $feeData['amount'],
        'dueDate' => $feeData['due_date'],
        'statusName' => $feeData['status_name'],
    ]
];

end:
echo json_encode($response);
?>