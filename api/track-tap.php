<?php
require_once '../config.php';
require_once '../includes/db.php';

$profile_id = intval($_GET['profile_id'] ?? 0);

if ($profile_id > 0) {
    $stmt = $db->prepare("UPDATE analytics SET total_taps = total_taps + 1 WHERE profile_id = ?");
    $stmt->execute([$profile_id]);
}
?>
