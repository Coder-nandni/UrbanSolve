<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// User not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Auto logout after 10 minutes (600 seconds)
$inactive_time = 600;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $inactive_time) {
    session_unset();
    session_destroy();
    header("Location: login.php?error=Session expired. Please login again.");
    exit();
}

$_SESSION['LAST_ACTIVITY'] = time();
?>
