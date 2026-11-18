<?php require_once __DIR__ . '/../../config/connection.php'; ?>

<?php
// DECLARATION OF VARIABLE
$emailAddress = trim($_POST['emailAddress'] ?? '');

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

$emailCheck = mysqli_query($conn, "SELECT * FROM staff_tab WHERE email_address = '$emailAddress' LIMIT 1") or die(mysqli_error($conn));
if (mysqli_num_rows($emailCheck) == 0) {
    $response = [
        'success' => false,
        'message' => "EMAIL NOT FOUND! No account is registered with this email address"
    ];
    goto end;
}

$staffData = mysqli_fetch_assoc($emailCheck);
$staffId = $staffData['staff_id'];
$staffName = $staffData['first_name'] . " " . $staffData['last_name'];

$otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

if (file_exists(__DIR__ . '/../../config/mail/user/sent-otp-to-mail.php')) {
    $resetOtp = $otpCode;
    try {
        @include_once __DIR__ . '/../../config/mail/user/sent-otp-to-mail.php';
    } catch (\Throwable $t) {
        // Fail silently so password reset flow continues
    }
}

mysqli_query($conn, "UPDATE staff_tab SET reset_otp='$otpCode', updated_at=NOW() WHERE staff_id='$staffId'");

$response = [
    'success' => true,
    'message' => "OTP GENERATED SUCCESSFULLY",
    'data' => [
        'staffId' => $staffId,
        'otpCode' => $otpCode,
        'emailAddress' => $emailAddress,
    ]
];

end:
echo json_encode($response);
?>
