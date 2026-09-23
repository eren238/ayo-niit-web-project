<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$allocationId = trim($_POST['allocationId']);

if ($allocationId == '') {
    $response = [
        'success' => false,
        'message' => 'ALLOCATION ID REQUIRED'
    ];
    goto end;
}

$query = mysqli_query($conn, "SELECT allocation_tab.*,CONCAT(student_tab.first_name, ' ', student_tab.last_name) AS student_name,  student_tab.email_address, student_tab.phone_number,  hostels_tab.hostel_name, rooms_tab.room_number,  status_tab.status_name FROM allocation_tab, student_tab, hostels_tab, rooms_tab, status_tab  WHERE allocation_tab.student_id = student_tab.student_id AND allocation_tab.hostel_id = hostels_tab.hostel_id  AND allocation_tab.room_id = rooms_tab.room_id AND allocation_tab.status_id = status_tab.status_id AND allocation_tab.allocation_id = '$allocationId'") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => 'ALLOCATION NOT FOUND'
    ];
    goto end;
}

$fetchData = mysqli_fetch_assoc($query);

$response = [
    'success' => true,
    'message' => "ALLOCATION FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>