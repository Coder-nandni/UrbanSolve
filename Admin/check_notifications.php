<?php
include_once 'includes/db.php';

$q = mysqli_query($conn, "SELECT COUNT(*) as c FROM issues WHERE is_read = 0");

$row = mysqli_fetch_assoc($q);

echo (int)$row['c'];