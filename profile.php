<?php
require_once 'config.php';
require_once 'includes/db.php';

$slug = $_GET['slug'] ?? basename($_SERVER['REQUEST_URI']);

if (empty($slug)) {
    header('Location: ' . BASE_URL);
    exit;
}

// Fetch profile by slug (only public profiles)
$stmt = $db->prepare("SELECT p.*, u.name as user_name FROM profiles p 
                      JOIN users u ON p.user_id = u.id 
                      WHERE p.slug = :slug AND p.is_public = 1");
$stmt->execute(['slug' => $slug]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    header('HTTP/1.0 404 Not Found');
    echo "Profile not found";
    exit;
}

// Update analytics
$stmt = $db->prepare("UPDATE analytics SET total_views = total_views + 1 WHERE profile_id = :id");
$stmt->execute(['id' => $profile['id']]);

$contact = json_decode($profile['contact_info'], true);
$social = json_decode($profile['social_links'], true);

$theme_colors = [
    1 => ['primary' => '#667eea', 'secondary' => '#764ba2'],
    2 => ['primary' => '#1a1a1a', 'secondary' => '#2d2d2d'],
    3 => ['primary' => '#f5f7fa', 'secondary' => '#c3cfe2']
];
$colors = $theme_colors[$profile['theme_id']] ?? $theme_colors[1];

// Use uploaded image or default
$profile_image = !empty($profile['image']) && file_exists('' . $profile['image'])
    ? BASE_URL . '/' . $profile['image']
    : BASE_URL . '/assets/default-profile.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($profile['profile_name']); ?> - Digital Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, <?php echo $colors['primary']; ?> 0%, <?php echo $colors['secondary']; ?> 100%);
    min-height: 100vh;
    padding: 20px 0;
}
.profile-container { max-width: 600px; margin: 0 auto; }
.profile-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}
.profile-image {
    width: 150px; height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 5px solid <?php echo $colors['primary']; ?>;
    margin: 0 auto 20px;
    display: block;
}
.social-links a {
    display: inline-block; width: 50px; height: 50px;
    line-height: 50px; text-align: center;
    border-radius: 50%;
    background: <?php echo $colors['primary']; ?>;
    color: white; margin: 5px; font-size: 20px;
    transition: transform 0.2s;
}
.social-links a:hover { transform: scale(1.1); }
.contact-item {
    padding: 10px; margin: 5px 0;
    background: #f8f9fa; border-radius: 10px;
}
</style>
</head>
<body>
<div class="profile-container">
    <div class="profile-card text-center">
        <img src="<?php echo $profile_image; ?>" alt="Profile" class="profile-image">
        <h1 class="mb-2"><?php echo htmlspecialchars($profile['profile_name']); ?></h1>
        <?php if ($profile['title']): ?>
            <p class="text-muted mb-4"><?php echo htmlspecialchars($profile['title']); ?></p>
        <?php endif; ?>
        <?php if ($profile['about']): ?>
            <div class="mb-4">
                <h5><i class="fas fa-info-circle me-2"></i>About</h5>
                <p><?php echo nl2br(htmlspecialchars($profile['about'])); ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty(array_filter($contact))): ?>
            <div class="mb-4 text-start">
                <h5><i class="fas fa-address-book me-2"></i>Contact</h5>
                <?php foreach ($contact as $key => $value):
                    if (!empty($value)):
                        $icon = match($key) {
                            'phone' => 'fa-phone',
                            'email' => 'fa-envelope',
                            'website' => 'fa-globe',
                            'address' => 'fa-map-marker-alt',
                            default => 'fa-info-circle'
                        };
                        ?>
                        <div class="contact-item">
                            <i class="fas <?php echo $icon; ?> me-2"></i>
                            <?php echo ($key === 'website') ? '<a href="'.htmlspecialchars($value).'" target="_blank">Website</a>' : htmlspecialchars($value); ?>
                        </div>
                    <?php endif;
                endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty(array_filter($social))): ?>
            <div class="text-center social-links">
                <?php foreach ($social as $key => $link):
                    if (!empty($link)):
                        $icon = match($key) {
                            'linkedin' => 'fab fa-linkedin-in',
                            'twitter' => 'fab fa-twitter',
                            'instagram' => 'fab fa-instagram',
                            'facebook' => 'fab fa-facebook-f',
                            default => 'fas fa-globe'
                        };
                        ?>
                        <a href="<?php echo htmlspecialchars($link); ?>" target="_blank"><i class="<?php echo $icon; ?>"></i></a>
                    <?php endif;
                endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <small class="text-muted">Powered by <?php echo SITE_NAME; ?></small>
        </div>
    </div>
</div>

<script>
if ('ontouchstart' in window || navigator.maxTouchPoints) {
    fetch('<?php echo BASE_URL; ?>/api/track-tap.php?profile_id=<?php echo $profile['id']; ?>');
}
</script>
</body>
</html>
