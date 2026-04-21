<?php
session_start();
include_once 'includes/db.php';

/* Mark ALL as read */
mysqli_query($conn,
    "UPDATE issues SET is_read = 1 WHERE is_read = 0"
);

echo "ok";
