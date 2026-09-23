<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
//DECLERATION OF VARIABLE 

$userId = trim($_POST['userId']);

$checkUserQuery = mysqli_query($conn, "SELECT * FROM user_tab WHERE user_id = '$userId'") or die(mysqli_error($conn));

if ($userId == '') {
    $response = [
        'success' => false,
        'message' => 'USER ID REQUIRED'
    ];
   goto end;  

}

if (mysqli_num_rows($checkUserQuery) == 0) {
    $response = [
        'success' => false,
        'message' => 'USER NOT FOUND'
    ];
   goto end;  

}


  $fetchEachUserQuery = mysqli_query($conn, "SELECT * FROM user_tab WHERE user_id = '$userId'") or die(mysqli_error($conn));
  
  while ($fetchdata = mysqli_fetch_assoc($fetchEachUserQuery)) {
    $response = [
    'success' => true,
    'mesagge' => "USER FETCH SUCCESSFULLY",
    'date' => $fetchdata
    
   ];
}

$fetchEachUserQuery = mysqli_query($conn, "SELECT user_tab.*, status_tab.status_name FROM user_tab, status_tab WHERE user_tab.status_id = status_tab.status_id AND user_tab.user_id = '$userId'") or die(mysqli_error($conn));
$userData = mysqli_fetch_assoc($fetchEachUserQuery);

$response = [
    'success' => true,
    'message' => "USER FETCH SUCCESSFUL",
    'data' => [
        'userId' => $userData['user_id'],
        'firstName' => $userData['first_name'],
        'lastName' => $userData['last_name'],
        'emailAddress' => $userData['email_address'],
        'phoneNumber' => $userData['phone'],
        'statusId' => $userData['status_id'],
        'statusName' => $userData['status_name'],
        'password' => $userData['password'],
        'createdAt' => $userData['created_at'],
        'updatedAt' => $userData['updated_at']
    ]
];
end:
echo json_encode($response);
?>