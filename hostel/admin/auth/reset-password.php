<?php require_once __DIR__ . '/../../config/connection.php'; ?>

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

$otpCheck = mysqli_query($conn, "SELECT * FROM staff_tab WHERE email_address = '$emailAddress' AND reset_otp = '$resetOtp' LIMIT 1") or die(mysqli_error($conn));
if (mysqli_num_rows($otpCheck) == 0) {

    $response = [
        'success' => false,
        'message' => "INVALID RESET OTP! Kindly check your details and try again"
    ];
    goto end;
}

$staffData = mysqli_fetch_assoc($otpCheck);
$staffId = $staffData['staff_id'];
$hashedPassword = md5($newPassword);

mysqli_query($conn, "UPDATE staff_tab SET password = '$hashedPassword', reset_otp = NULL, updated_at = NOW() WHERE staff_id = '$staffId'") or die(mysqli_error($conn));

$updateEachstaffQuery = mysqli_query($conn, "SELECT staff_tab.*, status_tab.status_name FROM staff_tab, status_tab WHERE staff_tab.status_id = status_tab.status_id AND staff_tab.staff_id = '$staffId'") or die(mysqli_error($conn));
$staffData = mysqli_fetch_assoc($updateEachstaffQuery);

$response = [
    'success' => true,
    'message' => "PASSWORD RESET SUCCESSFUL",
    'data' => [
        'staffId' => $staffData['staff_id'],
        'firstName' => $staffData['first_name'],
        'lastName' => $staffData['last_name'],
        'emailAddress' => $staffData['email_address'],
        'statusName' => $staffData['status_name'],
        'password' => $staffData['password'],
        'createdAt' => $staffData['created_at'],
        'updatedAt' => $staffData['updated_at']
    ]
];

end:
echo json_encode($response);
?>