<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$bedId     = trim($_POST['bedId']);
$bedNumber = trim($_POST['bedNumber']);
$statusId  = trim($_POST['statusId']); 

if ($bedId == '') {
    $response = [
        'success' => false,
        'message' => "BED ID IS REQUIRED"
    ];
    goto end;
}

if ($bedNumber == '') {
    $response = [
        'success' => false,
        'message' => "BED NUMBER IS REQUIRED"
    ];
    goto end;
}

if ($statusId == '') {
    $response = [
        'success' => false,
        'message' => "STATUS ID IS REQUIRED"
    ];
    goto end;
}


$checkBedQuery = mysqli_query($conn, "SELECT * FROM beds_tab WHERE bed_id = '$bedId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkBedQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "BED NOT FOUND"
    ];
    goto end;
}


mysqli_query($conn, "UPDATE `beds_tab` SET  `bed_number` = '$bedNumber',`status_id`  = '$statusId' WHERE `bed_id` = '$bedId'") or die(mysqli_error($conn));


$fetchUpdated = mysqli_query($conn, "SELECT beds_tab.*, rooms_tab.room_number, hostels_tab.hostel_name, status_tab.status_name FROM beds_tab, rooms_tab, hostels_tab, status_tab WHERE beds_tab.room_id = rooms_tab.room_id AND beds_tab.hostel_id = hostels_tab.hostel_id AND beds_tab.status_id = status_tab.status_id   AND beds_tab.bed_id = '$bedId'") or die(mysqli_error($conn));

$data = mysqli_fetch_assoc($fetchUpdated);
$response = [
    'success' => true,
    'message' => "BED UPDATED SUCCESSFULLY",
    'data'    => $data
];
end:
echo json_encode($response);
?>