<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$searchContent = trim($_POST['searchContent'] ?? '');

if ($searchContent == '') {
    $response = [
        'success' => false,
        'message' => "SEARCH CONTENT IS REQUIRED, Kindly fill in the search content to continue"
    ];
    goto end;
}

$searchHostelQuery = mysqli_query($conn, "SELECT hostels_tab.*, status_tab.status_name FROM hostels_tab LEFT JOIN status_tab ON hostels_tab.status_id = status_tab.status_id WHERE (hostels_tab.hostel_name LIKE '%$searchContent%' OR hostels_tab.hostel_id LIKE '%$searchContent%' OR hostels_tab.gender LIKE '%$searchContent%' OR status_tab.status_name LIKE '%$searchContent%') ORDER BY hostels_tab.hostel_name ASC") or die(mysqli_error($conn));

if (mysqli_num_rows($searchHostelQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "NO HOSTEL FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($searchHostelQuery, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "HOSTELS SEARCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>
