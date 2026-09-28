<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php

$query = mysqli_query($conn, "SELECT 
        student_tab.student_id,
        CONCAT(student_tab.first_name, ' ', student_tab.last_name) AS student_name,
        rooms_tab.room_number,
        allocation_tab.bed_id
    FROM student_tab, allocation_tab, rooms_tab 
    WHERE student_tab.student_id = allocation_tab.student_id 
      AND allocation_tab.room_id = rooms_tab.room_id 
      AND allocation_tab.status_id = 'A'
    ORDER BY student_tab.first_name ASC") or die(mysqli_error($conn));

$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

echo json_encode([
    'success' => true,
    'data'    => $data
]);
?>