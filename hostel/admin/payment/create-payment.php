<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLERATION OF VARIABLE

$studentId = trim($_POST['studentId']);
$feeId = trim($_POST['feeId']);
$amount = trim($_POST['amount']);
$paymentMethod = trim($_POST['paymentMethod']);
$paymentDate = trim($_POST['paymentDate']);
$statusId = trim($_POST['statusId']);

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
        'message' => "FEE ID IS REQUIRED, Kindly select fee"
    ];
    goto end;
}

if ($amount == '') {
    $response = [
        'success' => false,
        'message' => "AMOUNT IS REQUIRED, Kindly enter payment amount"
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
        'message' => "STATUS ID IS REQUIRED, Kindly select payment status"
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

$paymentId = 'PAY' . date("Ymdhis");
$receiptNo = 'REC' . date("Ymdhis");

mysqli_query($conn, "INSERT INTO `payments_tab`
    (`payment_id`, `receipt_no`, `student_id`, `fee_id`, `amount`, `payment_method`, `payment_date`, `status_id`, `created_at`, `updated_at`) VALUES
    ('$paymentId', '$receiptNo', '$studentId', '$feeId', '$amount', '$paymentMethod', '$paymentDate', '$statusId', NOW(), NOW())") or die(mysqli_error($conn));

$createPaymentQuery = mysqli_query($conn, "SELECT payments_tab.*, student_tab.first_name, student_tab.last_name, status_tab.status_name FROM payments_tab LEFT JOIN student_tab ON payments_tab.student_id = student_tab.student_id LEFT JOIN status_tab ON payments_tab.status_id = status_tab.status_id WHERE payments_tab.payment_id = '$paymentId'") or die(mysqli_error($conn));
$paymentData = mysqli_fetch_assoc($createPaymentQuery);

$response = [
    'success' => true,
    'message' => "PAYMENT CREATED SUCCESSFUL",
    'data' => [
        'paymentId' => $paymentData['payment_id'],
        'receiptNo' => $paymentData['receipt_no'],
        'studentId' => $paymentData['student_id'],
        'firstName' => $paymentData['first_name'],
        'lastName' => $paymentData['last_name'],
        'feeId' => $paymentData['fee_id'],
        'amount' => $paymentData['amount'],
        'paymentMethod' => $paymentData['payment_method'],
        'paymentDate' => $paymentData['payment_date'],
        'statusName' => $paymentData['status_name'],
    ]
];

end:
echo json_encode($response);
?>