<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php

// Uses a subquery to get student name so Available beds without occupants still show up (no LEFT JOIN needed!)
$fetchAllBedsQuery = mysqli_query($conn, "SELECT beds_tab.bed_id,
        rooms_tab.room_number,
        hostels_tab.hostel_name,
        COALESCE(
            (SELECT CONCAT(first_name, ' ', last_name) 
             FROM student_tab 
             WHERE student_tab.student_id = beds_tab.student_id),
            '—'
        ) AS occupant,
        beds_tab.status_id AS status
    FROM beds_tab, rooms_tab, hostels_tab 
    WHERE beds_tab.room_id = rooms_tab.room_id 
      AND beds_tab.hostel_id = hostels_tab.hostel_id 
    ORDER BY beds_tab.bed_id ASC") or die(mysqli_error($conn));

if (mysqli_num_rows($fetchAllBedsQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "NO BEDS FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($fetchAllBedsQuery, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "BEDS FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>