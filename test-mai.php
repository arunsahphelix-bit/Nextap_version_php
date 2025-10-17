
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Make sure PHPMailer is installed via Composer

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your-real-email@gmail.com';
    $mail->Password = 'your-16-character-app-password';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('noreply@nexttap.in', 'NextTap Builder');
    $mail->addAddress('your-email@gmail.com'); // send to yourself

    $mail->isHTML(true);
    $mail->Subject = 'Test OTP Email';
    $mail->Body = 'This is a test email from NextTap Builder.';

    $mail->send();
    echo "Email sent successfully!";
} catch (Exception $e) {
    echo "Email could not be sent. Error: {$mail->ErrorInfo}";
}
?>
