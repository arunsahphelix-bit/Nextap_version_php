<?php
require_once '../config.php';
require_once '../includes/db.php';
require_once '../includes/mailer.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$user_id = $_SESSION['user_id'];
$type = $_POST['type'] ?? '';

if (!in_array($type, ['profile', 'custom'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid order type']);
    exit;
}

$selected_profile_id = null;
$uploaded_design = null;
$requirements = null;

if ($type === 'profile') {
    $selected_profile_id = intval($_POST['selected_profile_id'] ?? 0);
    if ($selected_profile_id === 0) {
        echo json_encode(['success' => false, 'message' => 'Please select a profile']);
        exit;
    }
    
    $stmt = $db->prepare("SELECT id FROM profiles WHERE id = ? AND user_id = ?");
    $stmt->execute([$selected_profile_id, $user_id]);
    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid profile selected']);
        exit;
    }
} else {
    $requirements = trim($_POST['requirements'] ?? '');
    
    if (isset($_FILES['uploaded_design'])) {
        require_once '../includes/upload-helper.php';
        $result = validateAndUploadFile($_FILES['uploaded_design'], 'uploads/designs');
        if ($result['success']) {
            $uploaded_design = $result['filepath'];
        } else {
            echo json_encode(['success' => false, 'message' => $result['message']]);
            exit;
        }
    }
}

$business_proof = null;
if (isset($_FILES['business_proof'])) {
    require_once '../includes/upload-helper.php';
    $result = validateAndUploadFile($_FILES['business_proof'], 'uploads/proofs');
    if ($result['success']) {
        $business_proof = $result['filepath'];
    } else {
        echo json_encode(['success' => false, 'message' => $result['message']]);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Business proof is required']);
    exit;
}

$stmt = $db->prepare("INSERT INTO nfc_orders (user_id, type, selected_profile_id, uploaded_design, requirements, business_proof) 
                      VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isisss", $user_id, $type, $selected_profile_id, $uploaded_design, $requirements, $business_proof);

if ($stmt->execute()) {
    $order_id = $db->lastInsertId();
    
    sendOrderNotification($_SESSION['email'], $type, $order_id);
    
    echo json_encode(['success' => true, 'message' => 'Order placed successfully', 'order_id' => $order_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to place order']);
}
?>
