<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';



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
    $mail->setFrom(
        'yourgmail@gmail.com',
        'My Website'
    );

    // Where you want to receive the OTP
    $mail->addAddress(
        'yourgmail@gmail.com'
    );

    $mail->isHTML(true);

    $mail->Subject = 'Your OTP Code';

    $mail->Body = "
        <h2>OTP Verification</h2>

        <p>Your OTP code is:</p>

        <h1>$resetOtp</h1>

        <p>This code will expire in 10 minutes.</p>
    ";

    $mail->AltBody = "Your OTP code is: $resetOtp.";

    $mail->send();

    echo "OTP sent successfully.";

} catch (Exception $e) {

    echo "Message could not be sent.";

    echo "<br>";
    echo $mail->ErrorInfo;
}