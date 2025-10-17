<?php
// Quick conversion script for remaining API files
$files = [
    'api/verify-otp.php',
    'api/resend-otp.php',
    'api/create-profile.php',
    'api/update-profile.php',
    'api/create-order.php',
    'api/track-tap.php',
    'api/create-website.php',
    'api/update-website-status.php',
    'api/admin-update-order.php',
    'pages/dashboard.php',
    'admin/dashboard.php',
    'admin/orders.php',
    'create-admin.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    
    $content = file_get_contents($file);
    
    // Convert bind_param patterns
    $content = preg_replace(
        '/\$stmt->bind_param\(["\']([^"\']+)["\']\s*,\s*([^)]+)\);\s*\$stmt->execute\(\);/',
        '$stmt->execute([$2]);',
        $content
    );
    
    // Convert get_result()->fetch_assoc()
    $content = str_replace('$stmt->get_result()->fetch_assoc()', '$stmt->fetch(PDO::FETCH_ASSOC)', $content);
    
    // Convert get_result()->num_rows
    $content = preg_replace(
        '/\$stmt->get_result\(\)->num_rows\s*([><=!]+)\s*0/',
        '$stmt->rowCount() $1 0',
        $content
    );
    
    // Convert $db->insert_id
    $content = str_replace('$db->insert_id', '$db->lastInsertId()', $content);
    
    // Convert $db->query to work with PDO
    $content = preg_replace(
        '/\$stmt\s*=\s*\$db->query\(([^)]+)\);/',
        '$stmt = $db->query($1);',
        $content
    );
    
    file_put_contents($file, $content);
    echo "Converted: $file\n";
}

echo "\nConversion complete! Please manually review the files.\n";
?>
