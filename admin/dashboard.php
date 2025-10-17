<?php
require_once '../config.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

$stmt = $db->query("SELECT COUNT(*) as count FROM users");
$total_users = $stmt->fetch_assoc()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM profiles");
$total_profiles = $stmt->fetch_assoc()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM nfc_orders WHERE status = 'pending'");
$pending_orders = $stmt->fetch_assoc()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM websites");
$total_websites = $stmt->fetch_assoc()['count'];

$stmt = $db->query("SELECT u.name, u.email, u.company_name, u.verification_status, u.created_at 
                   FROM users u ORDER BY u.created_at DESC LIMIT 10");
$recent_users = $stmt;

$page_title = "Admin Dashboard";
include '../includes/header.php';
?>

<div class="container">
    <h1 class="mb-4">Admin Dashboard</h1>
    
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card analytics-card">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h3><?php echo $total_users; ?></h3>
                    <p class="mb-0">Total Users</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card analytics-card">
                <div class="card-body text-center">
                    <i class="fas fa-id-card fa-3x mb-3"></i>
                    <h3><?php echo $total_profiles; ?></h3>
                    <p class="mb-0">Profiles</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card analytics-card">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                    <h3><?php echo $pending_orders; ?></h3>
                    <p class="mb-0">Pending Orders</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card analytics-card">
                <div class="card-body text-center">
                    <i class="fas fa-globe fa-3x mb-3"></i>
                    <h3><?php echo $total_websites; ?></h3>
                    <p class="mb-0">Websites</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Pending Orders</h5>
                    <a href="orders.php" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <a href="orders.php" class="btn btn-primary w-100">
                        <i class="fas fa-tasks me-2"></i>Manage Orders (<?php echo $pending_orders; ?> pending)
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Users</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = $recent_users->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $user['verification_status'] === 'verified' ? 'success' : 'warning'; ?>">
                                            <?php echo $user['verification_status']; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
