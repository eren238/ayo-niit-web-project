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

$searchFeeQuery = mysqli_query($conn, "SELECT fees_tab.*, hostels_tab.hostel_name, status_tab.status_name FROM fees_tab, hostels_tab, status_tab WHERE fees_tab.hostel_id = hostels_tab.hostel_id AND fees_tab.status_id = status_tab.status_id AND (fees_tab.fee_name LIKE '%$searchContent%' OR fees_tab.session LIKE '%$searchContent%' OR fees_tab.amount LIKE '%$searchContent%' OR hostels_tab.hostel_name LIKE '%$searchContent%')") or die(mysqli_error($conn));

if (mysqli_num_rows($searchFeeQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "NO FEE FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($searchFeeQuery, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "FEE SEARCH SUCCESFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>