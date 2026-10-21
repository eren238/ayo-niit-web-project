<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php

$fetchAllBedsQuery = mysqli_query($conn, "SELECT beds_tab.*, rooms_tab.room_number, hostels_tab.hostel_name, status_tab.status_name FROM beds_tab, rooms_tab, hostels_tab, status_tab WHERE beds_tab.room_id = rooms_tab.room_id AND beds_tab.hostel_id = hostels_tab.hostel_id AND beds_tab.status_id = status_tab.status_id ORDER BY beds_tab.bed_id ASC") or die(mysqli_error($conn));

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