<?php
require_once 'config.php';
require_once 'includes/db.php';

$page_title = "Home";
include 'includes/header.php';
?>

<div class="container">
    <div class="row align-items-center min-vh-75">
        <div class="col-lg-6">
            <h1 class="display-4 fw-bold mb-4">Build Your Digital Presence</h1>
            <p class="lead mb-4">Create professional NFC profiles, order custom NFC cards, and build stunning websites - all in one platform.</p>
            <div class="d-flex gap-3">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo BASE_URL; ?>/pages/dashboard.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/pages/register.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Get Started
                    </a>
                    <a href="<?php echo BASE_URL; ?>/pages/login.php" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-6 text-center">
            <i class="fas fa-mobile-screen-button" style="font-size: 200px; color: var(--bs-primary);"></i>
        </div>
    </div>

    <div class="row mt-5 mb-5">
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <i class="fas fa-id-card fa-3x text-primary mb-3"></i>
                    <h4>Digital Profiles</h4>
                    <p class="text-muted">Create stunning NFC-enabled digital business profiles with analytics tracking</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <i class="fas fa-credit-card fa-3x text-primary mb-3"></i>
                    <h4>Custom NFC Cards</h4>
                    <p class="text-muted">Order personalized NFC cards with your profile or custom design</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <i class="fas fa-globe fa-3x text-primary mb-3"></i>
                    <h4>Website Builder</h4>
                    <p class="text-muted">Build professional websites with 7 pre-designed responsive templates</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
