<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
$paymentId = trim($_POST['paymentId'] ?? '');

if ($paymentId == '') {
    $response = [
        'success' => false,
        'message' => 'PAYMENT ID REQUIRED'
    ];
    goto end;
}

$query = mysqli_query($conn, "SELECT payments_tab.*, student_tab.first_name, student_tab.last_name, student_tab.email_address, student_tab.phone_number, fees_tab.fee_name, fees_tab.session, status_tab.status_name FROM payments_tab, student_tab, fees_tab, status_tab WHERE payments_tab.student_id = student_tab.student_id AND payments_tab.fee_id = fees_tab.fee_id AND payments_tab.status_id = status_tab.status_id AND payments_tab.payment_id = '$paymentId'") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => 'PAYMENT NOT FOUND'
    ];
    goto end;
}

$fetchData = mysqli_fetch_assoc($query);

$response = [
    'success' => true,
    'message' => "PAYMENT DETAILS FETCHED",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>