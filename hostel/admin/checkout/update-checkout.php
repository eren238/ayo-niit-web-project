<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php

$checkoutId    = trim($_POST['checkoutId'] ?? '');
$reason        = trim($_POST['reason'] ?? '');
$roomCondition = trim($_POST['roomCondition'] ?? '');

if ($checkoutId == '') {
    $response = [
        'success' => false,
        'message' => "CHECKOUT ID IS REQUIRED"
    ];
    goto end;
}

if ($reason == '') {
    $response = [
        'success' => false,
        'message' => "REASON IS REQUIRED"
    ];
    goto end;
}

if ($roomCondition == '') {
    $response = [
        'success' => false,
        'message' => "ROOM CONDITION IS REQUIRED"
    ];
    goto end;
}

$checkQuery = mysqli_query($conn, "SELECT * FROM checkouts_tab WHERE checkout_id = '$checkoutId'") or die(mysqli_error($conn));

if (mysqli_num_rows($checkQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "CHECKOUT RECORD NOT FOUND"
    ];
    goto end;
}

mysqli_query($conn, "UPDATE `checkouts_tab` SET `reason` = '$reason', `room_condition` = '$roomCondition', `updated_at` = NOW() WHERE `checkout_id` = '$checkoutId'") or die(mysqli_error($conn));

$fetchQuery = mysqli_query($conn, "SELECT checkouts_tab.*, student_tab.first_name, student_tab.last_name, hostels_tab.hostel_name, rooms_tab.room_number FROM checkouts_tab, student_tab, hostels_tab, rooms_tab WHERE checkouts_tab.student_id = student_tab.student_id AND checkouts_tab.hostel_id = hostels_tab.hostel_id AND checkouts_tab.room_id = rooms_tab.room_id AND checkouts_tab.checkout_id = '$checkoutId'") or die(mysqli_error($conn));
$fetchData = mysqli_fetch_assoc($fetchQuery);

$response = [
    'success' => true,
    'message' => "CHECKOUT RECORD UPDATED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>