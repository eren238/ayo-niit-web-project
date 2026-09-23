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

$searchRoomQuery = mysqli_query($conn, "SELECT  rooms_tab.*, hostels_tab.hostel_name FROM rooms_tab, hostels_tab   WHERE rooms_tab.hostel_id = hostels_tab.hostel_id  AND (rooms_tab.room_number LIKE '%$searchContent%'  OR rooms_tab.block LIKE '%$searchContent%'  OR rooms_tab.floor LIKE '%$searchContent%'  OR rooms_tab.room_id LIKE '%$searchContent%'   OR hostels_tab.hostel_name LIKE '%$searchContent%')ORDER BY rooms_tab.room_number ASC") or die(mysqli_error($conn));

if (mysqli_num_rows($searchRoomQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "NO ROOMS FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($searchRoomQuery, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "ROOMS SEARCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>