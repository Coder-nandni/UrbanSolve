<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

include("db.php");

if(!isset($_GET['id'])){
    header("Location: location.php");
    exit;
}

$id = (int)$_GET['id'];

mysqli_query($conn, "DELETE FROM locations WHERE id=$id");

header("Location: location.php");
exit;
?>
