<?php
require_once '../config.php';
require_once '../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$profile_id = intval($data['id'] ?? 0);

if ($profile_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid profile ID']);
    exit;
}

// Verify ownership
$stmt = $db->prepare("SELECT * FROM profiles WHERE id = ? AND user_id = ?");
$stmt->execute([$profile_id, $_SESSION['user_id']]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    echo json_encode(['success' => false, 'message' => 'Profile not found']);
    exit;
}

// Delete images if exist
if (!empty($profile['image']) && file_exists('../' . $profile['image'])) {
    unlink('../' . $profile['image']);
}
if (!empty($profile['logo']) && file_exists('../' . $profile['logo'])) {
    unlink('../' . $profile['logo']);
}

// Delete profile
$stmt = $db->prepare("DELETE FROM profiles WHERE id = ? AND user_id = ?");
$deleted = $stmt->execute([$profile_id, $_SESSION['user_id']]);

if ($deleted) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete profile']);
}
?>
