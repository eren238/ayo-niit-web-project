<?php
/**
 * School Hostel - SMTP Email Configuration
 *
 * To send real OTP emails via Gmail SMTP:
 * 1. Set SMTP_EMAIL to your real Gmail address (e.g. oludotunayomiposi5@gmail.com).
 * 2. Generate a 16-character Google App Password (NOT your regular password):
 *    - Visit https://myaccount.google.com/security
 *    - Ensure 2-Step Verification is turned ON
 *    - Search for "App Passwords"
 *    - Create an App Password for "School Hostel"
 * 3. Put that 16-character password in SMTP_PASSWORD below.
 */

if (!defined('SMTP_EMAIL')) {
    define('SMTP_EMAIL', 'yourgmail@gmail.com');
}

if (!defined('SMTP_PASSWORD')) {
    define('SMTP_PASSWORD', 'xxxx xxxx xxxx xxxx');
}

if (!defined('SMTP_SENDER_NAME')) {
    define('SMTP_SENDER_NAME', 'School Hostel Portal');
}
?>
