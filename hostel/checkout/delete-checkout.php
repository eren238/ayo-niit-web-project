<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$checkoutId = trim($_POST['checkoutId'] ?? '');

if ($checkoutId == '') {
    $response = [
        'success' => false,
        'message' => 'CHECKOUT ID REQUIRED'
    ];
    goto end;
}

$checkRecord = mysqli_query($conn, "SELECT * FROM checkouts_tab WHERE checkout_id = '$checkoutId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkRecord) == 0) {
    $response = [
        'success' => false,
        'message' => 'CHECKOUT RECORD NOT FOUND'
    ];
    goto end;
}

mysqli_query($conn, "DELETE FROM `checkouts_tab` WHERE `checkout_id` = '$checkoutId'") or die(mysqli_error($conn));

$response = [
    'success' => true,
    'message' => 'CHECKOUT RECORD DELETED SUCCESSFULLY'
];

end:
echo json_encode($response);
?>