<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php

$staffId = trim($_POST['staffId']);

if ($staffId == '') {
    $response = [
        'success' => false,
        'message' => 'STAFF ID REQUIRED'
    ];
    goto end;
}

$checkStaffQuery = mysqli_query($conn, "SELECT * FROM staff_tab WHERE staff_id = '$staffId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkStaffQuery) == 0) {
    $response = [
        'success' => false,
        'message' => 'STAFF ID NOT FOUND'
    ];
    goto end;
}

$checkStaffQuery = mysqli_query($conn, "SELECT staff_tab.*, role_tab.role_name, status_tab.status_name FROM staff_tab, role_tab, status_tab WHERE staff_tab.role_id = role_tab.role_id AND staff_tab.status_id = status_tab.status_id AND staff_tab.staff_id = '$staffId'") or die(mysqli_error($conn));
while ($fetchData = mysqli_fetch_assoc($checkStaffQuery)) {
    $response = [
        'success' => true,
        'message' => "STAFF FETCH SUCCESFULLY",
        'data' => $fetchData
    ];
}

end:
echo json_encode($response);
?>