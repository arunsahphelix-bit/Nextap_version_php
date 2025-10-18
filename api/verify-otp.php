<?php
require_once '../config.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get OTP from POST
$otp = trim($_POST['otp'] ?? '');
if (empty($otp)) {
    echo json_encode(['success' => false, 'message' => 'OTP is required']);
    exit;
}

// Get user email
$stmt = $db->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}
$email = $user['email'];

// Fetch latest OTP
$stmt = $db->prepare("
    SELECT otp_code 
    FROM otp_verifications 
    WHERE email = ? 
      AND expires_at > NOW() 
    ORDER BY created_at DESC 
    LIMIT 1
");
$stmt->execute([$email]);
$otpRow = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$otpRow) {
    echo json_encode(['success' => false, 'message' => 'OTP expired or not found']);
    exit;
}

// Compare OTP
if ($otpRow['otp_code'] !== $otp) {
    echo json_encode(['success' => false, 'message' => 'Invalid OTP']);
    exit;
}

// Mark user as verified
$updateUser = $db->prepare("UPDATE users SET verification_status = 'verified' WHERE id = ?");
$updateUser->execute([$_SESSION['user_id']]);

// Mark OTP as used
$updateOtp = $db->prepare("DELETE FROM otp_verifications WHERE email = ?");
$updateOtp->execute([$email]);

echo json_encode(['success' => true, 'message' => 'Email verified successfully']);
