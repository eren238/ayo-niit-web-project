<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
$maintenanceId = trim($_POST['maintenanceId']);

if ($maintenanceId == '') {
    $response = [
        'success' => false,
        'message' => "MAINTENANCE ID IS REQUIRED"
    ];
    goto end;
}

$query = mysqli_query($conn, "SELECT maintenance_tab.*, rooms_tab.room_number, hostels_tab.hostel_name, status_tab.status_name, DATE_FORMAT(maintenance_tab.reported_date, '%d %b %Y') AS formatted_date FROM maintenance_tab, rooms_tab, hostels_tab, status_tab WHERE maintenance_tab.room_id = rooms_tab.room_id AND rooms_tab.hostel_id = hostels_tab.hostel_id AND maintenance_tab.status_id = status_tab.status_id AND maintenance_tab.maintenance_id = '$maintenanceId'") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => "MAINTENANCE REQUEST NOT FOUND"
    ];
    goto end;
}

$maintenanceData = mysqli_fetch_assoc($query);

$response = [
    'success' => true,
    'message' => "MAINTENANCE REQUEST FETCHED SUCCESSFULLY",
    'data'    => $maintenanceData
];

end:
echo json_encode($response);
?>