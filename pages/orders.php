<?php
require_once '../config.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch recent NFC orders with profile names
$stmt = $db->prepare("
    SELECT nfc_orders.*, profiles.profile_name 
    FROM nfc_orders 
    LEFT JOIN profiles ON nfc_orders.selected_profile_id = profiles.id 
    WHERE nfc_orders.user_id = ? 
    ORDER BY nfc_orders.created_at DESC
    LIMIT 5
");
$stmt->execute([$user_id]);
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "My Orders";
include '../includes/header.php';
?>

<div class="container">
    <h1 class="mb-4">My NFC Orders</h1>

    <?php if (empty($recent_orders)): ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i>
            You haven't placed any NFC orders yet. <a href="create-order.php">Place your first order</a>
        </div>
    <?php else: ?>
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
                                <th>Profile</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['profile_name'] ?? 'N/A'); ?></td>
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
