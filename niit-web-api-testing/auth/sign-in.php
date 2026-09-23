<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLERATION OF VARIABLE
$emailAddress = trim($_POST['emailAddress']);
$password = md5(trim($_POST['password']));

if ($emailAddress == '') {

    $response = [
        'success' => false,
        'message' => "EMAIL ADDRESS IS REQUIRED, Kindly fill in the email address to continue"
    ];
    goto end;
}

if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
    $response = [
        'response' => 102,
        'success' => false,
        'message' => "INVALID EMAIL ADDRESS! Enter a valid email address and try again",
    ];
    goto end;
}

if ($password == '') {

    $response = [
        'success' => false,
        'message' => "PASSWORD IS REQUIRED, Kindly fill in the password to continue"
    ];
    goto end;
}


$loginQuery = mysqli_query($conn, "SELECT * FROM user_tab WHERE `email_address` = '$emailAddress' AND  `password` = '$password'") or die(mysqli_error($conn));

if (mysqli_num_rows($loginQuery) == 0) {
    $response = [
        'success' => false,
        'message' => "INVALID EMAIL OR PASSWORD! Kindly check your details and try again"
    ];
    goto end;
}

$loginQuery = mysqli_query($conn, "SELECT user_tab.*, status_tab.status_name FROM user_tab, status_tab WHERE user_tab.status_id = status_tab.status_id AND user_tab.email_address = '$emailAddress' AND user_tab.password = '$password'") or die(mysqli_error($conn));
$userData = mysqli_fetch_assoc($loginQuery);


$response = [
    'success' => true,
    'message' => "LOGIN SUCCESSFUL",
    'data' => [
        'userId' => $userData['user_id'],
        'firstName' => $userData['first_name'],
        'lastName' => $userData['last_name'],
        'emailAddress' => $userData['email_address'],
        'phoneNumber' => $userData['phone'],
        'statusId' => $userData['status_id'],
        'statusName' => $userData['status_name'],
        'createdAt' => $userData['created_at'],
        'updatedAt' => $userData['updated_at']
    ]
];

end:
echo json_encode($response);
?>                                                                