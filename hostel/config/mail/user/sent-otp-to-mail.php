<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    if (file_exists(__DIR__ . '/PHPMailer/src/Exception.php')) {
        require_once __DIR__ . '/PHPMailer/src/Exception.php';
        require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
        require_once __DIR__ . '/PHPMailer/src/SMTP.php';
    }
}

if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    return;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    // Your Gmail
    $mail->Username = 'yourgmail@gmail.com';

    // Gmail App Password
    $mail->Password = 'xxxx xxxx xxxx xxxx';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Sender
    $mail->setFrom('yourgmail@gmail.com', 'School Hostel');

    // Where you want to receive the OTP
    if (!empty($emailAddress)) {
        $mail->addAddress($emailAddress);
    } else {
        $mail->addAddress('yourgmail@gmail.com');
    }

    $mail->isHTML(true);
    $mail->Subject = 'Your OTP Code';

    $mailOtp = $resetOtp ?? $otpCode ?? '';

    $mail->Body = "
        <h2>OTP Verification</h2>
        <p>Your OTP code is:</p>
        <h1>$mailOtp</h1>
        <p>This code will expire in 10 minutes.</p>
    ";

    $mail->AltBody = "Your OTP code is: $mailOtp.";

    // Only attempt sending if real credentials are set
    if ($mail->Username !== 'yourgmail@gmail.com' && $mail->Password !== 'xxxx xxxx xxxx xxxx') {
        $mail->send();
    }
} catch (\Throwable $e) {
    // Fail silently without echoing HTML/errors to avoid breaking JSON response
}
