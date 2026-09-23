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

$query = mysqli_query($conn, "SELECT  hostels_tab.*,  status_tab.status_name FROM hostels_tab, status_tab WHERE hostels_tab.status_id = status_tab.status_id  AND hostels_tab.hostel_id = '$hostelId'") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => 'HOSTEL NOT FOUND'
    ];
    goto end;
}

$fetchData = mysqli_fetch_assoc($query);

$response = [
    'success' => true,
    'message' => "HOSTEL FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>