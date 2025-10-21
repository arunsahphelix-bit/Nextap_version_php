<?php
function validateAndUploadFile($file, $upload_dir, $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'], $max_size = 5242880) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }

    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File size exceeds the maximum allowed (' . ($max_size / 1048576) . 'MB)'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_extensions)) {
        return ['success' => false, 'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowed_extensions)];
    }

    // Validate MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowed_mimes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'pdf' => 'application/pdf'
    ];

    if (!isset($allowed_mimes[$ext]) || $mime !== $allowed_mimes[$ext]) {
        return ['success' => false, 'message' => 'File type mismatch'];
    }

    // Ensure upload directory exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $filepath = rtrim($upload_dir, '/') . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'message' => 'Failed to save file to ' . $filepath];
    }

    // Return path relative to main project
    $relative_path = str_replace(__DIR__ . '/../', '', realpath($filepath));

    return ['success' => true, 'filepath' => $relative_path];
}
?>
