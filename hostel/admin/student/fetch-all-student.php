<?php require_once __DIR__ . '/../../config/connection.php'; ?>
<?php
// Safe query joining status and emergency contact
$queryStr = "SELECT student_tab.*, status_tab.status_name,
    emergency_contacts_tab.contact_id,
    emergency_contacts_tab.nok_name,
    emergency_contacts_tab.relationship,
    emergency_contacts_tab.phone_number AS nok_phone,
    emergency_contacts_tab.residential_address AS nok_address
    FROM student_tab
    LEFT JOIN status_tab ON student_tab.status_id = status_tab.status_id
    LEFT JOIN emergency_contacts_tab ON student_tab.student_id = emergency_contacts_tab.student_id
    ORDER BY student_tab.created_at DESC";

$fetchAllStudentQuery = @mysqli_query($conn, $queryStr);

// Fallback if emergency_contacts_tab doesn't exist yet on some database instances
if (!$fetchAllStudentQuery) {
    $fetchAllStudentQuery = mysqli_query($conn, "SELECT student_tab.*, status_tab.status_name FROM student_tab LEFT JOIN status_tab ON student_tab.status_id = status_tab.status_id ORDER BY student_tab.created_at DESC") or die(mysqli_error($conn));
}

if (mysqli_num_rows($fetchAllStudentQuery) == 0) {
    $response = [
        'success' => false,
        'message' => 'NO STUDENT FOUND'
    ];
    goto end;
}
$fetchData = mysqli_fetch_all($fetchAllStudentQuery, MYSQLI_ASSOC);
$response = [
    'success' => true,
    'message' => "STUDENTS FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];
end:
echo json_encode($response);
?>
