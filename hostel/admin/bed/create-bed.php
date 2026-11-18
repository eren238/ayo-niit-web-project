<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLERATION OF VARIABLE

$hostelId = trim($_POST['hostelId']);
$roomId = trim($_POST['roomId']);
$bedNumber = trim($_POST['bedNumber']);
$statusId = trim($_POST['statusId']);

if ($hostelId == '') {
    $response = [
        'success' => false,
        'message' => "HOSTEL IS REQUIRED, Kindly select hostel to continue"
    ];
    goto end;
}

if ($roomId == '') {
    $response = [
        'success' => false,
        'message' => "ROOM IS REQUIRED, Kindly select room to continue"
    ];
    goto end;
}

if ($bedNumber == '') {
    $response = [
        'success' => false,
        'message' => "BED NUMBER IS REQUIRED, Kindly fill in the bed number to continue"
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

$roomCheck = mysqli_query($conn, "SELECT rooms_tab.*, hostels_tab.hostel_name FROM rooms_tab, hostels_tab WHERE rooms_tab.hostel_id = hostels_tab.hostel_id AND rooms_tab.hostel_id = '$hostelId' AND (rooms_tab.room_id = '$roomId' OR rooms_tab.room_number = '$roomId')") or die(mysqli_error($conn));
if (mysqli_num_rows($roomCheck) == 0) {
    $response = [
        'success' => false,
        'message' => "INVALID HOSTEL OR ROOM SELECTED"
    ];
    goto end;
}
$roomRow = mysqli_fetch_assoc($roomCheck);
$realRoomId = $roomRow['room_id'];

$bedcheck = mysqli_query($conn, "SELECT * FROM beds_tab WHERE hostel_id = '$hostelId' AND room_id = '$realRoomId' AND bed_number = '$bedNumber'") or die(mysqli_error($conn));
if (mysqli_num_rows($bedcheck) > 0) {
    $response = [
        "success" => false,
        "message" => "BED ALREADY EXIST Bed '$bedNumber' already exists in this room"
    ];
    goto end;
}

$bedId = date("YmdHis");

mysqli_query($conn, "INSERT INTO `beds_tab`
    (`bed_id`, `hostel_id`, `room_id`, `bed_number`, `status_id`, `created_at`, `updated_at`) VALUES
    ('$bedId', '$hostelId', '$realRoomId', '$bedNumber', '$statusId', NOW(), NOW())") or die(mysqli_error($conn));

$createBedQuery = mysqli_query($conn, "SELECT beds_tab.*, hostels_tab.hostel_name, rooms_tab.room_number, status_tab.status_name FROM beds_tab, hostels_tab, rooms_tab, status_tab WHERE beds_tab.hostel_id = hostels_tab.hostel_id AND beds_tab.room_id = rooms_tab.room_id AND beds_tab.status_id = status_tab.status_id AND beds_tab.bed_id = '$bedId'") or die(mysqli_error($conn));
$bedData = mysqli_fetch_assoc($createBedQuery);

$response = [
    'success' => true,
    'message' => "BED CREATED SUCCESSFUL",
    'data' => [
        'bedId' => $bedData['bed_id'],
        'hostelId' => $bedData['hostel_id'],
        'hostelName' => $bedData['hostel_name'],
        'roomId' => $bedData['room_id'],
        'roomNumber' => $bedData['room_number'],
        'bedNumber' => $bedData['bed_number'],
        'statusName' => $bedData['status_name'],
    ]
];

end:
echo json_encode($response);
?>