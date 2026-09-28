<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$checkoutId = trim($_POST['checkoutId'] ?? '');

if ($checkoutId == '') {
    $response = [
        'success' => false,
        'message' => 'CHECKOUT ID REQUIRED'
    ];
    goto end;
}

$query = mysqli_query($conn, "SELECT 
        checkouts_tab.*, 
        CONCAT(student_tab.first_name, ' ', student_tab.last_name) AS student_name, 
        student_tab.email_address, 
        student_tab.phone_number, 
        hostels_tab.hostel_name, 
        rooms_tab.room_number 
    FROM checkouts_tab, student_tab, hostels_tab, rooms_tab 
    WHERE checkouts_tab.student_id = student_tab.student_id 
      AND checkouts_tab.hostel_id = hostels_tab.hostel_id 
      AND checkouts_tab.room_id = rooms_tab.room_id 
      AND checkouts_tab.checkout_id = '$checkoutId'") or die(mysqli_error($conn));

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => 'CHECKOUT RECORD NOT FOUND'
    ];
    goto end;
}

$fetchData = mysqli_fetch_assoc($query);

$response = [
    'success' => true,
    'message' => "CHECKOUT RECORD FETCHED SUCCESSFULLY",
    'data'    => $fetchData
];

end:
echo json_encode($response);
?>