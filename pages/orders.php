<?php
require_once '../config.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT nfc_orders.*, profiles.profile_name 
                      FROM nfc_orders 
                      LEFT JOIN profiles ON nfc_orders.selected_profile_id = profiles.id 
                      WHERE nfc_orders.user_id = ? 
                      ORDER BY nfc_orders.created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();

$page_title = "My Orders";
include '../includes/header.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>My Orders</h1>
        <a href="create-order.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Order
        </a>
    </div>
    
    <?php if ($orders->num_rows === 0): ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i>
            You haven't placed any orders yet. <a href="create-order.php">Place your first order</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php while ($order = $orders->fetch_assoc()): ?>
            <div class="col-12 mb-3">
                <div class="card order-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <h5 class="mb-0">#<?php echo $order['id']; ?></h5>
                                <small class="text-muted"><?php echo ucfirst($order['type']); ?></small>
                            </div>
                            <div class="col-md-4">
                                <?php if ($order['type'] === 'profile' && $order['profile_name']): ?>
                                    <strong>Profile:</strong> <?php echo htmlspecialchars($order['profile_name']); ?>
                                <?php else: ?>
                                    <strong>Custom Design</strong>
                                    <?php if ($order['uploaded_design']): ?>
                                        <br><a href="<?php echo BASE_URL . '/' . $order['uploaded_design']; ?>" target="_blank">View Design</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-2">
                                <span class="badge bg-<?php 
                                    echo $order['status'] === 'approved' ? 'success' : 
                                         ($order['status'] === 'rejected' ? 'danger' : 
                                         ($order['status'] === 'completed' ? 'primary' : 'warning')); 
                                ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted">
                                    <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                </small>
                            </div>
                            <div class="col-md-2 text-end">
                                <?php if ($order['business_proof']): ?>
                                    <a href="<?php echo BASE_URL . '/' . $order['business_proof']; ?>" 
                                       target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file me-1"></i>Proof
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($order['admin_notes']): ?>
                            <div class="mt-3 pt-3 border-top">
                                <strong>Admin Notes:</strong> <?php echo htmlspecialchars($order['admin_notes']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
