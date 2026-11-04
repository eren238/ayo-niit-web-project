<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php

$query = mysqli_query($conn, "SELECT * FROM status_tab ORDER BY status_id ASC") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => "NO STATUS RECORDS FOUND"
    ];
    goto end;
}

$fetchData = mysqli_fetch_all($query, MYSQLI_ASSOC);

$response = [
    'success' => true,
    'message' => "STATUS FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>      