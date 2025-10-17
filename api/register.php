<?php
require_once '../config.php';
require_once '../includes/db.php';
require_once '../includes/mailer.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$company_name = trim($data['company_name'] ?? '');
$password = $data['password'] ?? '';

if (empty($name) || empty($email) || empty($company_name) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit;
}

$stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already registered']);
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$otp = sprintf("%06d", mt_rand(1, 999999));

$stmt = $db->prepare("INSERT INTO users (name, email, password, company_name, otp_code) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $email, $hashed_password, $company_name, $otp);

if ($stmt->execute()) {
    $user_id = $db->insert_id;
    
    $stmt = $db->prepare("INSERT INTO otp_verifications (user_id, otp, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
    $stmt->bind_param("is", $user_id, $otp);
    $stmt->execute();
    
    sendOTP($email, $otp, $name);
    
    $_SESSION['user_id'] = $user_id;
    $_SESSION['email'] = $email;
    $_SESSION['name'] = $name;
    
    echo json_encode(['success' => true, 'message' => 'Registration successful']);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed']);
}
?>
