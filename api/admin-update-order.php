<?php
require_once '../config.php';
require_once '../includes/db.php';
require_once '../includes/mailer.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$order_id = intval($data['order_id'] ?? 0);
$status = $data['status'] ?? '';
$notes = trim($data['notes'] ?? '');

if (!in_array($status, ['approved', 'rejected', 'processing', 'completed'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

$stmt = $db->prepare("SELECT u.email FROM nfc_orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}

$order = $result->fetch_assoc();

$stmt = $db->prepare("UPDATE nfc_orders SET status = ?, admin_notes = ? WHERE id = ?");
$stmt->bind_param("ssi", $status, $notes, $order_id);

if ($stmt->execute()) {
    sendOrderStatusUpdate($order['email'], $order_id, $status, $notes);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update order']);
}
?>
