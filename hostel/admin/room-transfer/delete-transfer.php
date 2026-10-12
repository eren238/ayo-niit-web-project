<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php

$transferId = trim($_POST['transferId'] ?? '');

if ($transferId == '')
     {
    $response = ['success' => false, 'message' => "TRANSFER ID IS REQUIRED"];
    goto end;
}

$checkQuery = mysqli_query($conn, "SELECT * FROM transfers_tab WHERE transfer_id = '$transferId'") or die(mysqli_error($conn));

if (mysqli_num_rows($checkQuery) == 0) {
    $response = ['success' => false, 'message' => "TRANSFER RECORD NOT FOUND"];
    goto end;
}

mysqli_query($conn, "DELETE FROM `transfers_tab` WHERE `transfer_id` = '$transferId'") or die(mysqli_error($conn));

$response = [
    'success' => true,
    'message' => "TRANSFER RECORD DELETED SUCCESSFULLY"
];

end:
echo json_encode($response);
?>