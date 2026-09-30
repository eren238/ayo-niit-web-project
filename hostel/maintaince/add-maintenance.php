<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
$roomId       = trim($_POST['roomId']);
$issue        = ($_POST['issue']);
$priority     = ($_POST['priority']);
$reportedDate = trim($_POST['reportedDate']);
$statusId     = trim($_POST['statusId'] ?? 'IP');

if ($roomId == '') {
    $response = [
        'success' => false,
        'message' => "ROOM IS REQUIRED, Kindly select a room to continue"
    ];
    goto end;
}

if ($issue == '') {
    $response = [
        'success' => false,
        'message' => "ISSUE DESCRIPTION IS REQUIRED, Kindly enter the issue to continue"
    ];
    goto end;
}

if ($priority == '') {
    $response = [
        'success' => false,
        'message' => "PRIORITY IS REQUIRED, Kindly select priority level to continue"
    ];
    goto end;
}

if ($reportedDate == '') {
    $response = [
        'success' => false,
        'message' => "REPORTED DATE IS REQUIRED, Kindly select a date to continue"
    ];
    goto end;
}

$checkRoom = mysqli_query($conn, "SELECT * FROM rooms_tab WHERE room_id = '$roomId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkRoom) == 0) {
    $response = [
        'success' => false,
        'message' => "ROOM NOT FOUND"
    ];
    goto end;
}

$maintenanceId = '#MT' . date("Ymdhis");

mysqli_query($conn, "INSERT INTO `maintenance_tab` (`maintenance_id`, `room_id`, `issue`, `priority`, `reported_date`, `status_id`, `created_at`, `updated_at`) VALUES ('$maintenanceId', '$roomId', '$issue', '$priority', '$reportedDate', '$statusId', NOW(), NOW())") or die(mysqli_error($conn));

$createMaintenanceQuery = mysqli_query($conn, "SELECT maintenance_tab.*, rooms_tab.room_number, hostels_tab.hostel_name, status_tab.status_name, DATE_FORMAT(maintenance_tab.reported_date, '%d %b %Y') AS formatted_date FROM maintenance_tab, rooms_tab, hostels_tab, status_tab WHERE maintenance_tab.room_id = rooms_tab.room_id AND rooms_tab.hostel_id = hostels_tab.hostel_id AND maintenance_tab.status_id = status_tab.status_id AND maintenance_tab.maintenance_id = '$maintenanceId'") or die(mysqli_error($conn));
$maintenanceData = mysqli_fetch_assoc($createMaintenanceQuery);

$response = [
    'success' => true,
    'message' => "MAINTENANCE REQUEST CREATED SUCCESSFULLY",
    'data'    => [
        'maintenanceId' => $maintenanceData['maintenance_id'],
        'roomId'        => $maintenanceData['room_id'],
        'roomNumber'    => $maintenanceData['room_number'],
        'hostelName'    => $maintenanceData['hostel_name'],
        'issue'         => $maintenanceData['issue'],
        'priority'      => $maintenanceData['priority'],
        'reportedDate'  => $maintenanceData['reported_date'],
        'formattedDate' => $maintenanceData['formatted_date'],
        'statusId'      => $maintenanceData['status_id'],
        'statusName'    => $maintenanceData['status_name'],
        'createdAt'     => $maintenanceData['created_at'],
        'updatedAt'     => $maintenanceData['updated_at']
    ]
];

end:
echo json_encode($response);
?>