<?php
require_once '../config.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

$page_title = "Create Website";
include '../includes/header.php';

$templates = [
    1 => ['name' => 'Business Pro', 'description' => 'Professional business website'],
    2 => ['name' => 'Portfolio Modern', 'description' => 'Creative portfolio showcase'],
    3 => ['name' => 'Agency Bold', 'description' => 'Digital agency template'],
    4 => ['name' => 'Startup Minimal', 'description' => 'Clean startup landing page'],
    5 => ['name' => 'Restaurant Fresh', 'description' => 'Restaurant and cafe website'],
    6 => ['name' => 'Personal Brand', 'description' => 'Personal branding site'],
    7 => ['name' => 'E-Commerce', 'description' => 'Online store template']
];
?>

<div class="container">
    <h1 class="mb-4">Create Your Website</h1>
    
    <div class="row">
        <?php foreach ($templates as $id => $template): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="template-preview" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="text-center text-white p-5">
                        <h3><?php echo $template['name']; ?></h3>
                        <p><?php echo $template['description']; ?></p>
                    </div>
                </div>
                <div class="card-body">
                    <button onclick="selectTemplate(<?php echo $id; ?>)" class="btn btn-primary w-100">
                        Use This Template
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="modal fade" id="websiteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Website Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="websiteForm">
                    <input type="hidden" name="template_id" id="templateId">
                    
                    <div class="mb-3">
                        <label class="form-label">Website Title *</label>
                        <input type="text" class="form-control" name="website_title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">URL Slug *</label>
                        <div class="input-group">
                            <span class="input-group-text"><?php echo BASE_URL; ?>/sites/</span>
                            <input type="text" class="form-control" name="slug" pattern="[a-z0-9-]+" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Custom Domain (optional)</label>
                        <input type="text" class="form-control" name="custom_domain" placeholder="www.yourdomain.com">
                    </div>
                    
                    <h5>Content Sections</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Hero Heading</label>
                        <input type="text" class="form-control" name="hero_heading">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Hero Description</label>
                        <textarea class="form-control" name="hero_description" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">About Section</label>
                        <textarea class="form-control" name="about_content" rows="4"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Services/Features (one per line)</label>
                        <textarea class="form-control" name="services" rows="4"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Contact Email</label>
                        <input type="email" class="form-control" name="contact_email">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Create Website</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let websiteModal;

document.addEventListener('DOMContentLoaded', function() {
    websiteModal = new bootstrap.Modal(document.getElementById('websiteModal'));
});

function selectTemplate(templateId) {
    document.getElementById('templateId').value = templateId;
    websiteModal.show();
}

document.getElementById('websiteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    makeAjaxRequest('<?php echo BASE_URL; ?>/api/create-website.php', 'POST', data, function(err, response) {
        if (err || !response.success) {
            showAlert(response?.message || 'Failed to create website', 'danger');
            return;
        }
        
        showAlert('Website created successfully!', 'success');
        setTimeout(() => {
            window.location.href = 'websites.php';
        }, 1500);
    });
});
</script>

<?php include '../includes/footer.php'; ?>
