<?php
/**
 * School Hostel - SMTP Email Configuration
 *
 * Configured with official sender credentials to deliver OTP codes.
 */

if (!defined('SMTP_EMAIL')) {
    define('SMTP_EMAIL', 'oludotunayomiposi5@gmail.com');
}

if (!defined('SMTP_PASSWORD')) {
    define('SMTP_PASSWORD', 'rjamnflkmhbynnnc');
}

if (!defined('SMTP_SENDER_NAME')) {
    define('SMTP_SENDER_NAME', 'School Hostel Portal');
}
?>
