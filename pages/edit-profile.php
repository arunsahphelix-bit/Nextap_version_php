<?php
require_once '../config.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

$page_title = "Edit Profile";

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$profile_id = intval($_GET['id']);

// Fetch existing profile
$stmt = $db->prepare("SELECT * FROM profiles WHERE id = ? AND user_id = ?");
$stmt->execute([$profile_id, $_SESSION['user_id']]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    die("Profile not found or you don’t have permission to edit this.");
}

include '../includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Edit Profile</h3>
                </div>
                <div class="card-body">
                    <form id="editProfileForm" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($profile['id']); ?>">

                        <div class="mb-3">
                            <label class="form-label">Profile Name *</label>
                            <input type="text" class="form-control" name="profile_name"
                                value="<?php echo htmlspecialchars($profile['profile_name']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Profile URL Slug *</label>
                            <div class="input-group">
                                <span class="input-group-text"><?php echo BASE_URL; ?>/profile/</span>
                                <input type="text" class="form-control" name="slug"
                                    value="<?php echo htmlspecialchars($profile['slug']); ?>"
                                    pattern="[a-z0-9-]+" required>
                            </div>
                            <small class="text-muted">Only lowercase letters, numbers, and hyphens</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Title/Designation</label>
                            <input type="text" class="form-control" name="title"
                                value="<?php echo htmlspecialchars($profile['title']); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">About</label>
                            <textarea class="form-control" name="about" rows="4"><?php echo htmlspecialchars($profile['about']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Profile Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <?php
                            $profile_image = !empty($profile['image']) && file_exists('../' . $profile['image'])
                                ? BASE_URL . '/' . $profile['image']
                                : BASE_URL . '/assets/default-profile.png';
                            ?>
                            <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Image" class="img-fluid mt-2" width="120">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Logo</label>
                            <input type="file" class="form-control" name="logo" accept="image/*">
                            <?php
                            $logo_image = !empty($profile['logo']) && file_exists('../' . $profile['logo'])
                                ? BASE_URL . '/' . $profile['logo']
                                : BASE_URL . '/assets/default-profile.png';
                            ?>
                            <img src="<?php echo htmlspecialchars($logo_image); ?>" alt="Logo" class="img-fluid mt-2" width="120">
                        </div>

                        <?php
                        $contact_info = json_decode($profile['contact_info'] ?? '{}', true);
                        $social_links = json_decode($profile['social_links'] ?? '{}', true);
                        ?>

                        <h5 class="mt-4 mb-3">Contact Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($contact_info['phone'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="contact_email" value="<?php echo htmlspecialchars($contact_info['email'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Website</label>
                            <input type="url" class="form-control" name="website" value="<?php echo htmlspecialchars($contact_info['website'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($contact_info['address'] ?? ''); ?></textarea>
                        </div>

                        <h5 class="mt-4 mb-3">Social Links</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">LinkedIn</label>
                                <input type="url" class="form-control" name="linkedin" value="<?php echo htmlspecialchars($social_links['linkedin'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Twitter</label>
                                <input type="url" class="form-control" name="twitter" value="<?php echo htmlspecialchars($social_links['twitter'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Instagram</label>
                                <input type="url" class="form-control" name="instagram" value="<?php echo htmlspecialchars($social_links['instagram'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Facebook</label>
                                <input type="url" class="form-control" name="facebook" value="<?php echo htmlspecialchars($social_links['facebook'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="is_public" value="1" <?php echo $profile['is_public'] ? 'checked' : ''; ?>>
                            <label class="form-check-label">Make profile public</label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                            <a href="profiles.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('editProfileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('<?php echo BASE_URL; ?>/api/update-profile.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Profile updated successfully!');
            setTimeout(() => window.location.href = 'profiles.php', 1200);
        } else {
            alert(data.message || 'Failed to update profile.');
        }
    })
    .catch(() => alert('An unexpected error occurred.'));
});
</script>

<?php include '../includes/footer.php'; ?>
