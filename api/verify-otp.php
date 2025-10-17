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

$data = json_decode(file_get_contents('php://input'), true);
$otp = trim($data['otp'] ?? '');

if (empty($otp)) {
    echo json_encode(['success' => false, 'message' => 'OTP is required']);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT otp FROM otp_verifications WHERE user_id = ? AND verified = 0 AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$user_id]);
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'OTP expired or not found']);
    exit;
}

$row = $result->fetch_assoc();

if ($row['otp'] !== $otp) {
    echo json_encode(['success' => false, 'message' => 'Invalid OTP']);
    exit;
}

$stmt = $db->prepare("UPDATE users SET verification_status = 'verified' WHERE id = ?");
$stmt->execute([$user_id]);

$stmt = $db->prepare("UPDATE otp_verifications SET verified = 1 WHERE user_id = ?");
$stmt->execute([$user_id]);

echo json_encode(['success' => true, 'message' => 'Email verified successfully']);
?>
