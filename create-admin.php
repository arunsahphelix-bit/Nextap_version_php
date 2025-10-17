<?php
require_once 'config.php';
require_once 'includes/db.php';

define('SETUP_TOKEN', 'CHANGE_ME_' . bin2hex(random_bytes(16)));

$provided_token = $_GET['token'] ?? '';
if ($provided_token !== SETUP_TOKEN) {
    http_response_code(404);
    die("Not Found");
}

$stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE is_admin = 1");
$admin_exists = $stmt->fetch_assoc()['count'] > 0;

if ($admin_exists) {
    die("Admin user already exists. Delete this file for security.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $name = trim($_POST['name'] ?? '');
    
    if (empty($email) || empty($password) || empty($name)) {
        $error = "All fields are required";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters";
    } else {
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Admin user already exists";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (name, email, password, company_name, verification_status, is_admin) VALUES (?, ?, ?, 'NextTap', 'verified', 1)");
            $stmt->bind_param("sss", $name, $email, $hashed);
            
            if ($stmt->execute()) {
                if (!unlink(__FILE__)) {
                    die("SECURITY CRITICAL: Admin created but failed to delete create-admin.php. Delete it manually NOW!");
                }
                $success = "Admin user created successfully! You can now login. This file has been deleted.";
                header('refresh:2;url=pages/login.php');
            } else {
                $error = "Failed to create admin user";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Admin User - NextTap Builder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3>Create Admin User</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php else: ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password (min 8 characters)</label>
                                <input type="password" class="form-control" name="password" minlength="8" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Create Admin</button>
                        </form>
                        <p class="mt-3 text-muted"><small>Note: Delete this file after creating your admin user.</small></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
