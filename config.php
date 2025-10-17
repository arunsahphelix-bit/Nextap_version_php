<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'nexttap_builder');

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('SMTP_FROM', 'noreply@nexttap.in');
define('SMTP_FROM_NAME', 'NextTap Builder');

define('BASE_URL', 'http://localhost:5000');
define('SITE_NAME', 'NextTap Builder');

define('UPLOAD_MAX_SIZE', 5242880);
define('ALLOWED_EXTENSIONS', ['png', 'jpg', 'jpeg', 'pdf']);

date_default_timezone_set('Asia/Kolkata');
session_start();
?>
