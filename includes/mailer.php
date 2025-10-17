<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendEmail($to, $subject, $body, $altBody = '') {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody ?: strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email error: " . $mail->ErrorInfo);
        return false;
    }
}

function sendOTP($email, $otp, $name) {
    $subject = "Verify Your Email - " . SITE_NAME;
    $body = "
        <h2>Welcome to " . SITE_NAME . "!</h2>
        <p>Hello $name,</p>
        <p>Your OTP verification code is: <strong style='font-size: 24px; color: #007bff;'>$otp</strong></p>
        <p>This code will expire in 10 minutes.</p>
        <p>If you didn't request this, please ignore this email.</p>
        <br>
        <p>Best regards,<br>The " . SITE_NAME . " Team</p>
    ";
    
    return sendEmail($email, $subject, $body);
}

function sendOrderNotification($email, $orderType, $orderId) {
    $subject = "NFC Order Confirmation - #" . $orderId;
    $body = "
        <h2>Order Confirmation</h2>
        <p>Your NFC card order (#$orderId) has been received successfully.</p>
        <p>Order Type: <strong>" . ucfirst($orderType) . "</strong></p>
        <p>We will review your order and get back to you soon.</p>
        <br>
        <p>Best regards,<br>The " . SITE_NAME . " Team</p>
    ";
    
    return sendEmail($email, $subject, $body);
}

function sendOrderStatusUpdate($email, $orderId, $status, $notes = '') {
    $subject = "Order Status Update - #" . $orderId;
    $body = "
        <h2>Order Status Update</h2>
        <p>Your NFC card order (#$orderId) status has been updated to: <strong>" . ucfirst($status) . "</strong></p>
        " . ($notes ? "<p>Admin Notes: $notes</p>" : "") . "
        <br>
        <p>Best regards,<br>The " . SITE_NAME . " Team</p>
    ";
    
    return sendEmail($email, $subject, $body);
}
?>
