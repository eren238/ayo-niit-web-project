<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLERATION OF VARIABLE
$firstName = trim($_POST['firstName']);
$lastName = trim($_POST['lastName']);
$emailAddress = trim($_POST['emailAddress']);
$phoneNumber = trim($_POST['phoneNumber']);
$password = md5(trim($_POST['password']));
$statusId = 'A';

if ($firstName == '') {
    $response = [
        'success' => false,
        'message' => "FIRST NAME IS REQUIRED, Kindly fill in the first name to continue"
    ];
    goto end;
}

if ($lastName == '') {
    $response = [
        'success' => false,
        'message' => "LAST NAME IS REQUIRED, Kindly fill in the last name to continue"
    ];
    goto end;
}

if ($emailAddress == '') {
    $response = [
        'success' => false,
        'message' => "EMAIL ADDRESS NAME IS REQUIRED, Kindly fill in the email address to continue"
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

if ($phoneNumber == '') {
    $response = [
        'success' => false,
        'message' => "PHONE NUMBER IS REQUIRED, Kindly fill in the phone number to continue"
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

$emailcheck = mysqli_query($conn, "SELECT * FROM user_tab WHERE email_address = '$emailAddress'") or die(mysqli_error($conn));
if (mysqli_num_rows($emailcheck) > 0) {
    $response = [
        "success" => false,
        "message" => "USER ALREDY EXIST kindly proceed to sign in or forget password"
    ];
    goto end;
}

$userId = 'USER' . date("Ymdhis");

mysqli_query($conn, "INSERT INTO `user_tab`
    (`user_id`, `first_name`, `last_name`, `email_address`, `phone_number`, `password`, `status_id`, `reset_otp`, `created_at`, `updated_at`) VALUES
    ('$userId', '$firstName', '$lastName', '$emailAddress', '$phoneNumber', '$password', '$statusId', 0, NOW(), NOW())") or die(mysqli_error($conn));
$createUserQuery = mysqli_query($conn, "SELECT user_tab.*, status_tab.status_name FROM user_tab, status_tab WHERE user_tab.status_id = status_tab.status_id AND user_tab.email_address = '$emailAddress'") or die(mysqli_error($conn));
$userData = mysqli_fetch_assoc($createUserQuery);

$response = [
    'success' => true,
    'message' => "LOGIN SUCCESSFUL",
    'data' => [
        'userId' => $userData['user_id'],
        'firstName' => $userData['first_name'],
        'lastName' => $userData['last_name'],
        'emailAddress' => $userData['email_address'],
        'phoneNumber' => $userData['phone_number'],
        'address' => $userData['address'],
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