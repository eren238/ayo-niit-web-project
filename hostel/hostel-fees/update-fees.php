<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$feeId    = trim($_POST['feeId']);
$feeName  = trim($_POST['feeName']);
$hostelId = trim($_POST['hostelId']);
$session  = trim($_POST['session']);
$amount   = trim($_POST['amount']);
$dueDate  = trim($_POST['dueDate']);
$statusId = trim($_POST['statusId'] ?? '1');

if ($feeId == '') {
    $response = [
        'success' => false,
        'message' => "FEE ID IS REQUIRED"
    ];
    goto end;
}

if ($feeName == '') {
    $response = [
        'success' => false,
        'message' => "FEE NAME IS REQUIRED"
    ];
    goto end;
}

if ($hostelId == '') {
    $response = [
        'success' => false,
        'message' => "HOSTEL IS REQUIRED"
    ];
    goto end;
}

if ($session == '') {
    $response = [
        'success' => false,
        'message' => "SESSION IS REQUIRED"
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
        'message' => "DUE DATE IS REQUIRED"
    ];
    goto end;
}

// 1. CHECK IF FEE EXISTS
$checkFee = mysqli_query($conn, "SELECT * FROM fees_tab WHERE fee_id = '$feeId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkFee) == 0) {
    $response = [
        'success' => false,
        'message' => "FEE NOT FOUND"
    ];
    goto end;
}

// 2. UPDATE RECORD
mysqli_query($conn, "UPDATE `fees_tab` SET 
    `fee_name`   = '$feeName',
    `hostel_id`  = '$hostelId',
    `session`    = '$session',
    `amount`     = '$amount',
    `due_date`   = '$dueDate',
    `status_id`  = '$statusId',
    `updated_at` = NOW() 
    WHERE `fee_id` = '$feeId'") or die(mysqli_error($conn));

// 3. FETCH UPDATED RECORD
$fetchUpdated = mysqli_query($conn, "SELECT 
        fees_tab.*, 
        hostels_tab.hostel_name, 
        status_tab.status_name 
    FROM fees_tab, hostels_tab, status_tab 
    WHERE fees_tab.hostel_id = hostels_tab.hostel_id 
      AND fees_tab.status_id = status_tab.status_id 
      AND fees_tab.fee_id = '$feeId'") or die(mysqli_error($conn));

$data = mysqli_fetch_assoc($fetchUpdated);

$response = [
    'success' => true,
    'message' => "FEE UPDATED SUCCESSFULLY",
    'data'    => $data
];

end:
echo json_encode($response);
?>