<?php
require_once '../config.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$website_id = intval($data['website_id'] ?? 0);
$status = $data['status'] ?? '';

if (!in_array($status, ['draft', 'published'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

$stmt = $db->prepare("UPDATE websites SET status = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("sii", $status, $website_id, $_SESSION['user_id']);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}
?>
