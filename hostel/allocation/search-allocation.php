<?php require_once __DIR__ . '/../config/connection.php';?>

<?php
$searchContent = trim($_POST['searchContent']);

if ($searchContent == '') {
    $response = [
        'success' => false,
        'message' => "SEARCH CONTENT IS REQUIRED"
    ];
    goto end;
}

$query = mysqli_query($conn, "SELECT allocation_tab.*, CONCAT(student_tab.first_name, ' ', student_tab.last_name) AS student_name, student_tab.email_address, hostels_tab.hostel_name, rooms_tab.room_number, status_tab.status_name FROM allocation_tab, student_tab, hostels_tab, rooms_tab, status_tab WHERE allocation_tab.student_id = student_tab.student_id AND allocation_tab.hostel_id = hostels_tab.hostel_id AND allocation_tab.room_id = rooms_tab.room_id AND allocation_tab.status_id = status_tab.status_id AND (student_tab.first_name LIKE '%$searchContent%' OR student_tab.last_name LIKE '%$searchContent%' OR allocation_tab.student_id LIKE '%$searchContent%' OR allocation_tab.bed_id LIKE '%$searchContent%' OR rooms_tab.room_number LIKE '%$searchContent%' OR hostels_tab.hostel_name LIKE '%$searchContent%' OR status_tab.status_name LIKE '%$searchContent%') ORDER BY allocation_tab.created_at DESC") or die(mysqli_error($conn));
if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => "NO ALLOCATIONS FOUND MATCHING '$searchContent'"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($query, MYSQLI_ASSOC);
$response = [
    'success' => true,
    'message' => "ALLOCATIONS SEARCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>