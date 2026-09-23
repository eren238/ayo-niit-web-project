<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php

$hostelId = trim($_POST['hostelId']);
$block    = trim($_POST['block']);
$floor    = trim($_POST['floor']);

// Base query
$sql = "SELECT  rooms_tab.*, hostels_tab.hostel_name FROM rooms_tab, hostels_tab WHERE rooms_tab.hostel_id = hostels_tab.hostel_id";

// Dynamic filters matching your UI dropdowns
if ($hostelId != '') {
    $sql .= " AND rooms_tab.hostel_id = '$hostelId'";
}
if ($block != '') {
    $sql .= " AND rooms_tab.block = '$block'";
}
if ($floor != '') {
    $sql .= " AND rooms_tab.floor = '$floor'";
}

$sql .= " ORDER BY rooms_tab.room_number ASC";

$fetchRoomsQuery = mysqli_query($conn, $sql) or die(mysqli_error($conn));

if (mysqli_num_rows($fetchRoomsQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "NO ROOMS FOUND"
    ];
    goto end;
}

$roomsData = mysqli_fetch_all($fetchRoomsQuery, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "ROOMS FETCHED SUCCESSFULLY",
    'data'    => $roomsData
];

end:
echo json_encode($response);
?>