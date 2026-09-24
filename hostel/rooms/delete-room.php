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


$checkRoom = mysqli_query($conn, "SELECT * FROM rooms_tab WHERE room_id = '$roomId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkRoom) == 0) {
    $response = [
        'success' => false,
        'message' => 'ROOM NOT FOUND'
    ];
    goto end;
}

$checkOccupied = mysqli_query($conn, "SELECT * FROM beds_tab WHERE room_id = '$roomId' AND status_id = 'O'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkOccupied) > 0) {
    $response = [
        'success' => false,
        'message' => 'CANNOT DELETE ROOM: There are students occupying beds in this room!'
    ];
    goto end;
}

mysqli_query($conn, "DELETE FROM beds_tab WHERE room_id = '$roomId'") or die(mysqli_error($conn));
mysqli_query($conn, "DELETE FROM rooms_tab WHERE room_id = '$roomId'") or die(mysqli_error($conn));

$response = [
    'success' => true,
    'message' => 'ROOM AND ITS BEDS DELETED SUCCESSFULLY'
];

end:
echo json_encode($response);
?>