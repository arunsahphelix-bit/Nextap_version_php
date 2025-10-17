<?php
require_once '../config.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

$stmt = $db->query("SELECT o.*, u.name as user_name, u.email, p.profile_name 
                   FROM nfc_orders o 
                   JOIN users u ON o.user_id = u.id 
                   LEFT JOIN profiles p ON o.selected_profile_id = p.id 
                   ORDER BY o.created_at DESC");
$orders = $stmt;

$page_title = "Manage Orders";
include '../includes/header.php';
?>

<div class="container">
    <h1 class="mb-4">Manage Orders</h1>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($order['user_name']); ?>
                                <br><small><?php echo htmlspecialchars($order['email']); ?></small>
                            </td>
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
                            <td>
                                <?php if ($order['business_proof']): ?>
                                    <a href="<?php echo BASE_URL . '/' . $order['business_proof']; ?>" 
                                       target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-file me-1"></i>Proof
                                    </a>
                                <?php endif; ?>
                                <?php if ($order['status'] === 'pending'): ?>
                                    <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'approved')" 
                                            class="btn btn-sm btn-success">
                                        <i class="fas fa-check me-1"></i>Approve
                                    </button>
                                    <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'rejected')" 
                                            class="btn btn-sm btn-danger">
                                        <i class="fas fa-times me-1"></i>Reject
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function updateOrderStatus(orderId, status) {
    const notes = prompt('Enter admin notes (optional):');
    
    makeAjaxRequest('<?php echo BASE_URL; ?>/api/admin-update-order.php', 'POST', 
        { order_id: orderId, status: status, notes: notes || '' },
        function(err, response) {
            if (err || !response.success) {
                showAlert('Failed to update order', 'danger');
                return;
            }
            
            showAlert('Order updated successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        }
    );
}
</script>

<?php include '../includes/footer.php'; ?>
