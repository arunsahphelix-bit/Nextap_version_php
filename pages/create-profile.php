<?php
require_once '../config.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

$page_title = "Create Profile";
include '../includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Create New Profile</h3>
                </div>
                <div class="card-body">
                    <form id="profileForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Profile Name *</label>
                            <input type="text" class="form-control" name="profile_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Profile URL Slug *</label>
                            <div class="input-group">
                                <span class="input-group-text"><?php echo BASE_URL; ?>/profile/</span>
                                <input type="text" class="form-control" name="slug" pattern="[a-z0-9-]+" required>
                            </div>
                            <small class="text-muted">Only lowercase letters, numbers, and hyphens</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Title/Designation</label>
                            <input type="text" class="form-control" name="title" placeholder="e.g., CEO, Designer">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">About</label>
                            <textarea class="form-control" name="about" rows="4"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Profile Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Logo</label>
                            <input type="file" class="form-control" name="logo" accept="image/*">
                        </div>
                        
                        <h5 class="mt-4 mb-3">Contact Information</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" name="phone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="contact_email">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Website</label>
                            <input type="url" class="form-control" name="website">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="2"></textarea>
                        </div>
                        
                        <h5 class="mt-4 mb-3">Social Links</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">LinkedIn</label>
                                <input type="url" class="form-control" name="linkedin">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Twitter</label>
                                <input type="url" class="form-control" name="twitter">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Instagram</label>
                                <input type="url" class="form-control" name="instagram">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Facebook</label>
                                <input type="url" class="form-control" name="facebook">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Select Theme</label>
                            <div class="theme-selector">
                                <div class="theme-option active" data-theme="1">
                                    <strong>Modern Blue</strong>
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 50px; border-radius: 5px; margin-top: 10px;"></div>
                                </div>
                                <div class="theme-option" data-theme="2">
                                    <strong>Professional Dark</strong>
                                    <div style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); height: 50px; border-radius: 5px; margin-top: 10px;"></div>
                                </div>
                                <div class="theme-option" data-theme="3">
                                    <strong>Elegant Light</strong>
                                    <div style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); height: 50px; border-radius: 5px; margin-top: 10px;"></div>
                                </div>
                            </div>
                            <input type="hidden" name="theme_id" value="1">
                        </div>
                        
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="is_public" value="1" checked>
                            <label class="form-check-label">Make profile public</label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Create Profile
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
// ===== THEME SELECTOR =====
document.querySelectorAll('.theme-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('.theme-option').forEach(opt => opt.classList.remove('active'));
        this.classList.add('active');
        document.querySelector('input[name="theme_id"]').value = this.dataset.theme;
    });
});

// ===== AUTO SLUG GENERATION & VALIDATION =====
function generateSlug(str) {
    return str
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')  // replace invalid chars
        .replace(/-+/g, '-')           // collapse multiple hyphens
        .replace(/^-|-$/g, '');        // trim hyphens
}

function isValidSlug(slug) {
    return /^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(slug);
}

const nameInput = document.querySelector('input[name="profile_name"]');
const slugInput = document.querySelector('input[name="slug"]');

if (nameInput && slugInput) {
    let lastAuto = '';
    const slugNote = document.createElement('small');
    slugNote.className = 'text-danger';
    slugInput.parentElement.appendChild(slugNote);

    nameInput.addEventListener('input', () => {
        const autoSlug = generateSlug(nameInput.value);
        if (slugInput.value === '' || slugInput.value === lastAuto) {
            slugInput.value = autoSlug;
            lastAuto = autoSlug;
        }
        if (!isValidSlug(slugInput.value)) {
            slugNote.textContent = 'Invalid format — use lowercase, numbers, and hyphens only.';
        } else {
            slugNote.textContent = '';
        }
    });

    slugInput.addEventListener('input', () => {
        if (!isValidSlug(slugInput.value)) {
            slugNote.textContent = 'Invalid format — use lowercase, numbers, and hyphens only.';
        } else {
            slugNote.textContent = '';
        }
    });
}

// ===== FORM SUBMISSION (AJAX) =====
document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const slug = formData.get('slug');
    if (!isValidSlug(slug)) {
        alert('Invalid slug format. Use lowercase letters, numbers, and hyphens only.');
        return;
    }
    
    fetch('<?php echo BASE_URL; ?>/api/create-profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Profile created successfully!');
            setTimeout(() => {
                window.location.href = 'profiles.php';
            }, 1200);
        } else {
            alert(data.message || 'Failed to create profile.');
        }
    })
    .catch(() => {
        alert('An unexpected error occurred.');
    });
});
</script>

<?php include '../includes/footer.php'; ?>
