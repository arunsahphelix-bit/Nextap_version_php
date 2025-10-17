<?php
require_once '../config.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT * FROM websites WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$websites = $stmt->get_result();

$page_title = "My Websites";
include '../includes/header.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>My Websites</h1>
        <a href="create-website.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create Website
        </a>
    </div>
    
    <?php if ($websites->num_rows === 0): ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i>
            You haven't created any websites yet. <a href="create-website.php">Create your first website</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php while ($website = $websites->fetch_assoc()): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5><?php echo htmlspecialchars($website['website_title']); ?></h5>
                        <p class="text-muted">
                            <i class="fas fa-link me-1"></i>
                            <?php echo BASE_URL; ?>/sites/<?php echo $website['slug']; ?>
                        </p>
                        <?php if ($website['custom_domain']): ?>
                            <p class="text-muted">
                                <i class="fas fa-globe me-1"></i>
                                <?php echo htmlspecialchars($website['custom_domain']); ?>
                            </p>
                        <?php endif; ?>
                        
                        <span class="badge bg-<?php echo $website['status'] === 'published' ? 'success' : 'secondary'; ?>">
                            <?php echo ucfirst($website['status']); ?>
                        </span>
                        
                        <div class="mt-3 d-flex gap-2">
                            <a href="<?php echo BASE_URL; ?>/sites/<?php echo $website['slug']; ?>" 
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <button onclick="publishWebsite(<?php echo $website['id']; ?>, '<?php echo $website['status']; ?>')" 
                                    class="btn btn-sm btn-success">
                                <i class="fas fa-upload me-1"></i>
                                <?php echo $website['status'] === 'published' ? 'Unpublish' : 'Publish'; ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function publishWebsite(id, currentStatus) {
    const newStatus = currentStatus === 'published' ? 'draft' : 'published';
    
    makeAjaxRequest('<?php echo BASE_URL; ?>/api/update-website-status.php', 'POST', 
        { website_id: id, status: newStatus }, 
        function(err, response) {
            if (err || !response.success) {
                showAlert('Failed to update status', 'danger');
                return;
            }
            
            showAlert('Status updated!', 'success');
            setTimeout(() => location.reload(), 1000);
        }
    );
}
</script>

<?php include '../includes/footer.php'; ?>
