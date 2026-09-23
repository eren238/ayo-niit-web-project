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

$otpCheck = mysqli_query($conn, "SELECT * FROM student_tab WHERE email_address = '$emailAddress' AND reset_otp = '$resetOtp' LIMIT 1") or die(mysqli_error($conn));
if (mysqli_num_rows($otpCheck) == 0) {

    $response = [
        'success' => false,
        'message' => "INVALID RESET OTP! Kindly check your details and try again"
    ];
    goto end;
}

$studentData = mysqli_fetch_assoc($otpCheck);
$studentId = $studentData['student_id'];
$hashedPassword = md5($newPassword);

mysqli_query($conn, "UPDATE student_tab SET password = '$hashedPassword', reset_otp = NULL, updated_at = NOW() WHERE student_id = '$studentId'") or die(mysqli_error($conn));

$updateEachstudentQuery = mysqli_query($conn, "SELECT student_tab.*, status_tab.status_name FROM student_tab, status_tab WHERE student_tab.status_id = status_tab.status_id AND student_tab.student_id = '$studentId'") or die(mysqli_error($conn));
$studentData = mysqli_fetch_assoc($updateEachstudentQuery);

$response = [
    'success' => true,
    'message' => "PASSWORD RESET SUCCESSFUL",
    'data' => [
        'studentId' => $studentData['student_id'],
        'firstName' => $studentData['first_name'],
        'lastName' => $studentData['last_name'],
        'emailAddress' => $studentData['email_address'],
        'statusName' => $studentData['status_name'],
        'password' => $studentData['password'],
        'createdAt' => $studentData['created_at'],
        'updatedAt' => $studentData['updated_at']
    ]
];

end:
echo json_encode($response);
?>