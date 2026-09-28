<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php

$query = mysqli_query($conn, "SELECT 
        beds_tab.bed_id,
        hostels_tab.hostel_name,
        rooms_tab.room_number
    FROM beds_tab, hostels_tab, rooms_tab 
    WHERE beds_tab.hostel_id = hostels_tab.hostel_id 
      AND beds_tab.room_id = rooms_tab.room_id 
      AND beds_tab.status_id = 'D'
    ORDER BY hostels_tab.hostel_name, rooms_tab.room_number ASC") or die(mysqli_error($conn));

$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

echo json_encode([
    'success' => true,
    'data'    => $data
]);
?>