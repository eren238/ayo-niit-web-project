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


$searchUserQuery = mysqli_query($conn, "SELECT user_tab.*, status_tab.status_name FROM user_tab, status_tab WHERE user_tab.status_id = status_tab.status_id AND (user_tab.first_name LIKE '%$searchContent%' OR user_tab.last_name LIKE '%$searchContent%' OR user_tab.email_address LIKE '%$searchContent%' OR user_tab.user_id LIKE '%$searchContent%')") or die(mysqli_error($conn));

if (mysqli_num_rows($searchUserQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "NO USER FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($searchUserQuery, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "USER SEARCH SUCCESFULLY",
    'data' => $fetchData
];

end:
echo json_encode($response);
?>