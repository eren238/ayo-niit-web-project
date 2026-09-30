<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
$searchContent = trim($_POST['searchContent']);

if ($searchContent == '') {
    $response = [
        'success' => false,
        'message' => "SEARCH CONTENT IS REQUIRED, Kindly fill in the search content to continue"
    ];
    goto end;
}

$searchQuery = mysqli_query($conn, "SELECT maintenance_tab.*, rooms_tab.room_number, hostels_tab.hostel_name, status_tab.status_name, DATE_FORMAT(maintenance_tab.reported_date, '%d %b %Y') AS formatted_date FROM maintenance_tab, rooms_tab, hostels_tab, status_tab WHERE maintenance_tab.room_id = rooms_tab.room_id AND rooms_tab.hostel_id = hostels_tab.hostel_id AND maintenance_tab.status_id = status_tab.status_id AND (maintenance_tab.maintenance_id LIKE '%$searchContent%' OR rooms_tab.room_number LIKE '%$searchContent%' OR hostels_tab.hostel_name LIKE '%$searchContent%' OR maintenance_tab.issue LIKE '%$searchContent%' OR maintenance_tab.priority LIKE '%$searchContent%' OR status_tab.status_name LIKE '%$searchContent%') ORDER BY maintenance_tab.created_at DESC") or die(mysqli_error($conn));

if (mysqli_num_rows($searchQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "NO MAINTENANCE REQUESTS FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($searchQuery, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "MAINTENANCE SEARCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>