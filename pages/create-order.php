<?php
require_once '../config.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT id, profile_name, slug FROM profiles WHERE user_id = ? AND is_public = 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$profiles = $stmt->get_result();

$page_title = "Order NFC Card";
include '../includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Order Custom NFC Card</h3>
                </div>
                <div class="card-body">
                    <form id="orderForm" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="form-label">Select Order Type *</label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card order-type-card" onclick="selectOrderType('profile')">
                                        <div class="card-body text-center">
                                            <i class="fas fa-id-card fa-3x text-primary mb-3"></i>
                                            <h5>Use Existing Profile</h5>
                                            <p class="text-muted mb-0">Link NFC card to your digital profile</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card order-type-card" onclick="selectOrderType('custom')">
                                        <div class="card-body text-center">
                                            <i class="fas fa-paint-brush fa-3x text-primary mb-3"></i>
                                            <h5>Custom Design</h5>
                                            <p class="text-muted mb-0">Upload your own card design</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="type" id="orderType" required>
                        </div>
                        
                        <div id="profileSection" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Select Profile *</label>
                                <select class="form-select" name="selected_profile_id">
                                    <option value="">Choose a profile...</option>
                                    <?php while ($profile = $profiles->fetch_assoc()): ?>
                                        <option value="<?php echo $profile['id']; ?>">
                                            <?php echo htmlspecialchars($profile['profile_name']); ?> 
                                            (<?php echo BASE_URL; ?>/profile/<?php echo $profile['slug']; ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div id="customSection" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Upload Design *</label>
                                <input type="file" class="form-control" name="uploaded_design" accept=".png,.jpg,.jpeg,.pdf">
                                <small class="text-muted">Accepted formats: PNG, JPG, PDF (Max 5MB)</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Design Requirements</label>
                                <textarea class="form-control" name="requirements" rows="4" 
                                          placeholder="Describe your requirements, dimensions, colors, etc."></textarea>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Business Proof Document *</label>
                            <input type="file" class="form-control" name="business_proof" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">Upload company registration or business proof (PDF/Image)</small>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Your order will be reviewed by our team. You'll receive an email notification once approved.
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-shopping-cart me-2"></i>Place Order
                            </button>
                            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectOrderType(type) {
    document.getElementById('orderType').value = type;
    
    document.querySelectorAll('.order-type-card').forEach(card => {
        card.classList.remove('border-primary');
    });
    event.currentTarget.classList.add('border-primary');
    
    if (type === 'profile') {
        document.getElementById('profileSection').style.display = 'block';
        document.getElementById('customSection').style.display = 'none';
        document.querySelector('input[name="uploaded_design"]').removeAttribute('required');
    } else {
        document.getElementById('profileSection').style.display = 'none';
        document.getElementById('customSection').style.display = 'block';
        document.querySelector('input[name="uploaded_design"]').setAttribute('required', 'required');
    }
}

document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?php echo BASE_URL; ?>/api/create-order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Order placed successfully! We will review and contact you soon.', 'success');
            setTimeout(() => {
                window.location.href = 'orders.php';
            }, 2000);
        } else {
            showAlert(data.message || 'Failed to place order', 'danger');
        }
    })
    .catch(error => {
        showAlert('An error occurred', 'danger');
    });
});
</script>

<style>
.order-type-card {
    cursor: pointer;
    transition: all 0.3s;
    border: 2px solid transparent;
}
.order-type-card:hover {
    border-color: var(--bs-primary);
    transform: translateY(-5px);
}
</style>

<?php include '../includes/footer.php'; ?>
