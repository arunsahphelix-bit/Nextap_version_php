<?php
require_once '../config.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $_SESSION['user_id'];
$template_id = intval($data['template_id'] ?? 0);
$website_title = trim($data['website_title'] ?? '');
$slug = strtolower(trim($data['slug'] ?? ''));
$custom_domain = trim($data['custom_domain'] ?? '');

if (empty($website_title) || empty($slug) || $template_id === 0) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
    echo json_encode(['success' => false, 'message' => 'Invalid slug format']);
    exit;
}

$stmt = $db->prepare("SELECT id FROM websites WHERE slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'This slug is already taken']);
    exit;
}

$content_json = json_encode([
    'hero_heading' => $data['hero_heading'] ?? '',
    'hero_description' => $data['hero_description'] ?? '',
    'about_content' => $data['about_content'] ?? '',
    'services' => $data['services'] ?? '',
    'contact_email' => $data['contact_email'] ?? ''
]);

$stmt = $db->prepare("INSERT INTO websites (user_id, template_id, slug, website_title, content_json, custom_domain, status) 
                      VALUES (?, ?, ?, ?, ?, ?, 'draft')");
$stmt->bind_param("iissss", $user_id, $template_id, $slug, $website_title, $content_json, $custom_domain);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'website_id' => $db->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create website']);
}
?>
