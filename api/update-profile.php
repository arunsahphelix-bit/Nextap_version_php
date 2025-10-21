<?php
require_once '../config.php';
require_once '../includes/db.php';

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
$profile_id = intval($_POST['id'] ?? 0);

// Check if profile exists and belongs to user
$stmt = $db->prepare("SELECT * FROM profiles WHERE id = ? AND user_id = ?");
$stmt->execute([$profile_id, $user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    echo json_encode(['success' => false, 'message' => 'Profile not found']);
    exit;
}

// Collect updated data
$profile_name = trim($_POST['profile_name'] ?? '');
$title = trim($_POST['title'] ?? '');
$about = trim($_POST['about'] ?? '');
$theme_id = intval($_POST['theme_id'] ?? 1);
$is_public = isset($_POST['is_public']) ? 1 : 0;

if (empty($profile_name)) {
    echo json_encode(['success' => false, 'message' => 'Profile name is required']);
    exit;
}

// Contact info
$contact_info = json_encode([
    'phone' => $_POST['phone'] ?? '',
    'email' => $_POST['contact_email'] ?? '',
    'website' => $_POST['website'] ?? '',
    'address' => $_POST['address'] ?? ''
], JSON_UNESCAPED_UNICODE);

// Social links
$social_links = json_encode([
    'linkedin' => $_POST['linkedin'] ?? '',
    'twitter' => $_POST['twitter'] ?? '',
    'instagram' => $_POST['instagram'] ?? '',
    'facebook' => $_POST['facebook'] ?? ''
], JSON_UNESCAPED_UNICODE);

// Handle file uploads
require_once '../includes/upload-helper.php';

// Upload profile image
$image_path = $profile['image'];
if (!empty($_FILES['image']['name'])) {
    $result = validateAndUploadFile($_FILES['image'], '../uploads/profile_images', ['jpg','jpeg','png']);
    if ($result['success']) {
        $image_path = 'uploads/profile_images/' . basename($result['filepath']);
    }
}

// Upload logo
$logo_path = $profile['logo'];
if (!empty($_FILES['logo']['name'])) {
    $result = validateAndUploadFile($_FILES['logo'], '../uploads/profile_images', ['jpg','jpeg','png']);
    if ($result['success']) {
        $logo_path = 'uploads/profile_images/' . basename($result['filepath']);
    }
}

// Update profile
$stmt = $db->prepare("
    UPDATE profiles 
    SET profile_name = ?, title = ?, about = ?, contact_info = ?, social_links = ?, image = ?, logo = ?, theme_id = ?, is_public = ? 
    WHERE id = ? AND user_id = ?
");
$updated = $stmt->execute([
    $profile_name,
    $title,
    $about,
    $contact_info,
    $social_links,
    $image_path,
    $logo_path,
    $theme_id,
    $is_public,
    $profile_id,
    $user_id
]);

if ($updated) {
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
}
?>
