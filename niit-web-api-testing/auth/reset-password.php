<?php require_once __DIR__ . '/../config/connection.php'; ?>

<?php
// DECLERATION OF VARIABLE
$emailAddress = trim($_POST['emailAddress']);
$resetOtp = trim($_POST['resetOtp']);
$newPassword = trim($_POST['newPassword']);
$confirmPassword = trim($_POST['confirmPassword']);

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

if ($resetOtp == '') {

    $response = [
        'success' => false,
        'message' => "RESET OTP IS REQUIRED, Kindly fill in the reset otp to continue"
    ];
    goto end;
}

if ($newPassword == '') {

    $response = [
        'success' => false,
        'message' => "NEW PASSWORD IS REQUIRED, Kindly fill in the new password to continue"
    ];
    goto end;
}

if ($confirmPassword == '') {

    $response = [
        'success' => false,
        'message' => "CONFIRM PASSWORD IS REQUIRED, Kindly fill in the confirm password to continue"
    ];
    goto end;
}

if ($newPassword != $confirmPassword) {

    $response = [
        'success' => false,
        'message' => "PASSWORDS DO NOT MATCH, Kindly ensure both passwords are the same"
    ];
    goto end;
}

$otpCheck = mysqli_query($conn, "SELECT * FROM user_tab WHERE email_address = '$emailAddress' AND reset_otp = '$resetOtp' LIMIT 1") or die(mysqli_error($conn));
if (mysqli_num_rows($otpCheck) == 0) {

    $response = [
        'success' => false,
        'message' => "INVALID RESET OTP! Kindly check your details and try again"
    ];
    goto end;
}

$userData = mysqli_fetch_assoc($otpCheck);
$userId = $userData['user_id'];
$hashedPassword = md5($newPassword);

$updateEachUserQuery = mysqli_query($conn, "SELECT user_tab.*, status_tab.status_name FROM user_tab, status_tab WHERE user_tab.status_id = status_tab.status_id AND user_tab.user_id = '$userId'") or die(mysqli_error($conn));
$userData = mysqli_fetch_assoc($updateEachUserQuery);

$response = [
    'success' => true,
    'message' => "PASSWORD RESET SUCCESSFUL",
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