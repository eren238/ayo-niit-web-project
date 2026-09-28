<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$hostelId = trim($_POST['hostelId'] ?? '');
$block    = trim($_POST['block'] ?? '');

$sql = "SELECT  rooms_tab.room_id, rooms_tab.room_number, rooms_tab.block,   rooms_tab.floor, rooms_tab.room_capacity, hostels_tab.hostel_id, hostels_tab.hostel_name  FROM rooms_tab, hostels_tab WHERE rooms_tab.hostel_id = hostels_tab.hostel_id  AND rooms_tab.status_id = '1'";

if ($hostelId != '') {
    $sql .= " AND rooms_tab.hostel_id = '$hostelId'";
}

// Optional filter: If user already picked a block
if ($block != '') {
    $sql .= " AND rooms_tab.block = '$block'";
}

$sql .= " ORDER BY hostels_tab.hostel_name, rooms_tab.room_number ASC";

$query = mysqli_query($conn, $sql) or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => "NO ROOMS FOUND",
        'data'    => []
    ];
    goto end;
}

$roomsData = mysqli_fetch_all($query, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "ROOMS LOADED FOR DROPDOWN",
    'data'    => $roomsData
];

end:
echo json_encode($response);
?>