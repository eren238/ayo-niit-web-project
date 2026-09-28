<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
$query = mysqli_query($conn, "SELECT payments_tab.*, student_tab.first_name, student_tab.last_name, fees_tab.fee_name, status_tab.status_name FROM payments_tab, student_tab, fees_tab, status_tab WHERE payments_tab.student_id = student_tab.student_id AND payments_tab.fee_id = fees_tab.fee_id AND payments_tab.status_id = status_tab.status_id ORDER BY payments_tab.created_at DESC") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => "NO PAYMENTS FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($query, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "PAYMENTS FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>