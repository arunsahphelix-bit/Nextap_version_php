<?php
require_once '../config.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

// Check login
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

// Validate required fields
if (empty($profile_name) || empty($slug)) {
    echo json_encode(['success' => false, 'message' => 'Profile name and slug are required']);
    exit;
}

// Validate slug format
if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
    echo json_encode(['success' => false, 'message' => 'Invalid slug format']);
    exit;
}

// Check if slug already exists
$stmt = $db->prepare("SELECT id FROM profiles WHERE slug = ?");
$stmt->execute([$slug]);
if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => false, 'message' => 'This slug is already taken']);
    exit;
}

// Prepare contact info and socials as JSON
$contact_info = json_encode([
    'phone' => $_POST['phone'] ?? '',
    'email' => $_POST['contact_email'] ?? '',
    'website' => $_POST['website'] ?? '',
    'address' => $_POST['address'] ?? ''
], JSON_UNESCAPED_UNICODE);

$social_links = json_encode([
    'linkedin' => $_POST['linkedin'] ?? '',
    'twitter' => $_POST['twitter'] ?? '',
    'instagram' => $_POST['instagram'] ?? '',
    'facebook' => $_POST['facebook'] ?? ''
], JSON_UNESCAPED_UNICODE);

// Handle file uploads
require_once '../includes/upload-helper.php';

$image_path = null;
if (!empty($_FILES['image']['name'])) {
    $result = validateAndUploadFile($_FILES['image'], '../uploads/profile_images', ['jpg', 'jpeg', 'png']);
    if ($result['success']) {
        $image_path = $result['filepath'];
    }
}

$logo_path = null;
if (!empty($_FILES['logo']['name'])) {
    $result = validateAndUploadFile($_FILES['logo'], '../uploads/profile_images', ['jpg', 'jpeg', 'png']);
    if ($result['success']) {
        $logo_path = $result['filepath'];
    }
}

// Insert profile into database
try {
    $stmt = $db->prepare("INSERT INTO profiles 
        (user_id, profile_name, slug, title, about, contact_info, social_links, image, logo, theme_id, is_public) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $user_id,
        $profile_name,
        $slug,
        $title,
        $about,
        $contact_info,
        $social_links,
        $image_path,
        $logo_path,
        $theme_id,
        $is_public
    ]);

    $profile_id = $db->lastInsertId();

    // Create analytics row
    $stmt = $db->prepare("INSERT INTO analytics (profile_id) VALUES (?)");
    $stmt->execute([$profile_id]);

    echo json_encode(['success' => true, 'message' => 'Profile created successfully', 'profile_id' => $profile_id]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
    