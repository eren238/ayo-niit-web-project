<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$feeId = trim($_POST['feeId']);

if ($feeId == '') {
    $response = [
        'success' => false,
        'message' => 'FEE ID REQUIRED'
    ];
    goto end;
}

$query = mysqli_query($conn, "SELECT 
        fees_tab.*, 
        hostels_tab.hostel_name, 
        status_tab.status_name 
    FROM fees_tab, hostels_tab, status_tab 
    WHERE fees_tab.hostel_id = hostels_tab.hostel_id 
      AND fees_tab.status_id = status_tab.status_id 
      AND fees_tab.fee_id = '$feeId'") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => 'FEE NOT FOUND'
    ];
    goto end;
}

$fetchData = mysqli_fetch_assoc($query);

$response = [
    'success' => true,
    'message' => "FEE DETAILS FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>