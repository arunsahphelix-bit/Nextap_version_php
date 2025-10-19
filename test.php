<?php
require_once 'includes/db.php';
if($db) {
    echo "Database connected successfully!";
} else {
    echo "Database connection failed!";
}
?>
