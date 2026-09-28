<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$searchContent = trim($_POST['searchContent'] ?? '');

if ($searchContent == '') {
    $response = [
        'success' => false,
        'message' => "SEARCH CONTENT IS REQUIRED"
    ];
    goto end;
}

$query = mysqli_query($conn, "SELECT 
        checkouts_tab.*, 
        CONCAT(student_tab.first_name, ' ', student_tab.last_name) AS student_name, 
        hostels_tab.hostel_name, 
        rooms_tab.room_number 
    FROM checkouts_tab, student_tab, hostels_tab, rooms_tab 
    WHERE checkouts_tab.student_id = student_tab.student_id 
      AND checkouts_tab.hostel_id = hostels_tab.hostel_id 
      AND checkouts_tab.room_id = rooms_tab.room_id 
      AND (
          student_tab.first_name LIKE '%$searchContent%' 
          OR student_tab.last_name LIKE '%$searchContent%' 
          OR checkouts_tab.student_id LIKE '%$searchContent%' 
          OR checkouts_tab.bed_id LIKE '%$searchContent%' 
          OR rooms_tab.room_number LIKE '%$searchContent%' 
          OR hostels_tab.hostel_name LIKE '%$searchContent%' 
          OR checkouts_tab.reason LIKE '%$searchContent%' 
          OR checkouts_tab.room_condition LIKE '%$searchContent%'
      ) 
    ORDER BY checkouts_tab.created_at DESC") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => "NO CHECKOUT RECORDS FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($query, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "CHECKOUT RECORDS SEARCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>