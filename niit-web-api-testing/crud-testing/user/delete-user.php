<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php

$userId = trim($_POST['userId']);

$checkUserQuery = mysqli_query($conn, "SELECT * FROM user_tab WHERE user_id = '$userId'") or die(mysqli_error($conn));
if (mysqli_num_rows($checkUserQuery) == 0) {
    $response = [
        'success' => false,
        'message' => 'USER NOT FOUND'
    ];
   Goto end;  
}

 mysqli_query($conn, "DELETE FROM user_tab WHERE user_id = '$userId'") or die(mysqli_error($conn));

    $response = [
        'success' => true,
        'message' => 'USER DELETED SUCCESSFULLY'
    ];

end:
echo json_encode($response);

?>