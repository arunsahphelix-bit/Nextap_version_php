<?php
require_once '../config.php';
if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/dashboard.php');
    exit;
}

$page_title = "Login";
include '../includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Welcome Back</h2>
                    
                    <form id="loginForm">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </form>
                    
                    <p class="text-center mt-3">
                        Don't have an account? <a href="register.php">Sign up here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    makeAjaxRequest('<?php echo BASE_URL; ?>/api/login.php', 'POST', data, function(err, response) {
        if (err) {
            showAlert('Login failed. Please try again.', 'danger');
            return;
        }
        
        if (response.success) {
            if (response.needs_verification) {
                showAlert('Please verify your email first.', 'warning');
                setTimeout(() => {
                    window.location.href = 'verify-otp.php';
                }, 1500);
            } else {
                showAlert('Login successful!', 'success');
                setTimeout(() => {
                    window.location.href = 'dashboard.php';
                }, 1000);
            }
        } else {
            showAlert(response.message || 'Invalid credentials', 'danger');
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
