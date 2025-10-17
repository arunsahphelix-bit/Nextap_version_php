<?php
require_once 'config.php';
require_once 'includes/db.php';

$slug = $_GET['slug'] ?? basename($_SERVER['REQUEST_URI']);

$stmt = $db->prepare("SELECT * FROM websites WHERE slug = ? AND status = 'published'");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('HTTP/1.0 404 Not Found');
    echo "Website not found";
    exit;
}

$website = $result->fetch_assoc();
$content = json_decode($website['content_json'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($website['website_title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#"><?php echo htmlspecialchars($website['website_title']); ?></a>
        </div>
    </nav>
    
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h1 class="display-4"><?php echo htmlspecialchars($content['hero_heading'] ?? 'Welcome'); ?></h1>
            <p class="lead"><?php echo htmlspecialchars($content['hero_description'] ?? ''); ?></p>
        </div>
    </section>
    
    <?php if (!empty($content['about_content'])): ?>
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">About</h2>
            <p><?php echo nl2br(htmlspecialchars($content['about_content'])); ?></p>
        </div>
    </section>
    <?php endif; ?>
    
    <?php if (!empty($content['services'])): ?>
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">Services</h2>
            <div class="row">
                <?php 
                $services = explode("\n", $content['services']);
                foreach ($services as $service): 
                    if (trim($service)):
                ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <p><?php echo htmlspecialchars(trim($service)); ?></p>
                        </div>
                    </div>
                </div>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <?php if (!empty($content['contact_email'])): ?>
    <section class="py-5">
        <div class="container text-center">
            <h2 class="mb-4">Contact Us</h2>
            <p><i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($content['contact_email']); ?></p>
        </div>
    </section>
    <?php endif; ?>
    
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($website['website_title']); ?></p>
        </div>
    </footer>
</body>
</html>
