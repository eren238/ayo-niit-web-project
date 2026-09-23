<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$searchContent = trim($_POST['searchContent']);

if ($searchContent == '') {
    $response = [
        'success' => false,
        'message' => "SEARCH CONTENT IS REQUIRED, Kindly fill in the search content to continue"
    ];
    goto end;
}

$searchBedsQuery = mysqli_query($conn, "SELECT 
        beds_tab.bed_id,
        rooms_tab.room_number,
        hostels_tab.hostel_name,
        COALESCE(
            (SELECT CONCAT(first_name, ' ', last_name) 
             FROM student_tab 
             WHERE student_tab.student_id = beds_tab.student_id),
            '—'
        ) AS occupant,
        status_tab.status_name AS status,
        beds_tab.status_id
    FROM beds_tab, rooms_tab, hostels_tab, status_tab 
    WHERE beds_tab.room_id = rooms_tab.room_id 
      AND beds_tab.hostel_id = hostels_tab.hostel_id 
      AND beds_tab.status_id = status_tab.status_id 
      AND (
          beds_tab.bed_id LIKE '%$searchContent%' 
          OR rooms_tab.room_number LIKE '%$searchContent%' 
          OR hostels_tab.hostel_name LIKE '%$searchContent%' 
          OR status_tab.status_name LIKE '%$searchContent%'
          OR beds_tab.student_id IN (
              SELECT student_id 
              FROM student_tab 
              WHERE first_name LIKE '%$searchContent%' 
                 OR last_name LIKE '%$searchContent%'
          )
      )
    ORDER BY beds_tab.bed_id ASC") or die(mysqli_error($conn));

if (mysqli_num_rows($searchBedsQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "NO BEDS FOUND MATCHING '$searchContent'"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($searchBedsQuery, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "BEDS SEARCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>