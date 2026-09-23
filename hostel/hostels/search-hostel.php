<?php require_once __DIR__ . '/../config/connection.php'; ?>

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

$searchHostelQuery = mysqli_query($conn, "SELECT * FROM hostels_tab  WHERE (hostel_name LIKE '%$searchContent%' OR code LIKE '%$searchContent%' OR hostel_id LIKE '%$searchContent%'  OR gender LIKE '%$searchContent%') ORDER BY hostel_name ASC") or die(mysqli_error($conn));

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