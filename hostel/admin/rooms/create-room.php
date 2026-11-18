<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLERATION OF VARIABLE

$hostelId = trim($_POST['hostelId']);
$roomNumber = trim($_POST['roomNumber']);
$block = trim($_POST['block']);
$floor = trim($_POST['floor']);
$roomCapacity = trim($_POST['roomCapacity']);
$statusId = trim($_POST['statusId']);

if ($hostelId == '') {
    $response = [
        'success' => false,
        'message' => "HOSTEL IS REQUIRED, Kindly select a hostel to continue"
    ];
    goto end;
}

if ($roomNumber == '') {
    $response = [
        'success' => false,
        'message' => "ROOM NUMBER IS REQUIRED, Kindly fill in the room number to continue"
    ];
    goto end;
}

if ($block == '') {
    $response = [
        'success' => false,
        'message' => "BLOCK IS REQUIRED, Kindly fill in the block to continue"
    ];
    goto end;
}

if ($floor == '') {
    $response = [
        'success' => false,
        'message' => "FLOOR IS REQUIRED, Kindly fill in the floor to continue"
    ];
    goto end;
}

if ($roomCapacity == '') {
    $response = [
        'success' => false,
        'message' => "ROOM CAPACITY IS REQUIRED, Kindly fill in the room capacity to continue"
    ];
    goto end;
}

if ($statusId == '') {
    $response = [
        'success' => false,
        'message' => "STATUS ID IS REQUIRED, Kindly fill in the status id to continue"
    ];
    goto end;
}

$roomcheck = mysqli_query($conn, "SELECT * FROM rooms_tab WHERE hostel_id = '$hostelId' AND room_number = '$roomNumber'") or die(mysqli_error($conn));
if (mysqli_num_rows($roomcheck) > 0) {
    $response = [
        "success" => false,
        "message" => "ROOM ALREADY EXIST Room '$roomNumber' already exists in this hostel"
    ];
    goto end;
}

$roomId = 'ROOM' . date("Ymdhis");

mysqli_query($conn, "INSERT INTO `rooms_tab`
    (`room_id`, `hostel_id`, `room_number`, `block`, `floor`, `room_capacity`, `status_id`, `created_at`, `updated_at`) VALUES
    ('$roomId', '$hostelId', '$roomNumber', '$block', '$floor', '$roomCapacity', '$statusId', NOW(), NOW())") or die(mysqli_error($conn));

$createRoomQuery = mysqli_query($conn, "SELECT rooms_tab.*, hostels_tab.hostel_name, status_tab.status_name FROM rooms_tab, hostels_tab, status_tab WHERE rooms_tab.hostel_id = hostels_tab.hostel_id AND rooms_tab.status_id = status_tab.status_id AND rooms_tab.room_id = '$roomId'") or die(mysqli_error($conn));
$roomData = mysqli_fetch_assoc($createRoomQuery);

$response = [
    'success' => true,
    'message' => "ROOM CREATED SUCCESSFUL",
    'data' => [
        'roomId' => $roomData['room_id'],
        'hostelId' => $roomData['hostel_id'],
        'hostelName' => $roomData['hostel_name'],
        'roomNumber' => $roomData['room_number'],
        'block' => $roomData['block'],
        'floor' => $roomData['floor'],
        'roomCapacity' => $roomData['room_capacity'],
        'statusName' => $roomData['status_name'],
    ]
];

end:
echo json_encode($response);
?>