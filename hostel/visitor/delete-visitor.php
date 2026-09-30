<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php

$visitorId = trim($_POST['visitorId']);

if ($visitorId == '') {
    $response = [
        'success' => false,
        'message' => "VISITOR ID REQUIRED"
    ];
    goto end;
}


$checkRecord = mysqli_query($conn, "SELECT * FROM visitors_tab WHERE visitor_id = '$visitorId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkRecord) == 0) {
    $response = [
        'success' => false,
        'message' => "VISITOR RECORD NOT FOUND"
    ];
    goto end;
}


mysqli_query($conn, "DELETE FROM `visitors_tab` WHERE `visitor_id` = '$visitorId'") or die(mysqli_error($conn));

$response = [
    'success' => true,
    'message' => "VISITOR RECORD DELETED SUCCESSFULLY"
];

end:
echo json_encode($response);
?>