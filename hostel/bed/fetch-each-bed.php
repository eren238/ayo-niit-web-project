<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$bedId = trim($_POST['bedId'] ?? '');

if ($bedId == '') {
    $response = [
        'success' => false,
        'message' => 'BED ID REQUIRED'
    ];
    goto end;
}

$query = mysqli_query($conn, "SELECT  beds_tab.*, rooms_tab.room_number,   hostels_tab.hostel_name, status_tab.status_name,COALESCE((SELECT CONCAT(first_name, ' ', last_name) FROM student_tab   WHERE student_tab.student_id = beds_tab.student_id),
'—'
        ) AS occupant_name
    FROM beds_tab, rooms_tab, hostels_tab, status_tab 
    WHERE beds_tab.room_id = rooms_tab.room_id 
      AND beds_tab.hostel_id = hostels_tab.hostel_id 
      AND beds_tab.status_id = status_tab.status_id 
      AND beds_tab.bed_id = '$bedId'") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => 'BED NOT FOUND'
    ];
    goto end;
}

$fetchData = mysqli_fetch_assoc($query);

$response = [
    'success' => true,
    'message' => "BED FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>