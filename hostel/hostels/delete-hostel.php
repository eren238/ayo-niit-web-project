<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$hostelId = trim($_POST['hostelId'] ?? '');

if ($hostelId == '') {
    $response = [
        'success' => false,
        'message' => 'HOSTEL ID REQUIRED'
    ];
    goto end;
}

$checkHostel = mysqli_query($conn, "SELECT * FROM hostels_tab WHERE hostel_id = '$hostelId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkHostel) == 0) {
    $response = [
        'success' => false,
        'message' => 'HOSTEL NOT FOUND'
    ];
    goto end;
}


$checkOccupied = mysqli_query($conn, "SELECT * FROM beds_tab WHERE hostel_id = '$hostelId' AND status_id = 'O'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkOccupied) > 0) {
    $response = [
        'success' => false,
        'message' => 'CANNOT DELETE HOSTEL: Students are currently occupying beds in this hostel!'
    ];
    goto end;
}

mysqli_query($conn, "DELETE FROM beds_tab WHERE hostel_id = '$hostelId'") or die(mysqli_error($conn));
mysqli_query($conn, "DELETE FROM rooms_tab WHERE hostel_id = '$hostelId'") or die(mysqli_error($conn));
mysqli_query($conn, "DELETE FROM hostels_tab WHERE hostel_id = '$hostelId'") or die(mysqli_error($conn));

$response = [
    'success' => true,
    'message' => 'HOSTEL AND ITS ROOMS DELETED SUCCESSFULLY'
];

end:
echo json_encode($response);
?>