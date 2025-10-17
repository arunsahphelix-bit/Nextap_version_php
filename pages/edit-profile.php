<?php
require_once '../config.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

$profile_id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT * FROM profiles WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $profile_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ' . BASE_URL . '/pages/profiles.php');
    exit;
}

$profile = $result->fetch_assoc();
$contact = json_decode($profile['contact_info'], true);
$social = json_decode($profile['social_links'], true);

$page_title = "Edit Profile";
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
                        <input type="hidden" name="profile_id" value="<?php echo $profile['id']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Profile Name *</label>
                            <input type="text" class="form-control" name="profile_name" 
                                   value="<?php echo htmlspecialchars($profile['profile_name']); ?>" required>
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
                            <?php if ($profile['image']): ?>
                                <div class="mb-2">
                                    <img src="<?php echo BASE_URL . '/' . $profile['image']; ?>" 
                                         class="img-thumbnail" style="max-width: 150px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        
                        <h5 class="mt-4 mb-3">Contact Information</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" name="phone" 
                                       value="<?php echo htmlspecialchars($contact['phone'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="contact_email" 
                                       value="<?php echo htmlspecialchars($contact['email'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Website</label>
                            <input type="url" class="form-control" name="website" 
                                   value="<?php echo htmlspecialchars($contact['website'] ?? ''); ?>">
                        </div>
                        
                        <h5 class="mt-4 mb-3">Social Links</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">LinkedIn</label>
                                <input type="url" class="form-control" name="linkedin" 
                                       value="<?php echo htmlspecialchars($social['linkedin'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Twitter</label>
                                <input type="url" class="form-control" name="twitter" 
                                       value="<?php echo htmlspecialchars($social['twitter'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Select Theme</label>
                            <div class="theme-selector">
                                <div class="theme-option <?php echo $profile['theme_id'] == 1 ? 'active' : ''; ?>" data-theme="1">
                                    <strong>Modern Blue</strong>
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 50px; border-radius: 5px; margin-top: 10px;"></div>
                                </div>
                                <div class="theme-option <?php echo $profile['theme_id'] == 2 ? 'active' : ''; ?>" data-theme="2">
                                    <strong>Professional Dark</strong>
                                    <div style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); height: 50px; border-radius: 5px; margin-top: 10px;"></div>
                                </div>
                                <div class="theme-option <?php echo $profile['theme_id'] == 3 ? 'active' : ''; ?>" data-theme="3">
                                    <strong>Elegant Light</strong>
                                    <div style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); height: 50px; border-radius: 5px; margin-top: 10px;"></div>
                                </div>
                            </div>
                            <input type="hidden" name="theme_id" value="<?php echo $profile['theme_id']; ?>">
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Update Profile
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
document.querySelectorAll('.theme-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('.theme-option').forEach(opt => opt.classList.remove('active'));
        this.classList.add('active');
        document.querySelector('input[name="theme_id"]').value = this.dataset.theme;
    });
});

document.getElementById('editProfileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?php echo BASE_URL; ?>/api/update-profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Profile updated successfully!', 'success');
            setTimeout(() => {
                window.location.href = 'profiles.php';
            }, 1500);
        } else {
            showAlert(data.message || 'Failed to update profile', 'danger');
        }
    })
    .catch(error => {
        showAlert('An error occurred', 'danger');
    });
});
</script>

<?php include '../includes/footer.php'; ?>
