<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php

$query = mysqli_query($conn, "SELECT 
        fees_tab.*, 
        hostels_tab.hostel_name, 
        status_tab.status_name 
    FROM fees_tab, hostels_tab, status_tab 
    WHERE fees_tab.hostel_id = hostels_tab.hostel_id 
      AND fees_tab.status_id = status_tab.status_id 
    ORDER BY fees_tab.created_at DESC") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => "NO HOSTEL FEES FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($query, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "HOSTEL FEES FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>