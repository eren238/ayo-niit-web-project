<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$bedId = trim($_POST['bedId'] ?? '');

if ($bedId == '') {
    $response = [
        'success' => false,
        'message' => 'BED ID REQUIRED'
    ];
    goto end;
}

// 1. CHECK IF BED EXISTS
$checkBed = mysqli_query($conn, "SELECT * FROM beds_tab WHERE bed_id = '$bedId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkBed) == 0) {
    $response = [
        'success' => false,
        'message' => 'BED NOT FOUND'
    ];
    goto end;
}

$bedData = mysqli_fetch_assoc($checkBed);

// 2. PREVENT DELETING OCCUPIED BED ('O')
if ($bedData['status_id'] == 'O') {
    $response = [
        'success' => false,
        'message' => 'CANNOT DELETE BED: This bed is currently occupied by a student!'
    ];
    goto end;
}

// 3. DELETE BED
mysqli_query($conn, "DELETE FROM beds_tab WHERE bed_id = '$bedId'") or die(mysqli_error($conn));

$response = [
    'success' => true,
    'message' => 'BED DELETED SUCCESSFULLY'
];

end:
echo json_encode($response);
?>