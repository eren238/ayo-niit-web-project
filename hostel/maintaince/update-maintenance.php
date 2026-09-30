<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
$maintenanceId = trim($_POST['maintenanceId']);
$roomId        = trim($_POST['roomId']);
$issue         = ($_POST['issue']);
$priority      = ($_POST['priority']);
$reportedDate  = trim($_POST['reportedDate']);
$statusId      = trim($_POST['statusId']);

if ($maintenanceId == '') {
    $response = [
        'success' => false,
        'message' => "MAINTENANCE ID IS REQUIRED"
    ];
    goto end;
}

if ($roomId == '') {
    $response = [
        'success' => false,
        'message' => "ROOM IS REQUIRED"
    ];
    goto end;
}

if ($issue == '') {
    $response = [
        'success' => false,
        'message' => "ISSUE DESCRIPTION IS REQUIRED"
    ];
    goto end;
}

if ($priority == '') {
    $response = [
        'success' => false,
        'message' => "PRIORITY IS REQUIRED"
    ];
    goto end;
}

if ($reportedDate == '') {
    $response = [
        'success' => false,
        'message' => "REPORTED DATE IS REQUIRED"
    ];
    goto end;
}

if ($statusId == '') {
    $response = [
        'success' => false,
        'message' => "STATUS ID IS REQUIRED"
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

mysqli_query($conn, "UPDATE `maintenance_tab` SET `room_id` = '$roomId', `issue` = '$issue', `priority` = '$priority', `reported_date` = '$reportedDate', `status_id` = '$statusId', `updated_at` = NOW() WHERE `maintenance_id` = '$maintenanceId'") or die(mysqli_error($conn));

$fetchUpdated = mysqli_query($conn, "SELECT maintenance_tab.*, rooms_tab.room_number, hostels_tab.hostel_name, status_tab.status_name, DATE_FORMAT(maintenance_tab.reported_date, '%d %b %Y') AS formatted_date FROM maintenance_tab, rooms_tab, hostels_tab, status_tab WHERE maintenance_tab.room_id = rooms_tab.room_id AND rooms_tab.hostel_id = hostels_tab.hostel_id AND maintenance_tab.status_id = status_tab.status_id AND maintenance_tab.maintenance_id = '$maintenanceId'") or die(mysqli_error($conn));
$maintenanceData = mysqli_fetch_assoc($fetchUpdated);

$response = [
    'success' => true,
    'message' => "MAINTENANCE REQUEST UPDATED SUCCESSFULLY",
    'data'    => $maintenanceData
];

end:
echo json_encode($response);
?>