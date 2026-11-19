<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load central mail configuration
if (file_exists(__DIR__ . '/../../../config/mail-config.php')) {
    require_once __DIR__ . '/../../../config/mail-config.php';
} elseif (file_exists(__DIR__ . '/../../mail-config.php')) {
    require_once __DIR__ . '/../../mail-config.php';
}

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

$smtpEmail    = defined('SMTP_EMAIL') ? SMTP_EMAIL : 'yourgmail@gmail.com';
$smtpPassword = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : 'xxxx xxxx xxxx xxxx';
$senderName   = defined('SMTP_SENDER_NAME') ? SMTP_SENDER_NAME : 'School Hostel Portal';

$recipientEmail = !empty($emailAddress) ? trim($emailAddress) : '';
$mailOtp        = $resetOtp ?? $otpCode ?? '';

if (empty($recipientEmail) || empty($mailOtp)) {
    return;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpEmail;
    $mail->Password   = $smtpPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom($smtpEmail, $senderName);
    $mail->addAddress($recipientEmail);

    $mail->isHTML(true);
    $mail->Subject = 'Your Password Reset OTP - School Hostel';

    $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 520px; margin: 0 auto; padding: 24px; border: 1px solid #e2e8f0; border-radius: 8px; background-color: #ffffff;'>
            <div style='border-bottom: 2px solid #2563eb; padding-bottom: 12px; margin-bottom: 20px;'>
                <h2 style='color: #1e3a8a; margin: 0; font-size: 20px;'>School Hostel Portal</h2>
                <p style='color: #64748b; font-size: 13px; margin: 4px 0 0;'>Password Reset Verification</p>
            </div>
            <p style='color: #334155; font-size: 15px;'>Hello,</p>
            <p style='color: #334155; font-size: 14px;'>You requested a one-time verification code (OTP) to reset your password. Use the code below to complete the verification:</p>
            <div style='background: #f1f5f9; padding: 18px; text-align: center; border-radius: 6px; margin: 24px 0;'>
                <span style='font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #1e40af;'>$mailOtp</span>
            </div>
            <p style='color: #64748b; font-size: 13px;'>This OTP code will expire in <strong>10 minutes</strong>. If you did not request a password reset, please ignore this email or contact the administrator.</p>
            <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
            <p style='color: #94a3b8; font-size: 11px; margin: 0;'>School Hostel Management System &bull; Automated notification</p>
        </div>
    ";

    $mail->AltBody = "Your Password Reset OTP is: $mailOtp. This code will expire in 10 minutes.";

    // Only attempt real send if user has set real credentials
    if ($smtpEmail !== 'yourgmail@gmail.com' && !empty($smtpPassword) && $smtpPassword !== 'xxxx xxxx xxxx xxxx') {
        $mail->send();
    }
} catch (\Throwable $e) {
    // Fail silently without echoing HTML/errors to avoid breaking JSON response
}
?>