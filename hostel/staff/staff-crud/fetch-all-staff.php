php


<?php require_once __DIR__ . '/../../config/connection.php'; ?>
<?php
$fetchAllStaffQuery = mysqli_query($conn, "SELECT  staff_tab.*, role_tab.role_name, status_tab.status_name FROM staff_tab, role_tab, status_tab WHERE staff_tab.role_id = role_tab.role_id AND staff_tab.status_id = status_tab.status_id   ORDER BY staff_tab.created_at DESC") or die(mysqli_error($conn));
if (mysqli_num_rows($fetchAllStaffQuery) == 0) {
    $response = [
        'success' => false,
        'message' => 'NO STAFF FOUND'
    ];
    goto end;
}
$fetchData = mysqli_fetch_all($fetchAllStaffQuery, MYSQLI_ASSOC);
$response = [
    'success' => true,
    'message' => "STAFF FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];
end:
echo json_encode($response);
?>