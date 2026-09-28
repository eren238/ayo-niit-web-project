<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
$searchContent = trim($_POST['searchContent']);

if ($searchContent == '') {
    $response = [
        'success' => false,
        'message' => "SEARCH CONTENT IS REQUIRED, Kindly fill in the search content to continue"
    ];
    goto end;
}

$searchPaymentQuery = mysqli_query($conn, "SELECT payments_tab.*, student_tab.first_name, student_tab.last_name, fees_tab.fee_name, status_tab.status_name FROM payments_tab, student_tab, fees_tab, status_tab WHERE payments_tab.student_id = student_tab.student_id AND payments_tab.fee_id = fees_tab.fee_id AND payments_tab.status_id = status_tab.status_id AND (payments_tab.receipt_no LIKE '%$searchContent%' OR student_tab.first_name LIKE '%$searchContent%' OR student_tab.last_name LIKE '%$searchContent%' OR fees_tab.fee_name LIKE '%$searchContent%' OR payments_tab.amount LIKE '%$searchContent%' OR status_tab.status_name LIKE '%$searchContent%') ORDER BY payments_tab.created_at DESC") or die(mysqli_error($conn));

if (mysqli_num_rows($searchPaymentQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "NO PAYMENT FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($searchPaymentQuery, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "PAYMENT SEARCH SUCCESFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>