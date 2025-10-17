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

$user_id = $_SESSION['user_id'];
$profile_id = intval($_POST['profile_id'] ?? 0);

$stmt = $db->prepare("SELECT id FROM profiles WHERE id = ? AND user_id = ?");
$stmt->execute([$profile_id, $user_id]);
if ($stmt->rowCount() === 0) {
    echo json_encode(['success' => false, 'message' => 'Profile not found']);
    exit;
}

$profile_name = trim($_POST['profile_name'] ?? '');
$title = trim($_POST['title'] ?? '');
$about = trim($_POST['about'] ?? '');
$theme_id = intval($_POST['theme_id'] ?? 1);

if (empty($profile_name)) {
    echo json_encode(['success' => false, 'message' => 'Profile name is required']);
    exit;
}

$contact_info = json_encode([
    'phone' => $_POST['phone'] ?? '',
    'email' => $_POST['contact_email'] ?? '',
    'website' => $_POST['website'] ?? '',
    'address' => $_POST['address'] ?? ''
]);

$social_links = json_encode([
    'linkedin' => $_POST['linkedin'] ?? '',
    'twitter' => $_POST['twitter'] ?? '',
    'instagram' => $_POST['instagram'] ?? '',
    'facebook' => $_POST['facebook'] ?? ''
]);

require_once '../includes/upload-helper.php';

$image_path = null;
if (isset($_FILES['image'])) {
    $result = validateAndUploadFile($_FILES['image'], 'uploads/profile_images', ['jpg', 'jpeg', 'png']);
    if ($result['success']) {
        $image_path = $result['filepath'];
    }
}

if ($image_path) {
    $stmt = $db->prepare("UPDATE profiles SET profile_name = ?, title = ?, about = ?, contact_info = ?, social_links = ?, image = ?, theme_id = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssssssiis", $profile_name, $title, $about, $contact_info, $social_links, $image_path, $theme_id, $profile_id, $user_id);
} else {
    $stmt = $db->prepare("UPDATE profiles SET profile_name = ?, title = ?, about = ?, contact_info = ?, social_links = ?, theme_id = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sssssiis", $profile_name, $title, $about, $contact_info, $social_links, $theme_id, $profile_id, $user_id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
}
?>
