<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php

$query = mysqli_query($conn, "SELECT 
        transfers_tab.*,
        CONCAT(student_tab.first_name, ' ', student_tab.last_name) AS student_name,
        student_tab.email_address,
        old_h.hostel_name AS old_hostel_name,
        old_r.room_number AS old_room_number,
        new_h.hostel_name AS new_hostel_name,
        new_r.room_number AS new_room_number
    FROM transfers_tab, student_tab, hostels_tab AS old_h, rooms_tab AS old_r, hostels_tab AS new_h, rooms_tab AS new_r
    WHERE transfers_tab.student_id = student_tab.student_id
      AND transfers_tab.old_hostel_id = old_h.hostel_id
      AND transfers_tab.old_room_id = old_r.room_id
      AND transfers_tab.new_hostel_id = new_h.hostel_id
      AND transfers_tab.new_room_id = new_r.room_id
    ORDER BY transfers_tab.created_at DESC") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => "NO TRANSFER RECORDS FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($query, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "TRANSFERS FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>