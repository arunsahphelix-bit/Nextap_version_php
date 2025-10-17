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
} else {
    $requirements = trim($_POST['requirements'] ?? '');
    
    if (isset($_FILES['uploaded_design']) && $_FILES['uploaded_design']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['uploaded_design']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'])) {
            $filename = 'design_' . uniqid() . '.' . $ext;
            $uploaded_design = 'uploads/designs/' . $filename;
            move_uploaded_file($_FILES['uploaded_design']['tmp_name'], '../' . $uploaded_design);
        }
    }
}

$business_proof = null;
if (isset($_FILES['business_proof']) && $_FILES['business_proof']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['business_proof']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'])) {
        $filename = 'proof_' . uniqid() . '.' . $ext;
        $business_proof = 'uploads/proofs/' . $filename;
        move_uploaded_file($_FILES['business_proof']['tmp_name'], '../' . $business_proof);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Business proof is required']);
    exit;
}

$stmt = $db->prepare("INSERT INTO nfc_orders (user_id, type, selected_profile_id, uploaded_design, requirements, business_proof) 
                      VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isisss", $user_id, $type, $selected_profile_id, $uploaded_design, $requirements, $business_proof);

if ($stmt->execute()) {
    $order_id = $db->insert_id;
    
    sendOrderNotification($_SESSION['email'], $type, $order_id);
    
    echo json_encode(['success' => true, 'message' => 'Order placed successfully', 'order_id' => $order_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to place order']);
}
?>
