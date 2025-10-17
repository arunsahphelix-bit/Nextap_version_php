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

$profile_name = trim($_POST['profile_name'] ?? '');
$slug = strtolower(trim($_POST['slug'] ?? ''));
$title = trim($_POST['title'] ?? '');
$about = trim($_POST['about'] ?? '');
$theme_id = intval($_POST['theme_id'] ?? 1);
$is_public = isset($_POST['is_public']) ? 1 : 0;

if (empty($profile_name) || empty($slug)) {
    echo json_encode(['success' => false, 'message' => 'Profile name and slug are required']);
    exit;
}

if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
    echo json_encode(['success' => false, 'message' => 'Invalid slug format']);
    exit;
}

$stmt = $db->prepare("SELECT id FROM profiles WHERE slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'This slug is already taken']);
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

$logo_path = null;
if (isset($_FILES['logo'])) {
    $result = validateAndUploadFile($_FILES['logo'], 'uploads/profile_images', ['jpg', 'jpeg', 'png']);
    if ($result['success']) {
        $logo_path = $result['filepath'];
    }
}

$stmt = $db->prepare("INSERT INTO profiles (user_id, profile_name, slug, title, about, contact_info, social_links, image, logo, theme_id, is_public) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssssssii", $user_id, $profile_name, $slug, $title, $about, $contact_info, $social_links, $image_path, $logo_path, $theme_id, $is_public);

if ($stmt->execute()) {
    $profile_id = $db->insert_id;
    
    $stmt = $db->prepare("INSERT INTO analytics (profile_id) VALUES (?)");
    $stmt->bind_param("i", $profile_id);
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Profile created successfully', 'profile_id' => $profile_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create profile']);
}
?>
