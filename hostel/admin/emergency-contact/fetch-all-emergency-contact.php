<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
$query = mysqli_query($conn, "SELECT emergency_contacts_tab.*, student_tab.first_name, student_tab.last_name, status_tab.status_name FROM emergency_contacts_tab LEFT JOIN student_tab ON emergency_contacts_tab.student_id = student_tab.student_id LEFT JOIN status_tab ON emergency_contacts_tab.status_id = status_tab.status_id ORDER BY emergency_contacts_tab.created_at DESC") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => "NO EMERGENCY CONTACT RECORDS FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($query, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "EMERGENCY CONTACTS FETCHED SUCCESSFUL",
    'data' => $fetchData
];

end:
echo json_encode($response);
?>