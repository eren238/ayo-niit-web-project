<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// Decleration Of Variable
$emailAddress = trim($_POST['emailAddress']);
$resetOtp     = trim($_POST['resetOtp']);

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

$emailCheck = mysqli_query($conn, "SELECT * FROM student_tab WHERE email_address = '$emailAddress' LIMIT 1") or die(mysqli_error($conn));
if (mysqli_num_rows($emailCheck) == 0) {
    $response = [
        'success' => false,
        'message' => "EMAIL NOT FOUND! No account is registered with this email address"
    ];
    goto end;
}

$studentData = mysqli_fetch_assoc($emailCheck);
$studentId = $studentData['student_id'];

$emailCheck = mysqli_query($conn, "SELECT * FROM student_tab WHERE email_address = '$emailAddress'");
if (mysqli_num_rows($emailCheck) == 0) {
    $response = [
        'success' => false,
        'message' => "EMAIL NOT FOUND! No account is registered with this email address"
    ];
    goto end;
}

$studentData = mysqli_fetch_assoc($emailCheck);
$studentId = $studentData['student_id'];

$otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);


mysqli_query($conn, "UPDATE student_tab SET reset_otp='$otpCode', updated_at=NOW() WHERE student_id='$studentId'");

$response = [
    'success' => true,
    'message' => "OTP GENERATED SUCCESSFULLY",
    'data' => [
        'studentId' => $studentId,
        'otpCode' => $otpCode,
        'emailAddress' => $emailAddress,
    ]
];

end:
echo json_encode($response);

?>;