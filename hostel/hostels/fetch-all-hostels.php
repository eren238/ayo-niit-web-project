<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php

$fetchAllHostelsQuery = mysqli_query($conn, "SELECT * FROM hostels_tab ORDER BY hostel_name ") or die(mysqli_error($conn));

if (mysqli_num_rows($fetchAllHostelsQuery) == 0) {
    $response = [
        'success' => false,
        'message' => 'NO HOSTEL FOUND'
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($fetchAllHostelsQuery, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "HOSTELS FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>