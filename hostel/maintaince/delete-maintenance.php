<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
$maintenanceId = trim($_POST['maintenanceId']);

if ($maintenanceId == '') {
    $response = [
        'success' => false,
        'message' => "MAINTENANCE ID REQUIRED"
    ];
    goto end;
}

$checkRecord = mysqli_query($conn, "SELECT * FROM maintenance_tab WHERE maintenance_id = '$maintenanceId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkRecord) == 0) {
    $response = [
        'success' => false,
        'message' => "MAINTENANCE RECORD NOT FOUND"
    ];
    goto end;
}

mysqli_query($conn, "DELETE FROM `maintenance_tab` WHERE `maintenance_id` = '$maintenanceId'") or die(mysqli_error($conn));

$response = [
    'success' => true,
    'message' => "MAINTENANCE REQUEST DELETED SUCCESSFULLY"
];

end:
echo json_encode($response);
?>