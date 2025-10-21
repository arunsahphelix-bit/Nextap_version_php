<?php
require_once '../config.php';
require_once '../includes/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

$template_id = intval($data['template_id'] ?? 0);
$website_title = trim($data['website_title'] ?? '');
$slug = strtolower(trim($data['slug'] ?? ''));
$custom_domain = trim($data['custom_domain'] ?? '');

if (empty($website_title) || empty($slug) || $template_id === 0) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit;
}

if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
    echo json_encode(['success' => false, 'message' => 'Invalid slug format']);
    exit;
}

// Check if slug already exists
$stmt = $db->prepare("SELECT id FROM websites WHERE slug = ?");
$stmt->execute([$slug]);
if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => false, 'message' => 'This slug is already taken']);
    exit;
}

// Prepare content JSON
$content_json = json_encode([
    'hero_heading' => $data['hero_heading'] ?? '',
    'hero_description' => $data['hero_description'] ?? '',
    'about_content' => $data['about_content'] ?? '',
    'services' => $data['services'] ?? '',
    'contact_email' => $data['contact_email'] ?? ''
]);

try {
    $stmt = $db->prepare("INSERT INTO websites 
        (user_id, template_id, slug, website_title, content_json, custom_domain, status, created_at) 
        VALUES (:user_id, :template_id, :slug, :website_title, :content_json, :custom_domain, 'draft', NOW())");

    $stmt->execute([
        ':user_id' => $user_id,
        ':template_id' => $template_id,
        ':slug' => $slug,
        ':website_title' => $website_title,
        ':content_json' => $content_json,
        ':custom_domain' => $custom_domain
    ]);

    echo json_encode(['success' => true, 'website_id' => $db->lastInsertId()]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
