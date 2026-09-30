<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php

$query = mysqli_query($conn, "SELECT complaints_tab.*, student_tab.first_name, student_tab.last_name, status_tab.status_name, DATE_FORMAT(complaints_tab.complaint_date, '%d %b %Y') AS formatted_date FROM complaints_tab, student_tab, status_tab WHERE complaints_tab.student_id = student_tab.student_id AND complaints_tab.status_id = status_tab.status_id ORDER BY complaints_tab.created_at DESC") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => "NO COMPLAINTS FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($query, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "COMPLAINTS FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>