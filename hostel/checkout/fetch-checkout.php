<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php

$query = mysqli_query($conn, "SELECT 
        checkouts_tab.*, 
        CONCAT(student_tab.first_name, ' ', student_tab.last_name) AS student_name, 
        student_tab.email_address, 
        hostels_tab.hostel_name, 
        rooms_tab.room_number 
    FROM checkouts_tab, student_tab, hostels_tab, rooms_tab 
    WHERE checkouts_tab.student_id = student_tab.student_id 
      AND checkouts_tab.hostel_id = hostels_tab.hostel_id 
      AND checkouts_tab.room_id = rooms_tab.room_id 
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
    'message' => "CHECKOUTS FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>