<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE

$paymentId     = trim($_POST['paymentId']);
$studentId     = trim($_POST['studentId']);
$feeId         = trim($_POST['feeId']);
$amount        = trim($_POST['amount']);
$paymentMethod = trim($_POST['paymentMethod']);
$paymentDate   = trim($_POST['paymentDate']);
$statusId      = trim($_POST['statusId']);

if ($paymentId == '') {
    $response = [
        'success' => false,
        'message' => "PAYMENT ID IS REQUIRED, Kindly fill in the payment ID to continue"
    ];
    goto end;
}

if ($studentId == '') {
    $response = [
        'success' => false,
        'message' => "STUDENT IS REQUIRED, Kindly select or enter a student ID"
    ];
    goto end;
}

if ($feeId == '') {
    $response = [
        'success' => false,
        'message' => "FEE TYPE IS REQUIRED, Kindly select fee type"
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

if ($paymentMethod == '') {
    $response = [
        'success' => false,
        'message' => "PAYMENT METHOD IS REQUIRED, Kindly select payment method"
    ];
    goto end;
}

if ($paymentDate == '') {
    $response = [
        'success' => false,
        'message' => "PAYMENT DATE IS REQUIRED, Kindly select payment date"
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

$checkPayment = mysqli_query($conn, "SELECT * FROM payments_tab WHERE payment_id = '$paymentId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkPayment) == 0) {
    $response = [
        'success' => false,
        'message' => "PAYMENT RECORD NOT FOUND"
    ];
    goto end;
}

mysqli_query($conn, "UPDATE `payments_tab` SET `student_id` = '$studentId', `fee_id` = '$feeId', `amount` = '$amount', `payment_method` = '$paymentMethod', `payment_date` = '$paymentDate', `status_id` = '$statusId', `updated_at` = NOW() WHERE `payment_id` = '$paymentId'") or die(mysqli_error($conn));

$fetchUpdated = mysqli_query($conn, "SELECT payments_tab.*, student_tab.first_name, student_tab.last_name, fees_tab.fee_name, status_tab.status_name FROM payments_tab, student_tab, fees_tab, status_tab WHERE payments_tab.student_id = student_tab.student_id AND payments_tab.fee_id = fees_tab.fee_id AND payments_tab.status_id = status_tab.status_id AND payments_tab.payment_id = '$paymentId'") or die(mysqli_error($conn));
$paymentData = mysqli_fetch_assoc($fetchUpdated);

$response = [
    'success' => true,
    'message' => "PAYMENT UPDATED SUCCESSFULLY",
    'data'    => $paymentData
];

end:
echo json_encode($response);
?>
