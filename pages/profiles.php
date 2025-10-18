<?php
require_once '../config.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ PDO version
$stmt = $db->prepare("
    SELECT p.*, 
           COALESCE(a.total_views, 0) AS total_views, 
           COALESCE(a.total_taps, 0) AS total_taps
    FROM profiles p
    LEFT JOIN analytics a ON p.id = a.profile_id
    WHERE p.user_id = ?
    ORDER BY p.created_at DESC
");
$stmt->execute([$user_id]);
$profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "My Profiles";
include '../includes/header.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>My Profiles</h1>
        <a href="create-profile.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create New Profile
        </a>
    </div>
    
    <?php if (empty($profiles)): ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i>
            You haven't created any profiles yet. <a href="create-profile.php">Create your first profile</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($profiles as $profile): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card profile-card h-100">
                    <div class="card-body">
                        <?php if ($profile['image']): ?>
                            <img src="<?php echo BASE_URL . '/' . $profile['image']; ?>" 
                                 alt="Profile" class="profile-image mx-auto d-block mb-3">
                        <?php else: ?>
                            <div class="text-center mb-3">
                                <i class="fas fa-user-circle fa-5x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        
                        <h5 class="text-center"><?php echo htmlspecialchars($profile['profile_name']); ?></h5>
                        <?php if (!empty($profile['title'])): ?>
                            <p class="text-center text-muted"><?php echo htmlspecialchars($profile['title']); ?></p>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-around my-3">
                            <div class="text-center">
                                <strong><?php echo $profile['total_views']; ?></strong>
                                <br><small class="text-muted">Views</small>
                            </div>
                            <div class="text-center">
                                <strong><?php echo $profile['total_taps']; ?></strong>
                                <br><small class="text-muted">Taps</small>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="<?php echo BASE_URL; ?>/profile/<?php echo $profile['slug']; ?>" 
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <a href="edit-profile.php?id=<?php echo $profile['id']; ?>" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <button onclick="generateQR(<?php echo $profile['id']; ?>, '<?php echo $profile['slug']; ?>')" 
                                    class="btn btn-sm btn-success">
                                <i class="fas fa-qrcode me-1"></i>QR Code
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Profile QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="qrContent"></div>
        </div>
    </div>
</div>

<script>
function generateQR(profileId, slug) {
    const url = '<?php echo BASE_URL; ?>/profile/' + slug;
    const qrContent = document.getElementById('qrContent');
    qrContent.innerHTML = `
        <div class="qr-code-container mx-auto">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(url)}" 
                 alt="QR Code">
        </div>
        <p class="mt-3">Scan to visit profile</p>
        <button class="btn btn-primary" onclick="copyToClipboard('${url}')">
            <i class="fas fa-copy me-2"></i>Copy Link
        </button>
    `;
    new bootstrap.Modal(document.getElementById('qrModal')).show();
}
</script>

<?php include '../includes/footer.php'; ?>
