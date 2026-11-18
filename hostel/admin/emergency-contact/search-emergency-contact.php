<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLERATION OF VARIABLE

$searchQuery = trim($_POST['searchQuery']);

if ($searchQuery == '') {
    $response = [
        'success' => false,
        'message' => "SEARCH QUERY IS REQUIRED, Kindly fill in the search term to continue"
    ];
    goto end;
}

$query = mysqli_query($conn, "SELECT emergency_contacts_tab.*, student_tab.first_name, student_tab.last_name, status_tab.status_name FROM emergency_contacts_tab LEFT JOIN student_tab ON emergency_contacts_tab.student_id = student_tab.student_id LEFT JOIN status_tab ON emergency_contacts_tab.status_id = status_tab.status_id WHERE emergency_contacts_tab.nok_name LIKE '%$searchQuery%' OR emergency_contacts_tab.phone_number LIKE '%$searchQuery%' OR emergency_contacts_tab.student_id LIKE '%$searchQuery%' OR student_tab.first_name LIKE '%$searchQuery%' OR student_tab.last_name LIKE '%$searchQuery%' ORDER BY emergency_contacts_tab.created_at DESC") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => "NO MATCHING EMERGENCY CONTACT RECORDS FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($query, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "EMERGENCY CONTACTS SEARCH SUCCESSFUL",
    'data' => $fetchData
];

end:
echo json_encode($response);
?>