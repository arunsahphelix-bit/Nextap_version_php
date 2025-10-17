<?php
require_once '../config.php';
if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/dashboard.php');
    exit;
}

$page_title = "Sign Up";
include '../includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Create Your Account</h2>
                    
                    <form id="registerForm">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Company Email</label>
                            <input type="email" class="form-control" name="email" required>
                            <small class="text-muted">We'll send an OTP to verify your email</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-control" name="company_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" minlength="6" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password" minlength="6" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-user-plus me-2"></i>Sign Up
                        </button>
                    </form>
                    
                    <p class="text-center mt-3">
                        Already have an account? <a href="login.php">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    if (data.password !== data.confirm_password) {
        showAlert('Passwords do not match!', 'danger');
        return;
    }
    
    makeAjaxRequest('<?php echo BASE_URL; ?>/api/register.php', 'POST', data, function(err, response) {
        if (err) {
            showAlert('Registration failed. Please try again.', 'danger');
            return;
        }
        
        if (response.success) {
            showAlert('Registration successful! Redirecting to OTP verification...', 'success');
            setTimeout(() => {
                window.location.href = 'verify-otp.php';
            }, 1500);
        } else {
            showAlert(response.message || 'Registration failed', 'danger');
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
