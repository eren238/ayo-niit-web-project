<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$roomId = trim($_POST['roomId'] ?? '');

if ($roomId == '') {
    $response = [
        'success' => false,
        'message' => 'ROOM ID REQUIRED'
    ];
    goto end;
}

$query = mysqli_query($conn, "SELECT rooms_tab.*, hostels_tab.hostel_name, status_tab.status_name FROM rooms_tab, hostels_tab, status_tab  WHERE rooms_tab.hostel_id = hostels_tab.hostel_id AND rooms_tab.status_id = status_tab.status_id AND rooms_tab.room_id = '$roomId'") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => 'ROOM NOT FOUND'
    ];
    goto end;
}

$fetchData = mysqli_fetch_assoc($query);

$response = [
    'success' => true,
    'message' => "ROOM FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>