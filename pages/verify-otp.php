<?php
require_once '../config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

require_once '../includes/db.php';

// ✅ Using PDO now
$stmt = $db->prepare("SELECT verification_status FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && $user['verification_status'] === 'verified') {
    header('Location: ' . BASE_URL . '/pages/dashboard.php');
    exit;
}

$page_title = "Verify Email";
include '../includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body p-5 text-center">
                    <i class="fas fa-envelope-circle-check fa-4x text-primary mb-3"></i>
                    <h2 class="mb-3">Verify Your Email NexTap team</h2>
                    <p class="text-muted mb-4">We've sent a 6-digit OTP to your email</p>
                    
                    <form id="otpForm">
                        <div class="mb-3">
                            <input type="text" class="form-control text-center fs-4" 
                                   name="otp" placeholder="Enter OTP" maxlength="6" 
                                   pattern="[0-9]{6}" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-check me-2"></i>Verify
                        </button>
                    </form>
                    
                    <button onclick="resendOTP()" class="btn btn-link">
                        Didn't receive OTP? Resend
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('otpForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    makeAjaxRequest('<?php echo BASE_URL; ?>/api/verify-otp.php', 'POST', data, function(err, response) {
        if (err) {
            showAlert('Verification failed. Please try again.', 'danger');
            return;
        }
        
        if (response.success) {
            showAlert('Email verified successfully!', 'success');
            setTimeout(() => {
                window.location.href = 'dashboard.php';
            }, 1000);
        } else {
            showAlert(response.message || 'Invalid OTP', 'danger');
        }
    });
});

function resendOTP() {
    makeAjaxRequest('<?php echo BASE_URL; ?>/api/resend-otp.php', 'POST', {}, function(err, response) {
        if (err) {
            showAlert('Failed to resend OTP', 'danger');
            return;
        }
        
        if (response.success) {
            showAlert('OTP resent successfully!', 'success');
        } else {
            showAlert(response.message || 'Failed to resend OTP', 'danger');
        }
    });
}
</script>

<?php include '../includes/footer.php'; ?>
