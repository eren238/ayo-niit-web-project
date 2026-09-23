<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php

$staffId = trim($_POST['staffId']);

if ($staffId == '') {
    $response = [
        'success' => false,
        'message' => 'STAFF ID REQUIRED'
    ];
    goto end;
}

$checkStaffQuery = mysqli_query($conn, "SELECT * FROM staff_tab WHERE staff_id = '$staffId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkStaffQuery) == 0) {
    $response = [
        'success' => false,
        'message' => 'STAFF NOT FOUND'
    ];
    goto end;
}

mysqli_query($conn, "DELETE FROM staff_tab WHERE staff_id = '$staffId'") or die(mysqli_error($conn));

$response = [
    'success' => true,
    'message' => 'STAFF DELETED SUCCESSFULLY'
];

end:
echo json_encode($response);
?>