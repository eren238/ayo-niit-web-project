<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$hostelId  = trim($_POST['hostelId']);
$roomId    = trim($_POST['roomId']);
$bedNumber = trim($_POST['bedNumber']);
$statusId  = trim($_POST['statusId'] ?? 'D');

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

if ($bedNumber == '' || $bedNumber == '00') {
    $response = [
        'success' => false,
        'message' => "BED NUMBER IS REQUIRED, Kindly enter bed number (e.g. 01, 02)"
    ];
    goto end;
}


$detailsQuery = mysqli_query($conn, "SELECT hostels_tab.code, rooms_tab.room_number FROM hostels_tab, rooms_tab WHERE hostels_tab.hostel_id = '$hostelId' AND rooms_tab.room_id = '$roomId'") or die(mysqli_error($conn));

if (mysqli_num_rows($detailsQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "INVALID HOSTEL OR ROOM SELECTED"
    ];
    goto end;
}

$details = mysqli_fetch_assoc($detailsQuery);
$bedId = $details['code'] . '-' . $details['room_number'] . '-' . $bedNumber;


$checkBedQuery = mysqli_query($conn, "SELECT * FROM beds_tab WHERE bed_id = '$bedId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkBedQuery) > 0) {
    $response = [
        'success' => false,
        'message' => "BED ALREADY EXISTS! Bed '$bedId' already exists."
    ];
    goto end;
}

mysqli_query($conn, "INSERT INTO `beds_tab`
    (`bed_id`, `hostel_id`, `room_id`, `bed_number`, `status_id`, `created_at`, `updated_at`) VALUES
    ('$bedId', '$hostelId', '$roomId', '$bedNumber', '$statusId', NOW(), NOW())") or die(mysqli_error($conn));


$fetchBedQuery = mysqli_query($conn, "SELECT  beds_tab.*, hostels_tab.hostel_name, rooms_tab.room_number, status_tab.status_name  FROM beds_tab, hostels_tab, rooms_tab, status_tab WHERE beds_tab.hostel_id = hostels_tab.hostel_id  AND beds_tab.room_id = rooms_tab.room_id AND beds_tab.status_id = status_tab.status_id  AND beds_tab.bed_id = '$bedId'") or die(mysqli_error($conn));

$bedData = mysqli_fetch_assoc($fetchBedQuery);

$response = [
    'success' => true,
    'message' => "BED CREATED SUCCESSFULLY",
    'data'    => [
        'bedId'      => $bedData['bed_id'],
        'roomNumber' => $bedData['room_number'],
        'hostelName' => $bedData['hostel_name'],
        'bedNumber'  => $bedData['bed_number'],
        'statusId'   => $bedData['status_id'],
        'statusName' => $bedData['status_name'], 
        'occupant'   => '—',
        'createdAt'  => $bedData['created_at'],
        'updatedAt'  => $bedData['updated_at']
    ]
];

end:
echo json_encode($response);
?>