<?php
session_start();
session_destroy();
header('Location: ' . (defined('BASE_URL') ? BASE_URL : '../') . '/pages/login.php');
exit;
?>
