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
        transfers_tab.*,
        CONCAT(student_tab.first_name, ' ', student_tab.last_name) AS student_name,
        new_h.hostel_name AS new_hostel_name,
        new_r.room_number AS new_room_number
    FROM transfers_tab, student_tab, hostels_tab AS new_h, rooms_tab AS new_r
    WHERE transfers_tab.student_id = student_tab.student_id
      AND transfers_tab.new_hostel_id = new_h.hostel_id
      AND transfers_tab.new_room_id = new_r.room_id
      AND (
          student_tab.first_name LIKE '%$searchContent%'
          OR student_tab.last_name LIKE '%$searchContent%'
          OR transfers_tab.student_id LIKE '%$searchContent%'
          OR transfers_tab.new_bed_id LIKE '%$searchContent%'
          OR transfers_tab.old_bed_id LIKE '%$searchContent%'
          OR new_h.hostel_name LIKE '%$searchContent%'
          OR transfers_tab.reason LIKE '%$searchContent%'
      )
    ORDER BY transfers_tab.created_at DESC") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => "NO TRANSFER RECORDS FOUND MATCHING '$searchContent'"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($query, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "TRANSFER RECORDS SEARCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>