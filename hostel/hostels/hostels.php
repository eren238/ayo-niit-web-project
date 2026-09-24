<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$hostelName     = trim($_POST['hostelName']);
$code           = trim($_POST['code']);
$gender         = trim($_POST['gender']);
$hostelCapacity = trim($_POST['capacity']);
$statusId       = trim($_POST['statusId'] ??'1');

$hostelId = $code;

if ($hostelName == '') {
    $response = [
        'success' => false,
        'message' => "HOSTEL NAME IS REQUIRED, Kindly fill in the hostel name to continue"
    ];
    goto end;
}

if ($code == '') {
    $response = [
        'success' => false,
        'message' => "HOSTEL CODE IS REQUIRED, Kindly fill in the hostel code to continue"
    ];
    goto end;
}

if ($gender == '') {
    $response = [
        'success' => false,
        'message' => "GENDER IS REQUIRED, Kindly select gender to continue"
    ];
    goto end;
}

if ($hostelCapacity == '' || !is_numeric($hostelCapacity) || $hostelCapacity <= 0) {
    $response = [
        'success' => false,
        'message' => "VALID CAPACITY IS REQUIRED, Kindly enter a valid capacity number"
    ];
    goto end;
}

$checkCodeQuery = mysqli_query($conn, "SELECT * FROM hostels_tab WHERE code = '$code' OR hostel_id = '$hostelId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkCodeQuery) > 0) {
    $response = [
        'success' => false,
        'message' => "HOSTEL ALREADY EXISTS! A hostel with code '$code' already exists"
    ];
    goto end;
}

mysqli_query($conn, "INSERT INTO `hostels_tab`
    (`hostel_id`, `hostel_name`, `code`, `gender`, `hostel_capacity`) VALUES
    ('$hostelId', '$hostelName', '$code', '$gender', '$hostelCapacity')") or die(mysqli_error($conn));


$fetchHostelQuery = mysqli_query($conn, "SELECT * FROM hostels_tab WHERE hostel_id = '$hostelId'") or die(mysqli_error($conn));
$hostelData = mysqli_fetch_assoc($fetchHostelQuery);

$response = [
    'success' => true,
    'message' => "HOSTEL CREATED SUCCESSFULLY",
    'data'    => [
        'hostelId'       => $hostelData['hostel_id'],
        'hostelName'     => $hostelData['hostel_name'],
        'code'           => $hostelData['code'],
        'gender'         => $hostelData['gender'],
        'hostelCapacity' => $hostelData['hostel_capacity']
    ]
];

end:
echo json_encode($response);
?>