<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    die("User not logged in");
}

$user_id     = $_SESSION['user_id'];
$category_id = $_POST['category_id'];
$title       = mysqli_real_escape_string($conn, $_POST['title']);
$description = mysqli_real_escape_string($conn, $_POST['description']);
$location    = mysqli_real_escape_string($conn, $_POST['location']);
$priority    = $_POST['priority'];
$department  = $_POST['department'] ?? null;

/* ✅ category_name fetch karo */
$catQ = mysqli_query($conn,
    "SELECT category_name FROM categories WHERE id='$category_id'"
);
$catRow   = mysqli_fetch_assoc($catQ);
$category = $catRow['category_name'] ?? '';

$status = 'pending'; // lowercase

$query = "INSERT INTO issues 
(user_id, category_id, category, title, description, location, priority, department, status)
VALUES 
('$user_id', '$category_id', '$category', '$title', '$description', '$location', '$priority', '$department', '$status')";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Insert failed: " . mysqli_error($conn));
}

header("Location: dashboard.php?success=1");
exit;
?>
