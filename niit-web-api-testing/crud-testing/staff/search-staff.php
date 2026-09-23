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

$searchStaffQuery = mysqli_query($conn, "SELECT staff_tab.*, role_tab.role_name, status_tab.status_name FROM staff_tab, role_tab, status_tab WHERE staff_tab.role_id = role_tab.role_id AND staff_tab.status_id = status_tab.status_id AND (staff_tab.first_name LIKE '%$searchContent%' OR staff_tab.last_name LIKE '%$searchContent%' OR staff_tab.email_address LIKE '%$searchContent%' OR staff_tab.staff_id LIKE '%$searchContent%' OR staff_tab.phone_number LIKE '%$searchContent%')") or die(mysqli_error($conn));

if (mysqli_num_rows($searchStaffQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "NO STAFF FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($searchStaffQuery, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "STAFF SEARCH SUCCESFULLY",
    'data' => $fetchData
];

end:
echo json_encode($response);
?>