<?php
require_once '../config.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch user verification status
$stmt = $db->prepare("SELECT verification_status FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user['verification_status'] === 'unverified') {
    header('Location: ' . BASE_URL . '/pages/verify-otp.php');
    exit;
}

// ✅ Fetch counts
$stmt = $db->prepare("SELECT COUNT(*) as count FROM profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $db->prepare("SELECT COUNT(*) as count FROM websites WHERE user_id = ?");
$stmt->execute([$user_id]);
$website_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $db->prepare("SELECT COUNT(*) as count FROM nfc_orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$order_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// ✅ Fetch recent orders
$stmt = $db->prepare("SELECT nfc_orders.*, profiles.profile_name 
                      FROM nfc_orders 
                      LEFT JOIN profiles ON nfc_orders.selected_profile_id = profiles.id 
                      WHERE nfc_orders.user_id = ? 
                      ORDER BY nfc_orders.created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC); // ✅ fetchAll returns an array

$page_title = "Dashboard";
include '../includes/header.php';
?>

<div class="container">
    <h1 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
    
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card analytics-card">
                <div class="card-body text-center">
                    <i class="fas fa-id-card fa-3x mb-3"></i>
                    <h3><?php echo $profile_count; ?></h3>
                    <p class="mb-0">Digital Profiles</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card analytics-card">
                <div class="card-body text-center">
                    <i class="fas fa-globe fa-3x mb-3"></i>
                    <h3><?php echo $website_count; ?></h3>
                    <p class="mb-0">Websites</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card analytics-card">
                <div class="card-body text-center">
                    <i class="fas fa-credit-card fa-3x mb-3"></i>
                    <h3><?php echo $order_count; ?></h3>
                    <p class="mb-0">NFC Orders</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <a href="create-profile.php" class="text-decoration-none">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-plus-circle fa-3x text-primary mb-3"></i>
                        <h5>Create Profile</h5>
                        <p class="text-muted">Build a new digital profile</p>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-4 mb-3">
            <a href="create-order.php" class="text-decoration-none">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-shopping-cart fa-3x text-primary mb-3"></i>
                        <h5>Order NFC Card</h5>
                        <p class="text-muted">Get your custom NFC card</p>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-4 mb-3">
            <a href="create-website.php" class="text-decoration-none">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-laptop-code fa-3x text-primary mb-3"></i>
                        <h5>Build Website</h5>
                        <p class="text-muted">Create with templates</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
    <?php if (!empty($recent_orders)): ?>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Recent Orders</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo ucfirst($order['type']); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $order['status'] === 'approved' ? 'success' : 
                                         ($order['status'] === 'rejected' ? 'danger' : 'warning'); 
                                ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
