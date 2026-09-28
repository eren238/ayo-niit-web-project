<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$feeName  = trim($_POST['feeName']);
$hostelId = trim($_POST['hostelId']); 
$session  = trim($_POST['session']);      
$amount   = trim($_POST['amount']);       
$dueDate  = trim($_POST['dueDate']);     
$statusId = trim($_POST['statusId']);    

if ($feeName == '') {
    $response = [
        'success' => false,
        'message' => "FEE NAME IS REQUIRED (e.g. Accommodation, Maintenance)"
    ];
    goto end;
}

if ($hostelId == '') {
    $response = [
        'success' => false,
        'message' => "HOSTEL IS REQUIRED, Select a hostel or 'All Hostels'"
    ];
    goto end;
}

if ($session == '') {
    $response = [
        'success' => false,
        'message' => "ACADEMIC SESSION IS REQUIRED (e.g. 2026/2027)"
    ];
    goto end;
}

if ($amount == '' || !is_numeric($amount) || $amount <= 0) {
    $response = [
        'success' => false,
        'message' => "VALID FEE AMOUNT IS REQUIRED"
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

$checkFeeQuery = mysqli_query($conn, "SELECT * FROM fees_tab 
    WHERE fee_name = '$feeName' AND hostel_id = '$hostelId' AND session = '$session'") or die(mysqli_error($conn));

if (mysqli_num_rows($checkFeeQuery) > 0) {
    $response = [
        'success' => false,
        'message' => "FEE '$feeName' HAS ALREADY BEEN CONFIGURED FOR THIS HOSTEL AND SESSION"
    ];
    goto end;
}


$feeId = 'FEE' . date("YmdHis");


mysqli_query($conn, "INSERT INTO `fees_tab` 
    (`fee_id`, `fee_name`, `hostel_id`, `session`, `amount`, `due_date`, `status_id`, `created_at`, `updated_at`) VALUES 
    ('$feeId', '$feeName', '$hostelId', '$session', '$amount', '$dueDate', '$statusId', NOW(), NOW())") or die(mysqli_error($conn));


$fetchQuery = mysqli_query($conn, "SELECT 
        fees_tab.*,
        IF(fees_tab.hostel_id = 'ALL', 'All Hostels', (SELECT hostel_name FROM hostels_tab WHERE hostels_tab.hostel_id = fees_tab.hostel_id)) AS hostel_name,
        status_tab.status_name 
    FROM fees_tab, status_tab 
    WHERE fees_tab.status_id = status_tab.status_id 
      AND fees_tab.fee_id = '$feeId'") or die(mysqli_error($conn));

$data = mysqli_fetch_assoc($fetchQuery);

$response = [
    'success' => true,
    'message' => "HOSTEL FEE CREATED SUCCESSFULLY",
    'data'    => $data
];

end:
echo json_encode($response);
?>