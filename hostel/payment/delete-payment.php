<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
$allocationId = trim($_POST['allocationId']);

if ($allocationId == '') {
    $response = [
        'success' => false,
        'message' => 'ALLOCATION ID REQUIRED'
    ];
    goto end;
}

$checkAlloc = mysqli_query($conn, "SELECT * FROM allocation_tab WHERE allocation_id = '$allocationId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkAlloc) == 0) {
    $response = [
        'success' => false,
        'message' => 'ALLOCATION NOT FOUND'
    ];
    goto end;
}

$allocData = mysqli_fetch_assoc($checkAlloc);
$bedId = $allocData['bed_id'];

mysqli_query($conn, "UPDATE `beds_tab` SET `status_id` = 'D', `student_id` = NULL, `updated_at` = NOW() WHERE `bed_id` = '$bedId'") or die(mysqli_error($conn));
mysqli_query($conn, "DELETE FROM `allocation_tab` WHERE `allocation_id` = '$allocationId'") or die(mysqli_error($conn));

$response = [
    'success' => true,
    'message' => 'ALLOCATION DELETED AND BED FREED SUCCESSFULLY'
];

end:
echo json_encode($response);
?>