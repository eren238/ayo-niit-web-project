<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$feeId = trim($_POST['feeId']);

if ($feeId == '') {
    $response = [
        'success' => false,
        'message' => 'FEE ID REQUIRED'
    ];
    goto end;
}

$checkRecord = mysqli_query($conn, "SELECT * FROM fees_tab WHERE fee_id = '$feeId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkRecord) == 0) {
    $response = [
        'success' => false,
        'message' => 'FEE NOT FOUND'
    ];
    goto end;
}

mysqli_query($conn, "DELETE FROM `fees_tab` WHERE `fee_id` = '$feeId'") or die(mysqli_error($conn));

$response = [
    'success' => true,
    'message' => 'HOSTEL FEE DELETED SUCCESSFULLY'
];

end:
echo json_encode($response);
?>