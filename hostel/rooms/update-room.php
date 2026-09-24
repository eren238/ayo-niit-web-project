<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$roomId       = trim($_POST['roomId']);
$hostelId     = trim($_POST['hostelId']);
$roomNumber   = trim($_POST['roomNumber']);
$block        = trim($_POST['block']);
$floor        = trim($_POST['floor']);
$roomCapacity = trim($_POST['roomCapacity']);
$statusId     = trim($_POST['statusId']); 

if ($roomId == '') {
    $response = [
        'success' => false,
        'message' => "ROOM ID IS REQUIRED"
    ];
    goto end;
}

if ($hostelId == '') {
    $response = [
        'success' => false,
        'message' => "HOSTEL ID IS REQUIRED"
    ];
    goto end;
}

if ($roomNumber == '') {
    $response = [
        'success' => false,
        'message' => "ROOM NUMBER IS REQUIRED"
    ];
    goto end;
}

if ($block == '') {
    $response = [
        'success' => false,
        'message' => "BLOCK IS REQUIRED"
    ];
    goto end;
}

if ($floor == '') {
    $response = [
        'success' => false,
        'message' => "FLOOR IS REQUIRED"
    ];
    goto end;
}

if ($roomCapacity == '' || !is_numeric($roomCapacity) || $roomCapacity <= 0) {
    $response = [
        'success' => false,
        'message' => "VALID ROOM CAPACITY IS REQUIRED"
    ];
    goto end;
}

// 1. CHECK IF ROOM EXISTS
$checkRoomQuery = mysqli_query($conn, "SELECT * FROM rooms_tab WHERE room_id = '$roomId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkRoomQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "ROOM NOT FOUND"
    ];
    goto end;
}

// 2. CHECK IF ROOM NUMBER IS ALREADY TAKEN IN THIS HOSTEL
$roomCheck = mysqli_query($conn, "SELECT * FROM rooms_tab 
    WHERE hostel_id = '$hostelId' AND room_number = '$roomNumber' AND room_id != '$roomId'") or die(mysqli_error($conn));
if (mysqli_num_rows($roomCheck) > 0) {
    $response = [
        'success' => false,
        'message' => "ROOM '$roomNumber' ALREADY EXISTS IN THIS HOSTEL"
    ];
    goto end;
}

// 3. UPDATE ROOM RECORD
mysqli_query($conn, "UPDATE `rooms_tab` SET 
    `hostel_id`     = '$hostelId',
    `room_number`   = '$roomNumber',
    `block`         = '$block',
    `floor`         = '$floor',
    `room_capacity` = '$roomCapacity',
    `status_id`     = '$statusId'
    WHERE `room_id` = '$roomId'") or die(mysqli_error($conn));

// 4. FETCH UPDATED RECORD
$fetchUpdated = mysqli_query($conn, "SELECT 
        rooms_tab.*, 
        hostels_tab.hostel_name, 
        status_tab.status_name 
    FROM rooms_tab, hostels_tab, status_tab 
    WHERE rooms_tab.hostel_id = hostels_tab.hostel_id 
      AND rooms_tab.status_id = status_tab.status_id 
      AND rooms_tab.room_id = '$roomId'") or die(mysqli_error($conn));

$data = mysqli_fetch_assoc($fetchUpdated);

$response = [
    'success' => true,
    'message' => "ROOM UPDATED SUCCESSFULLY",
    'data'    => $data
];

end:
echo json_encode($response);
?>