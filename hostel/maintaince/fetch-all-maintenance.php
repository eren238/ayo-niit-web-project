<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
$query = mysqli_query($conn, "SELECT maintenance_tab.*, rooms_tab.room_number, hostels_tab.hostel_name, status_tab.status_name, DATE_FORMAT(maintenance_tab.reported_date, '%d %b %Y') AS formatted_date FROM maintenance_tab, rooms_tab, hostels_tab, status_tab WHERE maintenance_tab.room_id = rooms_tab.room_id AND rooms_tab.hostel_id = hostels_tab.hostel_id AND maintenance_tab.status_id = status_tab.status_id ORDER BY maintenance_tab.created_at DESC") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => "NO MAINTENANCE REQUESTS FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($query, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "MAINTENANCE REQUESTS FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>