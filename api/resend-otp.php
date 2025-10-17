<?php
require_once '../config.php';
require_once '../includes/db.php';
require_once '../includes/mailer.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$otp = sprintf("%06d", mt_rand(1, 999999));

$stmt = $db->prepare("INSERT INTO otp_verifications (user_id, otp, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
$stmt->execute([$user_id, $otp]);

if (sendOTP($user['email'], $otp, $user['name'])) {
    echo json_encode(['success' => true, 'message' => 'OTP resent successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send OTP']);
}
?>
